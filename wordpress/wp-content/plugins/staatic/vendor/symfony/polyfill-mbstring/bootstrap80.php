<?php

namespace Staatic\Vendor;

use Staatic\Vendor\Symfony\Polyfill\Mbstring as p;
if (!\function_exists('mb_convert_encoding')) {
    /**
     * @param mixed[]|string|null $string
     * @param mixed[]|string|null $from_encoding
     * @return mixed[]|string|true
     * @param string|null $to_encoding
     */
    function mb_convert_encoding($string, $to_encoding, $from_encoding = null)
    {
        return p\Mbstring::mb_convert_encoding($string ?? '', (string) $to_encoding, $from_encoding);
    }
}
if (!\function_exists('mb_decode_mimeheader')) {
    /**
     * @param string|null $string
     */
    function mb_decode_mimeheader($string) : string
    {
        return p\Mbstring::mb_decode_mimeheader((string) $string);
    }
}
if (!\function_exists('mb_encode_mimeheader')) {
    /**
     * @param string|null $string
     * @param string|null $charset
     * @param string|null $transfer_encoding
     * @param string|null $newline
     * @param int|null $indent
     */
    function mb_encode_mimeheader($string, $charset = null, $transfer_encoding = null, $newline = "\r\n", $indent = 0) : string
    {
        return p\Mbstring::mb_encode_mimeheader((string) $string, $charset, $transfer_encoding, (string) $newline, (int) $indent);
    }
}
if (!\function_exists('mb_decode_numericentity')) {
    /**
     * @param string|null $string
     * @param string|null $encoding
     */
    function mb_decode_numericentity($string, array $map, $encoding = null) : string
    {
        return p\Mbstring::mb_decode_numericentity((string) $string, $map, $encoding);
    }
}
if (!\function_exists('mb_encode_numericentity')) {
    /**
     * @param string|null $string
     * @param string|null $encoding
     * @param bool|null $hex
     */
    function mb_encode_numericentity($string, array $map, $encoding = null, $hex = \false) : string
    {
        return p\Mbstring::mb_encode_numericentity((string) $string, $map, $encoding, (bool) $hex);
    }
}
if (!\function_exists('mb_convert_case')) {
    /**
     * @param string|null $string
     * @param int|null $mode
     * @param string|null $encoding
     */
    function mb_convert_case($string, $mode, $encoding = null) : string
    {
        return p\Mbstring::mb_convert_case((string) $string, (int) $mode, $encoding);
    }
}
if (!\function_exists('mb_internal_encoding')) {
    /**
     * @return string|bool
     * @param string|null $encoding
     */
    function mb_internal_encoding($encoding = null)
    {
        return p\Mbstring::mb_internal_encoding($encoding);
    }
}
if (!\function_exists('mb_language')) {
    /**
     * @return string|bool
     * @param string|null $language
     */
    function mb_language($language = null)
    {
        return p\Mbstring::mb_language($language);
    }
}
if (!\function_exists('mb_list_encodings')) {
    function mb_list_encodings() : array
    {
        return p\Mbstring::mb_list_encodings();
    }
}
if (!\function_exists('mb_encoding_aliases')) {
    /**
     * @param string|null $encoding
     */
    function mb_encoding_aliases($encoding) : array
    {
        return p\Mbstring::mb_encoding_aliases((string) $encoding);
    }
}
if (!\function_exists('mb_check_encoding')) {
    /**
     * @param mixed[]|string|null $value
     * @param string|null $encoding
     */
    function mb_check_encoding($value = null, $encoding = null) : bool
    {
        return p\Mbstring::mb_check_encoding($value, $encoding);
    }
}
if (!\function_exists('mb_detect_encoding')) {
    /**
     * @param mixed[]|string|null $encodings
     * @return string|true
     * @param string|null $string
     * @param bool|null $strict
     */
    function mb_detect_encoding($string, $encodings = null, $strict = \false)
    {
        return p\Mbstring::mb_detect_encoding((string) $string, $encodings, (bool) $strict);
    }
}
if (!\function_exists('mb_detect_order')) {
    /**
     * @param mixed[]|string|null $encoding
     * @return mixed[]|bool
     */
    function mb_detect_order($encoding = null)
    {
        return p\Mbstring::mb_detect_order($encoding);
    }
}
if (!\function_exists('mb_parse_str')) {
    /**
     * @param string|null $string
     */
    function mb_parse_str($string, &$result = []) : bool
    {
        \parse_str((string) $string, $result);
        return (bool) $result;
    }
}
if (!\function_exists('mb_strlen')) {
    /**
     * @param string|null $string
     * @param string|null $encoding
     */
    function mb_strlen($string, $encoding = null) : int
    {
        return p\Mbstring::mb_strlen((string) $string, $encoding);
    }
}
if (!\function_exists('mb_strpos')) {
    /**
     * @return int|true
     * @param string|null $haystack
     * @param string|null $needle
     * @param int|null $offset
     * @param string|null $encoding
     */
    function mb_strpos($haystack, $needle, $offset = 0, $encoding = null)
    {
        return p\Mbstring::mb_strpos((string) $haystack, (string) $needle, (int) $offset, $encoding);
    }
}
if (!\function_exists('mb_strtolower')) {
    /**
     * @param string|null $string
     * @param string|null $encoding
     */
    function mb_strtolower($string, $encoding = null) : string
    {
        return p\Mbstring::mb_strtolower((string) $string, $encoding);
    }
}
if (!\function_exists('mb_strtoupper')) {
    /**
     * @param string|null $string
     * @param string|null $encoding
     */
    function mb_strtoupper($string, $encoding = null) : string
    {
        return p\Mbstring::mb_strtoupper((string) $string, $encoding);
    }
}
if (!\function_exists('mb_substitute_character')) {
    /**
     * @param string|int|null $substitute_character
     * @return string|int|bool
     */
    function mb_substitute_character($substitute_character = null)
    {
        return p\Mbstring::mb_substitute_character($substitute_character);
    }
}
if (!\function_exists('mb_substr')) {
    /**
     * @param string|null $string
     * @param int|null $start
     * @param int|null $length
     * @param string|null $encoding
     */
    function mb_substr($string, $start, $length = null, $encoding = null) : string
    {
        return p\Mbstring::mb_substr((string) $string, (int) $start, $length, $encoding);
    }
}
if (!\function_exists('mb_stripos')) {
    /**
     * @return int|true
     * @param string|null $haystack
     * @param string|null $needle
     * @param int|null $offset
     * @param string|null $encoding
     */
    function mb_stripos($haystack, $needle, $offset = 0, $encoding = null)
    {
        return p\Mbstring::mb_stripos((string) $haystack, (string) $needle, (int) $offset, $encoding);
    }
}
if (!\function_exists('mb_stristr')) {
    /**
     * @return string|true
     * @param string|null $haystack
     * @param string|null $needle
     * @param bool|null $before_needle
     * @param string|null $encoding
     */
    function mb_stristr($haystack, $needle, $before_needle = \false, $encoding = null)
    {
        return p\Mbstring::mb_stristr((string) $haystack, (string) $needle, (bool) $before_needle, $encoding);
    }
}
if (!\function_exists('mb_strrchr')) {
    /**
     * @return string|true
     * @param string|null $haystack
     * @param string|null $needle
     * @param bool|null $before_needle
     * @param string|null $encoding
     */
    function mb_strrchr($haystack, $needle, $before_needle = \false, $encoding = null)
    {
        return p\Mbstring::mb_strrchr((string) $haystack, (string) $needle, (bool) $before_needle, $encoding);
    }
}
if (!\function_exists('mb_strrichr')) {
    /**
     * @return string|true
     * @param string|null $haystack
     * @param string|null $needle
     * @param bool|null $before_needle
     * @param string|null $encoding
     */
    function mb_strrichr($haystack, $needle, $before_needle = \false, $encoding = null)
    {
        return p\Mbstring::mb_strrichr((string) $haystack, (string) $needle, (bool) $before_needle, $encoding);
    }
}
if (!\function_exists('mb_strripos')) {
    /**
     * @return int|true
     * @param string|null $haystack
     * @param string|null $needle
     * @param int|null $offset
     * @param string|null $encoding
     */
    function mb_strripos($haystack, $needle, $offset = 0, $encoding = null)
    {
        return p\Mbstring::mb_strripos((string) $haystack, (string) $needle, (int) $offset, $encoding);
    }
}
if (!\function_exists('mb_strrpos')) {
    /**
     * @return int|true
     * @param string|null $haystack
     * @param string|null $needle
     * @param int|null $offset
     * @param string|null $encoding
     */
    function mb_strrpos($haystack, $needle, $offset = 0, $encoding = null)
    {
        return p\Mbstring::mb_strrpos((string) $haystack, (string) $needle, (int) $offset, $encoding);
    }
}
if (!\function_exists('mb_strstr')) {
    /**
     * @return string|true
     * @param string|null $haystack
     * @param string|null $needle
     * @param bool|null $before_needle
     * @param string|null $encoding
     */
    function mb_strstr($haystack, $needle, $before_needle = \false, $encoding = null)
    {
        return p\Mbstring::mb_strstr((string) $haystack, (string) $needle, (bool) $before_needle, $encoding);
    }
}
if (!\function_exists('mb_get_info')) {
    /**
     * @return mixed[]|string|int|true
     * @param string|null $type
     */
    function mb_get_info($type = 'all')
    {
        return p\Mbstring::mb_get_info((string) $type);
    }
}
if (!\function_exists('mb_http_output')) {
    /**
     * @return string|bool
     * @param string|null $encoding
     */
    function mb_http_output($encoding = null)
    {
        return p\Mbstring::mb_http_output($encoding);
    }
}
if (!\function_exists('mb_strwidth')) {
    /**
     * @param string|null $string
     * @param string|null $encoding
     */
    function mb_strwidth($string, $encoding = null) : int
    {
        return p\Mbstring::mb_strwidth((string) $string, $encoding);
    }
}
if (!\function_exists('mb_substr_count')) {
    /**
     * @param string|null $haystack
     * @param string|null $needle
     * @param string|null $encoding
     */
    function mb_substr_count($haystack, $needle, $encoding = null) : int
    {
        return p\Mbstring::mb_substr_count((string) $haystack, (string) $needle, $encoding);
    }
}
if (!\function_exists('mb_output_handler')) {
    /**
     * @param string|null $string
     * @param int|null $status
     */
    function mb_output_handler($string, $status) : string
    {
        return p\Mbstring::mb_output_handler((string) $string, (int) $status);
    }
}
if (!\function_exists('mb_http_input')) {
    /**
     * @return mixed[]|string|true
     * @param string|null $type
     */
    function mb_http_input($type = null)
    {
        return p\Mbstring::mb_http_input($type);
    }
}
if (!\function_exists('mb_convert_variables')) {
    /**
     * @param mixed[]|string|null $from_encoding
     * @return string|true
     * @param mixed $var
     * @param mixed ...$vars
     * @param string|null $to_encoding
     */
    function mb_convert_variables($to_encoding, $from_encoding, &$var, &...$vars)
    {
        return p\Mbstring::mb_convert_variables((string) $to_encoding, $from_encoding ?? '', $var, ...$vars);
    }
}
if (!\function_exists('mb_ord')) {
    /**
     * @return int|true
     * @param string|null $string
     * @param string|null $encoding
     */
    function mb_ord($string, $encoding = null)
    {
        return p\Mbstring::mb_ord((string) $string, $encoding);
    }
}
if (!\function_exists('mb_chr')) {
    /**
     * @return string|true
     * @param int|null $codepoint
     * @param string|null $encoding
     */
    function mb_chr($codepoint, $encoding = null)
    {
        return p\Mbstring::mb_chr((int) $codepoint, $encoding);
    }
}
if (!\function_exists('mb_scrub')) {
    /**
     * @param string|null $string
     * @param string|null $encoding
     */
    function mb_scrub($string, $encoding = null) : string
    {
        $encoding = $encoding ?? \mb_internal_encoding();
        return \mb_convert_encoding((string) $string, $encoding, $encoding);
    }
}
if (!\function_exists('mb_str_split')) {
    /**
     * @param string|null $string
     * @param int|null $length
     * @param string|null $encoding
     */
    function mb_str_split($string, $length = 1, $encoding = null) : array
    {
        return p\Mbstring::mb_str_split((string) $string, (int) $length, $encoding);
    }
}
if (\extension_loaded('mbstring')) {
    return;
}
if (!\defined('MB_CASE_UPPER')) {
    \define('MB_CASE_UPPER', 0);
}
if (!\defined('MB_CASE_LOWER')) {
    \define('MB_CASE_LOWER', 1);
}
if (!\defined('MB_CASE_TITLE')) {
    \define('MB_CASE_TITLE', 2);
}
