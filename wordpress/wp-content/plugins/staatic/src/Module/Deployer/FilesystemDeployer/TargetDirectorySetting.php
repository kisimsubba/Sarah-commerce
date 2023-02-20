<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\FilesystemDeployer;

use Staatic\WordPress\Service\Filesystem;
use Staatic\WordPress\Setting\AbstractSetting;

final class TargetDirectorySetting extends AbstractSetting
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function name() : string
    {
        return 'staatic_filesystem_target_directory';
    }

    public function type() : string
    {
        return self::TYPE_STRING;
    }

    public function label() : string
    {
        return \__('Target Directory', 'staatic');
    }

    /**
     * @return string|null
     */
    public function description()
    {
        return \__('The path to the directory on the filesystem where the static version of your site is deployed.', 'staatic');
    }

    public function sanitizeValue($value)
    {
        $path = \untrailingslashit($this->filesystem->normalizePath($value));
        if (strncmp($this->filesystem->getRootPath(), $path, strlen($path)) === 0) {
            \add_settings_error('staatic-settings', 'invalid_filesystem_target_directory', \sprintf(
                /* translators: %s: Supplied target directory. */
                \__('The supplied target directory "%s" is on the same level (or higher) as to where WordPress itself is installed. This would overwrite your WordPress installation and possibly more. Please choose another directory to publish your site to.', 'staatic'),
                $value
            ));

            return $this->defaultValue();
        }

        return $path;
    }

    public function defaultValue()
    {
        return $this->filesystem->getUploadsDirectory() . 'staatic/deploy/';
    }
}
