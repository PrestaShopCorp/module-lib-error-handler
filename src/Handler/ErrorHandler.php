<?php

namespace PrestaShop\Sentry\Handler;

use Configuration;
use Module;
use Sentry\Severity;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Throwable;

/**
 * Handle Error.
 */
abstract class ErrorHandler implements ErrorHandlerInterface
{
    /**
     * @param string $dsn // sentry DSN key
     * @param string $localPath // local module path. You can find it in $module->getLocalPath()
     */
    public function __construct(string $dsn, string $localPath)
    {
        try {
            \Sentry\init(
                [
                    'dsn' => $dsn,
                    'project_root' => $localPath,
                ]
            );
        } catch (InvalidOptionsException $e) {
            return;
        }

        \Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
            $scope->setLevel(\Sentry\Severity::warning());
            $scope->setTags(
                [
                    'php_version' => phpversion(),
                    'prestashop_version' => _PS_VERSION_,
                ]
            );
        });
    }


    /**
     * @param array<string, string> $tags The tags to merge into the current context
     */
    public function setTags(array $tags): void
    {
        \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($tags): void {
            $scope->setTags(
                $tags
            );
        });
    }

    /**
     * @param Severity|null $level The severity
     */
    public function setLevel(Severity $level): void
    {
        \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($level): void {
            $scope->setLevel($level);
        });
    }

    /**
     * @param array<string, mixed> $data The data
     * @param bool $merge If true, $data will be merged into user context instead of replacing it
     */
    public function setUser(array $data, bool $merge = false): void
    {
        \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($data, $merge): void {
            $scope->setUser(
                $data,
                $merge
            );
        });
    }

    public function setModuleInfo(Module $module): void
    {
        $this->setTags(
            [
                $module->name . '_version' => $module->version,
                $module->name . '_is_enabled' => (int) Module::isEnabled($module->name),
                $module->name . '_is_installed' => (int) Module::isInstalled($module->name),
            ]
        );
    }

    public function handle(Throwable $exception): void
    {
        \Sentry\captureException($exception);
    }
}
