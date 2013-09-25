<?php
namespace Ouzo\Utilities;

class Strings
{
    public static function underscoreToCamelCase($str)
    {
        $words = explode('_', strtolower($str));
        $return = '';
        foreach ($words as $word) {
            $return .= ucfirst(trim($word));
        }
        return $return;
    }

    public static function camelCaseToUnderscore($string)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }

    public static function changePolishChars($string)
    {
        $polishChars = array('ą', 'ż', 'ś', 'ź', 'ę', 'ć', 'ń', 'ó', 'ł', 'Ą', 'Ż', 'Ś', 'Ź', 'Ę', 'Ć', 'Ń', 'Ó', 'Ł');
        $replacement = array('a', 'z', 's', 'z', 'e', 'c', 'n', 'o', 'l', 'A', 'Z', 'S', 'Z', 'E', 'C', 'N', 'O', 'L');
        return str_replace($polishChars, $replacement, $string);
    }

    public static function removePrefix($string, $prefix)
    {
        if (self::startsWith($string, $prefix)) {
            $string = substr($string, strlen($prefix));
        }
        return $string;
    }

    public static function removePrefixes($string, array $prefixes)
    {
        return array_reduce($prefixes, function ($string, $prefix) {
            return Strings::removePrefix($string, $prefix);
        }, $string);
    }

    public static function startsWith($string, $prefix)
    {
        return strpos($string, $prefix) === 0;
    }

    public static function endsWith($string, $suffix)
    {
        return substr($string, -strlen($suffix)) === $suffix;
    }
}