<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Build;

use Staatic\Vendor\GuzzleHttp\Psr7\Exception\MalformedUriException;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\WordPress\Setting\AbstractSetting;

final class DestinationUrlSetting extends AbstractSetting
{
    public function name() : string
    {
        return 'staatic_destination_url';
    }

    public function type() : string
    {
        return self::TYPE_STRING;
    }

    public function label() : string
    {
        return \__('Destination URL', 'staatic');
    }

    /**
     * @return string|null
     */
    public function description()
    {
        return \__('The destination URL determines how links within your static site are constructed.', 'staatic');
    }

    public function defaultValue()
    {
        return '/';
    }

    public function sanitizeValue($value)
    {
        try {
            $url = new Uri($value);
        } catch (MalformedUriException $e) {
            \add_settings_error('staatic-settings', 'invalid_destination_url', \sprintf(
                /* translators: %s: Supplied destination URL. */
                \__('The supplied destination URL "%s" is invalid; using "/" instead', 'staatic'),
                $value
            ));

            return '/';
        }

        return $value;
    }

    /**
     * @param mixed[] $attributes
     * @return void
     */
    public function render($attributes = [])
    {
        $this->renderer->render('admin/settings/destination_url.php', [
            'setting' => $this,
            'attributes' => $attributes
        ]);
    }
}
