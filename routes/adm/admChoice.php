<?php
/**
 * Created by PhpStorm.
 * User: Dave
 * Date: 20.05.2015
 * Time: 11:05
 */



$app->get('/admchoice', function () use ($app) {



    $results = DB::query("SELECT * FROM project");


    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('adm/admchoice.phtml', compact('results'));


    $app->render('footer.phtml');

})->name('restricted');


