# PrestaShop Sentry Error handler for Modules

This repository includes sentry lib with handler that you can use in your PrestaShop Module to catch errors and send it to sentry.

## Pre-requisites

You should install this library only on a PrestaShop environment and with PHP 7.1.3 minimum.

## Installation

```
composer require prestashop/module-lib-error-handler
```

## Usage

- To catch all unhandled exceptions in the module you need to init error handler in your main class :

```
    new PrestaShop\Sentry\Handler\Errorhandler($dsn, $module->getLocalPath());
```

- If you want to add custom settings like tags, user and level you should create your own ErrorHandler and extend \PrestaShop\Sentry\Handler\ErrorHandler :

```
class ErrorHandler extends \PrestaShop\Sentry\Handler\ErrorHandler
{
    /**
     * @var ?Raven_Client
     */
    protected $client;

    public function __construct(Module $module, Env $env)
    {
        parent::__construct($env->get('SENTRY_CREDENTIALS'), $module->getLocalPath());
        
          $this->setUser(
            [
                'id' => Configuration::get('PS_SHOP_EMAIL'),
                'name' => Configuration::get('PS_SHOP_EMAIL')
            ],
            true
        );
        
        $this->setLevel(\Sentry\Severity::warning());
        
             $this->setTags(        
             [
                'ps_version' => $module->version,
                'ps_version' => $psAccounts ? $psAccounts->version : false,
                'php_version' => phpversion(),
                'prestashop_version' => _PS_VERSION_,
                'ps_is_enabled' => (int) Module::isEnabled($module->name),
                'ps_is_installed' => (int) Module::isInstalled($module->name),
                'env' => $env->get('SENTRY_ENVIRONMENT')
            ]
        );
    }
}
```

- You can also send exceptions if you catch them by using sentry handle :

```
        $this->errorHandler = $this->module->getService(ErrorHandler::class);
        try {
            throw new ModuleVersionException('test exception');
        } catch (ModuleVersionException $exception) {
            $this->errorHandler->handle($exception);
            return;
        }
```

- There are also extra function that sets default module tags to sentry :

```
    $this->setModuleInfo(Module $module);
```
