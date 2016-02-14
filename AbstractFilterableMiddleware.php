<?php
/**
 * Created by DashTec - Services
 *
 *      SnapCare 2015
 *
 * Date: 09.10.2015
 * Time: 22:34
 * v0.0.11
 */
namespace SP\Middleware;

use Slim\Middleware;
use Slim\Route;

abstract class AbstractFilterableMiddleware extends Middleware
{
    const INCLUSION = 'inclusion';
    const EXCLUSION = 'exclusion';

    abstract protected function getConfigKey();

    protected function processAtRoute(Route $route)
    {
        $configKeyName = 'middleware.' . $this->getConfigKey();
        $middlewareRouteFilterConfig = $this->app->config($configKeyName);

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