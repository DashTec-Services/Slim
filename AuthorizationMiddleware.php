<?php
/**
 * Created by David Schomburg (DashTec - Services)
 *      www.dashtec.de
 *
 *  SnapCare Project
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
        $accountGroup = $_SESSION['consumer_login']['Permission'];


        # Ergänzung der Benutzerrichtlinien



        # Prüfung ob berechtigungen für Benutzer besteht
        $CantGiveSuccess = true;
        if (is_array($accountGroup)) {
            $toproof = array_diff($accountGroup, $requiredGroups);

            foreach ($accountGroup as $varIfSet) {
                echo $varIfSet . '<br>';
                if (in_array($varIfSet, $requiredGroups)) {
                    $CantGiveSuccess = false;
                    break;
                }
            }
        } else {
            if (in_array($accountGroup, $requiredGroups)) {
                $CantGiveSuccess = false;
            }
        }

        # Blckt Benutzer wenn keine Berechtigung besteht
        if ($CantGiveSuccess == true ) {

            if (isset($_SESSION['account_id'])) {
                $user = $_SESSION['account_id'];

                \DB::update('consumer_login', array(
                    'is_aktiv' => '1'
                ), "id=%s", $_SESSION['account_id']);


                \DB::insert('userLog', array(
                    'userInformation' => $user . ' ' . $_SERVER['HTTP_USER_AGENT'],
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'Action' => 'Zugriff auf nicht erlaubte Ressource! Benutzer wurde deaktiviert!'
                ));

            } else {
                \DB::insert('userLog', array(
                    'userInformation' => $_SERVER['HTTP_USER_AGENT'] . ' ' . $_SERVER['REMOTE_HOST'],
                    'ip-address' => $_SERVER['REMOTE_ADDR'],
                    'Action' => 'Zugriff auf nicht erlaubte Ressource'
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


