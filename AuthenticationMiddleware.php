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
use DB;

class AuthenticationMiddleware extends AbstractFilterableMiddleware
{
    protected function getConfigKey()
    {
        return 'authentication';
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

        // Get reference to application
        $app = $this->app;

        if (!isset($_SESSION['account_id'])) {
            $this->redirectToLoginPage();
            return;
        }

        $account = $this->tryToLoadAccountFromDatabase($_SESSION['account_id']);
        if (!$account) {
            $app->redirect('/logout', 303);
            return;
        }

        if (!$account['is_aktiv']) {
            $app->redirect('/logout', 303);
            return;
        }

        $this->storeAccountInApplication($account);
    }

    protected function tryToLoadAccountFromDatabase($accountId)
    {
        return DB::queryFirstRow("SELECT * FROM consumer_login WHERE id=%d", $accountId);
    }

    protected function storeAccountInApplication($account)
    {
        $this->app->container->set('account', $account);
        $this->app->view()->set('account', $account);
    }

    protected function redirectToLoginPage()
    {
        $this->storeCurrentUrlInSession();
        $this->app->redirect('/login', 303);
    }

    protected function storeCurrentUrlInSession()
    {
        $currentUrl = $this->app->request()->getUrl();
        $_SESSION['authentication.attempted_url'] = $currentUrl;
    }
}


