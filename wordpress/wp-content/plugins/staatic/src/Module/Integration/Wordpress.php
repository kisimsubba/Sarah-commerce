<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Integration;

use Staatic\WordPress\Module\ModuleInterface;

final class Wordpress implements ModuleInterface
{
    /**
     * @var mixed[]
     */
    private $emojiFiles = [];

    /**
     * @var string|null
     */
    private $wpDebugLogPath;

    /**
     * @return void
     */
    public function hooks()
    {
        \add_action('wp_loaded', [$this, 'setupIntegration']);
    }

    /**
     * @return void
     */
    public function setupIntegration()
    {
        $this->emojiFiles = $this->detectEmojiFiles();
        $this->wpDebugLogPath = $this->detectWpDebugLogPath();
        if (\count($this->emojiFiles) > 0) {
            \add_filter('staatic_additional_paths', [$this, 'overrideAdditionalPaths']);
        }
        if ($this->wpDebugLogPath) {
            \add_filter('staatic_additional_paths_exclude_paths', [$this, 'overrideAdditionalPathsExcludePaths']);
        }
    }

    private function detectEmojiFiles() : array
    {
        $candidatePaths = [
            \ABSPATH . \WPINC . '/js/wp-emoji-release.min.js',
            \ABSPATH . \WPINC . '/js/wp-emoji.js',
            \ABSPATH . \WPINC . '/js/twemoji.js'
        ];

        return \array_filter($candidatePaths, function ($path) {
            return \file_exists($path);
        });
    }

    /**
     * @param mixed[] $additionalPaths
     */
    public function overrideAdditionalPaths($additionalPaths) : array
    {
        $extraAdditionalPaths = [];
        foreach ($this->emojiFiles as $path) {
            $extraAdditionalPaths[$path] = [
                'path' => $path,
                'dontTouch' => \false,
                'dontFollow' => \false,
                'dontSave' => \false
            ];
        }

        return \array_merge($extraAdditionalPaths, $additionalPaths);
    }

    /**
     * @return string|null
     */
    private function detectWpDebugLogPath()
    {
        if (!\defined('WP_DEBUG_LOG')) {
            return null;
        }
        if (\is_string(\WP_DEBUG_LOG)) {
            return \WP_DEBUG_LOG;
        } elseif (\WP_DEBUG_LOG) {
            return \WP_CONTENT_DIR . '/debug.log';
        } else {
            return null;
        }
    }

    /**
     * @param mixed[] $excludePaths
     */
    public function overrideAdditionalPathsExcludePaths($excludePaths) : array
    {
        $excludePaths[] = $this->wpDebugLogPath;

        return $excludePaths;
    }
}
