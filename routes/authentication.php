<?php
/**
 * Created by David Schomburg (DashTec - Services)
 *      www.dashtec.de
 *
 *  S:P (StreamersPanel)
 *  Support: http://board.streamerspanel.de
 *
 *  v 0.25
 *
 *  Kundennummer:   @KDNUM@
 *  Lizenznummer:   @RECHNR@
 *  Lizenz: http://login.streamerspanel.de/user/terms
 */
use core\password\password;




// zeigt die loginseite an
$app->get('/login', function () use ($app) {


    $app->render('authentication/login.phtml');



})->name('login');

// fuehrt den login des benutzers durch
$app->post('/login', function () use ($app) {
    $username = $app->request()->post('username');
    $password = $app->request()->post('password');


    if (!$username || !$password) {
        $_SESSION['error_login_text'] = true;
        $app->redirect('/login', 303);
    }

    $account = DB::queryFirstRow("SELECT * FROM users WHERE loginnumb=%s", $username);
    if (!$account) {
        $app->flash('error', _('DB Invalid account credentials'));
        $_SESSION['error_login_text'] = true;
        $app->redirect('/login', 303);
    }

    $passwordIsCorrect = password::verifyPassword($password, $account['bday']);
    if (!$passwordIsCorrect || !$account['is_aktiv']) {
        $app->flash('error', _('PW Invalid account credentials'));
        $_SESSION['error_login_text'] = true;
        $app->redirect('/login', 303);
    }

    $proWoTopic = DB::queryFirstRow("SELECT proWoTopic, votersLogin FROM config WHERE id=%s", 1);
    if (!isset($_SESSION['proWoTopic'])){
        $_SESSION['proWoTopic'] = $proWoTopic['proWoTopic'];
    }

    if($proWoTopic['votersLogin'] == 0 AND $account['usrgroup'] == 'usr' ){
        $app->flash('error', _('PW Invalid account credentials'));

        $_SESSION['error_login_text'] = true;
        $app->redirect('/login', 303);
    }

    # Session setzen
    $_SESSION['account_id'] = (int)$account['id'];
    $_SESSION['group'] = $account['usrgroup'];
    $_SESSION['startClass'] = $account['class'];
    $_SESSION['UserName'] = $account['vname'] . ' '. $account['surename'];
    $_SESSION['voteDone'] = $account['voteDone'];

    if(isset($_SESSION['error_login_text']) AND  $_SESSION['error_login_text'] == true){
        $_SESSION['error_login_text'] = false;
    }

    DB::update('users', array(
        'lasLogin' => date("Y-m-d"),
        'lastlogTime' => date("H:i", time()),
    ), "id=%s", (int)$account['id']);

    $app->flash('success', _('Login successful'));
    $app->redirect('/');
})->name('doLogin');

// fuehrt den logout des benutzers durch
$app->get('/logout', function () use ($app) {
    $_SESSION = array();
    session_destroy();

    $app->flash('success', _('Logout successful'));
    $app->redirect('/login');
})->name('doLogout');
