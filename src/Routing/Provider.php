<?php


namespace Dissonance\Routing;

use Dissonance\Container\DIContainerInterface;
use Dissonance\Core\{CoreInterface, ServiceProvider};


class Provider extends ServiceProvider
{
    public function register(): void
    {

        $this->registerFactory();
        $this->registerRoutesRepository();
        $this->registerRouter();
        $this->registerUriGenerator();

    }

    protected function registerFactory()
    {
        $this->app->singleton(RouterFactoryInterface::class, function (DIContainerInterface $app) {
            $class = $this->getFactoryClass();
            return new $class(
                $app,
                $this->getRouterClass(),
                $this->routesLoaderCallback(),
                $this->app['request']->getUri()->getHost()
            );
        });
    }

    protected function getFactoryClass()
    {
        return RouterFactory::class;
    }

    protected function getRouterClass()
    {
        return Router::class;
    }

    protected function routesLoaderCallback()
    {
        return function (RouterInterface $router) {
            /**
             * @var AppRoutingInterface $provider
             * @var RouterInterface $router
             */
            foreach ($this->app[AppsRoutesRepository::class]->getProviders() as $provider) {
                $app_id = $provider->getAppId();

                $router->group([
                    'prefix' => $app_id,
                    'as' => $app_id . '::',
                    'app' => $app_id
                ], function ($router) use ($provider) {
                    $provider->loadFrontendRoutes($router);
                });

                $router->group([
                    'prefix' => 'api/' . $app_id,
                    'as' => 'api:' . $app_id . '::',
                    'app' => $app_id
                ], function ($router) use ($provider) {
                    $provider->loadApiRoutes($router);
                });

                $router->group([
                        'prefix' => 'backend/' . $app_id,
                        'as' => 'backend:' . $app_id . '::',
                        'app' => $app_id]
                    , function ($router) use ($provider) {
                        $provider->loadBackendRoutes($router);
                    });

                $router->group([
                    'as' => 'default:' . $app_id . '::',
                    'app' => $app_id
                ], function ($router) use ($provider) {
                    $provider->loadDefaultRoutes($router);
                });
            }

        };
    }

    protected function registerRoutesRepository()
    {
        $this->app->singleton(AppsRoutesRepository::class)
            ->afterResolving(AppsRoutesRepository::class, function (AppsRoutesRepository $repository) {
                /**
                 * @var AppsRoutesRepository $repository
                 */
                return \_DS\event($repository);
            });
    }

    protected function registerRouter()
    {

        $this->app->singleton(RouterInterface::class, function ($app) {
            /**
             * @var RouterFactoryInterface $f
             */
            $f = $app[RouterFactoryInterface::class];
            $router = $f->factoryRouter(['name' => 'default']);
            $f->loadRoutes($router);
            return $router;

        }, 'router');
    }

    protected function registerUriGenerator()
    {
        $this->app->singleton(UrlGeneratorInterface::class, function (CoreInterface $app) {
            $base_uri = $app['base_uri'];
            $prefix = $app['config::uri_prefix'];
            if (!empty($prefix)) {
                $base_uri = rtrim($base_uri, '\\/') . '/' . trim($prefix, '\\/');
            }
            return new UrlGenerator(
                $app['router'],
                $base_uri,
                trim($app('config::assets_prefix', 'assets'), '/')
            );
        }, 'url');
    }

    public function boot(): void
    {

    }

    /**
     * @return RouterFactoryInterface
     */
    protected function getFactory()
    {
        return $this->app->make(RouterFactoryInterface::class);

    }
}