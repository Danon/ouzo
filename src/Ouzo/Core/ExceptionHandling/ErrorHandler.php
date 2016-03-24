<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\ExceptionHandling;

use ErrorException;
use Exception;

class ErrorHandler
{
    public static function exceptionHandler(Exception $exception)
    {
        self::getExceptionHandler()->handleException($exception);
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (self::stopsExecution($errno)) {
            self::exceptionHandler(new ErrorException($errstr, $errno, 0, $errfile, $errline));
        } else {
            throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
        }
    }

    public static function stopsExecution($errno)
    {
        switch ($errno) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                return true;
        }
        return false;
    }

    /**
     * @return ExceptionHandler
     */
    private static function getExceptionHandler()
    {
        return new ExceptionHandler();
    }

    public static function shutdownHandler()
    {
        $error = error_get_last();

        if (!ExceptionHandler::lastErrorHandled() && $error && $error['type'] & (E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR)) {
            self::getExceptionHandler()->handleExceptionData(new OuzoExceptionData(500, array(new Error(0, $error['message'])), self::trace($error['file'], $error['line'])));
        }
    }

    private static function trace($errfile, $errline)
    {
        return "$errfile:$errline";
    }
}
