<?php

namespace Symbiotic\Routing;


class CacheLazyRouterDecorator extends CacheRouterDecorator implements RouterInterface, NamedRouterInterface, LazyRouterInterface
{
    protected bool $loaded = false;

    public function isLoadedRoutes(): bool
    {
        return $this->loaded;
    }

    public function getRoute(string $name): ?RouteInterface
    {
        $this->loadRoutes();
        return parent::getRoute($name);
    }

    public function loadRoutes()
    {
        if (!$this->loaded) {
            $this->factory->loadRoutes($this);
            $this->loaded = true;
        }
    }

    public function getRoutes(string $httpMethod = null): array
    {
        $this->loadRoutes();
        return parent::getRoutes($httpMethod);
    }

    public function getBySettlement(string $settlement): array
    {
        $this->loadRoutes();
        return parent::getBySettlement($settlement);
    }

    public function match(string $httpMethod, string $uri): ?RouteInterface
    {
        $this->loadRoutes();
        return parent::match($httpMethod, $uri); // TODO: Change the autogenerated stub
    }

    public function setName(string $name)
    {
        $this->call(__FUNCTION__, func_get_args());
    }

    public function getName(): string
    {
        return $this->call(__FUNCTION__, func_get_args());
    }

}
