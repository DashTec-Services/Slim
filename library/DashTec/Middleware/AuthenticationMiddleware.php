<?php
namespace DashTec\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;

class AuthenticationMiddleware extends AbstractFilterableMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        /** @var Route $currentRoute */
        $currentRoute = $request->getAttribute('route');

        /** @var Route require name */
        $permissionRoute = $this->loadRequiredGroupsFromConfig($request);

        if ($currentRoute == true AND $this->shouldProcessRoute($currentRoute) != 'exklusion') {

            return $next($request, $response);
        }

        if (!isset($_SESSION['account_id'])) {
            return $this->redirectToLoginPage($request, $response);
        }

        $account = $this->tryToLoadAccountFromDatabase($_SESSION['account_id']);
        if (!$account) {
            return $response->withRedirect('/logout', 303);
        }


        if ($currentRoute == true) {

           

            if ((bool)array_intersect($_SESSION['user_groupe'], $permissionRoute)) {

                return $next($request, $response);

            } else {
                session_unset();
                session_destroy();
                return $response->withRedirect('/login', 303);
            }


        }

        return $next($request, $response);
    }

    protected function getConfigKey()
    {
        return 'authentication';
    }

    protected function getConfigKeyAuthorization()
    {
        return 'authorization';
    }

    protected function tryToLoadAccountFromDatabase($accountId)
    {
        return ['id' => $accountId, 'username' => 'foobar', 'password' => 'foobar'];
    }

    protected function redirectToLoginPage(Request $request, Response $response)
    {
        $this->storeCurrentUrlInSession($request);
        return $response->withRedirect('/login', 303);
    }

    protected function storeCurrentUrlInSession(Request $request)
    {
        $currentUrl = $request->getUri();
        $_SESSION['authentication.attempted_url'] = $currentUrl;
    }

    protected function getRouteName(Route $route)
    {
        return $route->getName();
    }

    protected function loadRequiredGroupsFromConfig(Request $request)
    {
        $middlewareConfig = $this->settings['middleware'][$this->getConfigKeyAuthorization()];

        if (!isset($middlewareConfig['route_group_mappings'])) {
            throw new \LogicException('missing route_group_mappings in configuration for middleware: ' . $this->getConfigKeyAuthorization());
        }

        # GET Array route_group_mappings from Conf->autorization
        $routeGroupMappings = $middlewareConfig['route_group_mappings'];

        # GET route_name from Conf->autorization
        $routeGroupeNames = $middlewareConfig['route_names'];
        $currentRoute = $request->getAttribute('route');

        # GET current RouteName
        $currentRouteName = $this->getRouteName($currentRoute);
        if (!isset($currentRouteName)) {
            throw new \LogicException('missing required routes for route with name: ' . $currentRouteName);
        }

        function in_array_r($currentRouteName, $routeGroupMappings, $strict = false)
        {
            foreach ($routeGroupMappings as $item) {
                if (($strict ? $item === $currentRouteName : $item == $currentRouteName) || (is_array($item) && in_array_r($currentRouteName, $item, $strict))) {
                    return true;
                }
            }
            return false;
        }

        # Check register array to conf
        if (in_array($currentRouteName, $routeGroupeNames)) {
            $returnStatement = $routeGroupMappings[$currentRouteName];
        } else {
            $returnStatement = false;
        }


        return $returnStatement;
    }

}
