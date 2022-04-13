<?php

use Configuration;
use Module;

namespace PrestaShop\Sentry\Handler;

/**
 * Handle Error.
 */
class ErrorHandler implements ErrorHandlerInterface
{
    public function __construct(Module $module, Env $env)
    {
        \Sentry\init(
            [
                'dsn' => $env->get('SENTRY_CREDENTIALS'),
//                'in_app_include' => [$module->name],
                'project_root' => $module->getLocalPath(),
            ]
        );
        \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($module, $env): void {
            $scope->setLevel(\Sentry\Severity::warning());
            $scope->setUser(
                [
                    'name' => Configuration::get('PS_SHOP_EMAIL')
                ],
                true
            );
            $scope->setTags(
                [
                    'ps_eventbus_version' => $module->version,
                    'php_version' => phpversion(),
                    'prestashop_version' => _PS_VERSION_,
                    'ps_eventbus_is_enabled' => (int) Module::isEnabled($module->name),
                    'ps_eventbus_is_installed' => (int) Module::isInstalled($module->name),
                    'env' => $env->get('SENTRY_ENVIRONMENT'),
                ]
            );
        });
    }

    /**
     * @param Exception $error
     * @param mixed $code
     * @param bool|null $throw
     * @param array|null $data
     *
     * @return void
     *
     * @throws Exception
     */
    public function handle($exception, $code = null, $throw = true, $data = null)
    {
        \Sentry\captureException($exception);
    }
}
