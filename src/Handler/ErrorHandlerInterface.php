<?php

namespace PrestaShop\Sentry\Handler;

interface ErrorHandlerInterface
{
    public function handle($error, $code = null, $throw = true, $data = null);
}
