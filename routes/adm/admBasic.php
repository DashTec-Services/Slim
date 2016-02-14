<?php
/**
 * Created by PhpStorm.
 * User: Dave
 * Date: 27.04.2015
 * Time: 15:06
 */


$app->post('/', function () use ($app) {

    if ($_SESSION['group'] == 'usr') {
        $user = $_SESSION['account_id'];
        \DB::update('users', array(
            'is_aktiv' => '0'
        ), "id=%s", $_SESSION['account_id']);


        \DB::insert('userLog', array(
            'user' => $user . ' ' . $_SERVER['HTTP_USER_AGENT'] . ' ' . $_SERVER['REMOTE_HOST'],
            'ip-address' => $_SERVER['REMOTE_ADDR'],
            'otherData' => 'Zugriff auf nicht erlaubte Ressource! Benutzer wurde deaktiviert!'
        ));

        session_destroy();

    } elseif ($_SESSION['group'] == 'adm') {


        if (isset($_POST['addUserAdm']) AND !empty($_POST['vname']) AND !empty($_POST['surename']) AND !empty($_POST['loginnumb']) AND !empty($_POST['bday'])) {
            $vname = $app->request()->post('vname');
            $surename = $app->request()->post('surename');
            $loginnumb = $app->request()->post('loginnumb');
            $bday = $app->request()->post('bday');

            $CheckAccount = DB::queryFirstRow("SELECT * FROM users WHERE loginnumb=%s", $loginnumb);

            if (!isset($CheckAccount['loginnumb'])) {

                $password = new \core\password\password();

                DB::insert('users', array(
                    'usrgroup' => 'adm',
                    'vname' => $vname,
                    'surename' => $surename,
                    'loginnumb' => $loginnumb,
                    'bday' => $password->createPassword($bday),
                    'class' => '0'
                ));

                $_SESSION['success_message'] = true;
            } else {
                $_SESSION['error_message'] = true;

            }
        }


        if (isset($_POST['chPassw']) AND !empty($_POST['passwnow']) AND !empty($_POST['passwnew']) AND !empty($_POST['passwnowWH'])) {
            $_SESSION['error_messagePWCH'] = false;
            $_SESSION['success_messagePWCH'] = false;


            $passwnow = $app->request()->post('passwnow');
            $passwnew = $app->request()->post('passwnew');
            $passwnowWH = $app->request()->post('passwnowWH');

            $password = new \core\password\password();

            $CheckAccount = DB::queryFirstRow("SELECT * FROM users WHERE bday=%s AND id=%s", $password->createPassword($passwnow), $_SESSION['account_id']);


            if($passwnew == $passwnowWH AND $CheckAccount['bday'] != $password->createPassword($passwnew) AND  $CheckAccount['bday'] ==  $password->createPassword($passwnow)){

                DB::update('users', array(
                    'bday' => $password->createPassword($passwnew)
                ), "id=%s", $_SESSION['account_id']);

                $_SESSION['success_messagePWCH'] = true;
                $_SESSION['error_messagePWCH'] = false;

            }else{
                $_SESSION['error_messagePWCH'] = true;
                $_SESSION['success_messagePWCH'] = false;

            }



        }








        $app->render('header.phtml');
        $app->render('usrMenu.phtml');
        $app->render('adm/admsideBar.phtml');
        $app->render('/adm/home.phtml');
        $app->render('footer.phtml');
    } else {
        if (isset($_SESSION['account_id'])) {
            $user = $_SESSION['account_id'];

            \DB::update('users', array(
                'is_aktiv' => '0'
            ), "id=%s", $_SESSION['account_id']);

            \DB::insert('userLog', array(
                'user' => $user . ' ' . $_SERVER['HTTP_USER_AGENT'] . ' ' . $_SERVER['REMOTE_HOST'],
                'ip-address' => $_SERVER['REMOTE_ADDR'],
                'otherData' => 'Zugriff auf nicht erlaubte Ressource! Benutzer wurde deaktiviert!'
            ));
            session_destroy();
        } else {
            \DB::insert('userLog', array(
                'user' => $_SERVER['HTTP_USER_AGENT'] . ' ' . $_SERVER['REMOTE_HOST'],
                'ip-address' => $_SERVER['REMOTE_ADDR'],
                'otherData' => 'Zugriff auf nicht erlaubte Ressource'
            ));
            session_destroy();
        }
    }


})->name('restricted');


$app->get('/admmain', function () use ($app) {

    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('/adm/home.phtml');


    $app->render('footer.phtml');

})->name('restricted');


