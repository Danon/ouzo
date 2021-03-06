<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests;

use Ouzo\DownloadHandler;
use Ouzo\Utilities\Arrays;

class MockDownloadHandler extends DownloadHandler
{
    private $fileData = [];

    public function downloadFile(array $fileData)
    {
        $this->fileData = $fileData;
        return $this;
    }

    public function streamMediaFile(array $fileData)
    {
        $this->fileData = $fileData;
        return $this;
    }

    public function getFileName()
    {
        return Arrays::getValue($this->fileData, 'label');
    }

    public function getFileData()
    {
        return Arrays::getValue($this->fileData, 'data');
    }
}
