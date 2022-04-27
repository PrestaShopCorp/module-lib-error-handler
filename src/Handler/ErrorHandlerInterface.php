<?php

namespace PrestaShop\Sentry\Handler;

use Sentry\Severity;
use Module;
use Throwable;

interface ErrorHandlerInterface
{
    public function setTags(array $tags): void;
    public function setLevel(Severity $level): void;
    public function setUser(array $data, bool $merge = false): void;
    public function setModuleInfo(Module $module): void;
    public function handle(Throwable $error): void;
}
