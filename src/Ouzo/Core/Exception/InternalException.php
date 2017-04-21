<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Exception;

use Ouzo\ExceptionHandling\Error;
use Ouzo\ExceptionHandling\OuzoException;

class InternalException extends OuzoException
{
    const HTTP_CODE = 500;

    /**
     * @param Error[]|Error $errors
     * @param string[] $headers
     */
    public function __construct($errors, $headers = [])
    {
        parent::__construct(self::HTTP_CODE, "Internal error.", $errors, $headers);
    }
}
