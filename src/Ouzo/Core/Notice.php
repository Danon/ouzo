<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Utilities\Arrays;

class Notice
{
    /** @var null|string */
    private $url;
    /** @var string */
    private $message;

    /**
     * @param string $message
     * @param null|string $url
     */
    public function __construct($message, $url = null)
    {
        $this->url = $url ? $url : null;
        $this->message = $message;
    }

    /**
     * @return null|string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param Uri $uri
     * @return bool
     */
    public function requestUrlMatches(Uri $uri)
    {
        return $this->getUrl() == null || strcmp(Uri::removePrefix($this->getCurrentPath($uri)), Uri::removePrefix($this->getUrlWithoutQuery($this->getUrl()))) === 0;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->message;
    }

    /**
     * @param Uri $uri
     * @return string
     */
    private function getCurrentPath(Uri $uri)
    {
        $url = $uri->getFullUrlWithPrefix();
        return $this->getUrlWithoutQuery($url);
    }

    /**
     * @param string $url
     * @return string
     */
    private function getUrlWithoutQuery($url)
    {
        $parts = parse_url($url);
        return Arrays::getValue($parts, 'path', $url);
    }
}
