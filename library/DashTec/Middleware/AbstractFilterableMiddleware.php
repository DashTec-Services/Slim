<?php
namespace DashTec\Middleware;

use Slim\App;
use Slim\Route;

abstract class AbstractFilterableMiddleware
{
    const INCLUSION = 'inclusion';
    const EXCLUSION = 'exclusion';

    /**
     * @var App
     */
    protected $app;

    /**
     * @var array
     */
    protected $settings;

    /**
     * AbstractFilterableMiddleware constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->settings = $app->getContainer()->get('settings');
    }

    abstract protected function getConfigKey();

    protected function shouldProcessRoute(Route $route)
    {
        $middlewareRouteFilterConfig = $this->settings['middleware'][$this->getConfigKey()];
        $filterMode = $this->getFilterModeFromFilterConfig($middlewareRouteFilterConfig);
        $routeNames = $middlewareRouteFilterConfig['route_names'];
        $result = in_array($route->getName(), $routeNames);
        return $filterMode === static::INCLUSION ? $result : !$result;
    }

    protected function getFilterModeFromFilterConfig($filterConfig)
    {
        $validModes = [static::INCLUSION, static::EXCLUSION];
        $filterMode = isset($filterConfig['filter_mode']) ? $filterConfig['filter_mode'] : null;

        if (!in_array($filterMode, $validModes)) {
            throw new \LogicException('invalid filter mode configured: ' . $filterMode);
        }

        return $filterMode;
    }
}