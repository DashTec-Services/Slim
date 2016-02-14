<?php
/**
 * Created by David Schomburg (DashTec - Services)
 *      www.dashtec.de
 *
 *  S:P (StreamersPanel)
 *  Support: http://board.streamerspanel.de
 *
 *  v 4.0.0
 *
 *  Kundennummer:   @KDNUM@
 *  Lizenznummer:   @RECHNR@
 *  Lizenz: http://login.streamerspanel.de/user/terms
 */
namespace SP\Middleware;

class AuthorizationMiddleware extends AbstractFilterableMiddleware
{
    protected function getConfigKey()
    {
        return 'authorization';
    }

    public function call()
    {
        $this->app->hook('slim.before.dispatch', [$this, 'onBeforeDispatch']);

        // Run inner middleware and application
        $this->next->call();
    }

    public function onBeforeDispatch()
    {
        if (!$this->processAtRoute($this->app->router->getCurrentRoute())) {
            return;
        }

        $requiredGroups = $this->loadRequiredGroupsFromConfig();
        $accountGroup = $_SESSION['group'];

        if (!in_array($accountGroup, $requiredGroups)) {

            if(isset($_SESSION['account_id'])){
                $user = $_SESSION['account_id'];

                \DB::update('users', array(
                    'is_aktiv' => '0'
                ), "id=%s", $_SESSION['account_id']);


                \DB::insert('userLog', array(
                    'user' => $user.' '. $_SERVER['HTTP_USER_AGENT']. ' '.$_SERVER['REMOTE_HOST'],
                    'ip-address' =>$_SERVER['REMOTE_ADDR'],
                    'otherData' => 'Zugriff auf nicht erlaubte Ressource! Benutzer wurde deaktiviert!'
                ));

            }else{
                \DB::insert('userLog', array(
                    'user' => $_SERVER['HTTP_USER_AGENT']. ' '.$_SERVER['REMOTE_HOST'],
                    'ip-address' =>$_SERVER['REMOTE_ADDR'],
                    'otherData' => 'Zugriff auf nicht erlaubte Ressource'
                ));
            }


            $this->app->redirect('/logout', 303);
            $this->app->halt(403);
            return;
        }
    }

    protected function loadRequiredGroupsFromConfig()
    {
        $middlewareConfig = $this->app->config('middleware.' . $this->getConfigKey());

        if (!isset($middlewareConfig['route_group_mappings'])) {
            throw new \LogicException('missing route_group_mappings in configuration for middleware: ' . $this->getConfigKey());
        }

        $routeGroupMappings = $middlewareConfig['route_group_mappings'];
        $currentRouteName = $this->app->router()->getCurrentRoute()->getName();


        if (!isset($routeGroupMappings[$currentRouteName])) {
            throw new \LogicException('missing required routes for route with name: ' . $currentRouteName);
        }

        return $routeGroupMappings[$currentRouteName];
    }
}


