<?php

class ManipleUserConsents_Filter_RelativizeHrefs implements Zend_Filter_Interface
{
    /**
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        return self::filterStatic($value);
    }

    /**
     * @param string $value
     * @return string
     */
    public static function filterStatic($value)
    {
        $serverUrlHelper = new Zend_View_Helper_ServerUrl();
        $serverUrl = $serverUrlHelper->serverUrl();

        return preg_replace_callback('/(href=")([^"]+)/i', function (array $matches) use ($serverUrl) {
            $url = $matches[2];
            if (stripos($url, $serverUrl)) {
                $url = substr($url, strlen($serverUrl));
            }
            return $matches[1] . $url;
        }, (string) $value);
    }
}
