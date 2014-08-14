<?php
namespace Ouzo\Utilities;

class Json
{
    public static function decode($string, $asArray = false)
    {
        return json_decode($string, $asArray);
    }

    public static function encode($array)
    {
        return json_encode($array);
    }

    public static function lastError()
    {
        return json_last_error();
    }

    public static function isJson($string)
    {
        return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $string));
    }
}