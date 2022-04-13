<?php

namespace PrestaShop\Sentry\Config;

class Env
{
    /**
     * @param string $key
     *
     * @return string
     */
    public function get($key)
    {
        if (!empty($_ENV[$key])) {
            return $_ENV[$key];
        }

        return '';
    }
}
