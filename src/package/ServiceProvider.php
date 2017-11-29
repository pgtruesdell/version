<?php

namespace PragmaRX\Version\Package;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use PragmaRX\Version\Package\Console\Commands\Build;
use PragmaRX\Version\Package\Console\Commands\Major;
use PragmaRX\Version\Package\Console\Commands\Minor;
use PragmaRX\Version\Package\Console\Commands\Patch;
use PragmaRX\Version\Package\Console\Commands\Show;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot Service Provider.
     */
    public function boot()
    {
        $this->publishConfiguration();

        $this->loadConfig();
    }

    private function getConfigFile()
    {
        return config_path('version.yml');
    }

    /**
     * Load config file to Laravel config.
     */
    private function loadConfig()
    {
        $this->app
            ->make('pragmarx.yaml-conf')
            ->loadToConfig($this->getConfigFile(), 'version');
    }

    /**
     * Configure config path.
     */
    private function publishConfiguration()
    {
        $this->publishes([
            __DIR__.'/../config/version.yml' => $this->getConfigFile(),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerService();

        $this->registerBlade();

        $this->registerCommands();
    }

    /**
     * Register Blade directives.
     */
    private function registerBlade()
    {
        Blade::directive('version', function ($format = Version::DEFAULT_FORMAT) {
            return "<?php echo app('pragmarx.version')->format($format); ?>";
        });
    }

    /**
     * Register command.
     *
     * @param $name
     * @param \Closure $command
     */
    private function registerCommand($name, \Closure $command)
    {
        $this->app->singleton($name, $command);

        $this->commands($name);
    }

    /**
     * Register Artisan commands.
     */
    private function registerCommands()
    {
        $this->registerCommand('pragmarx.version.build.command', function () {
            return new Build();
        });

        $this->registerCommand('pragmarx.version.show.command', function () {
            return new Show();
        });

        $this->registerCommand('pragmarx.version.major.command', function () {
            return new Major();
        });

        $this->registerCommand('pragmarx.version.minor.command', function () {
            return new Minor();
        });

        $this->registerCommand('pragmarx.version.patch.command', function () {
            return new Patch();
        });
    }

    /**
     * Register service service.
     */
    private function registerService()
    {
        $this->app->singleton('pragmarx.version', function ($app) {
            $version = $app->make(Version::class);

            $version->setConfigFile($this->getConfigFile());

            return $version;
        });
    }
}
