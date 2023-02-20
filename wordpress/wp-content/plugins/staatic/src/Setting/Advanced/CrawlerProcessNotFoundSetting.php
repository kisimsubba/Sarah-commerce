<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Setting\AbstractSetting;

final class CrawlerProcessNotFoundSetting extends AbstractSetting
{
    public function name() : string
    {
        return 'staatic_crawler_process_not_found';
    }

    public function type() : string
    {
        return self::TYPE_BOOLEAN;
    }

    public function label() : string
    {
        return \__('Process not found resources', 'staatic');
    }

    /**
     * @return string|null
     */
    public function extendedLabel()
    {
        return \__('Process page not found resources', 'staatic');
    }

    /**
     * @return string|null
     */
    public function description()
    {
        return \__('If enabled, in addition to the configured page not found page, other resources with a 404 status code will be included in the build as well.', 'staatic');
    }

    public function defaultValue()
    {
        return \false;
    }
}
