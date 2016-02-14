<?php
/**
 * Created by David Schomburg (DashTec - Services)
 *      www.dashtec.de
 *
 *  S:P (StreamersPanel)
 *  Support: http://board.streamerspanel.de
 *
 *  v 4.5.0
 *
 *  Kundennummer:   @KDNUM@
 *  Lizenznummer:   @RECHNR@
 *  Lizenz: http://login.streamerspanel.de/user/terms
 */

# Passende Stationconf laden
$app->get('/playlist/timecontrol', function() use ($app){

    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('station/userautodj.phtml', compact('Users'));

})->name('user');



$app->post('/playlist/timecontrol', function() use ($app){
    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    #$app->render('station/userautodj.phtml', compact('Users'));
    $spgrowel = new \core\sp_special\growl();



    if (isset($_POST['editPlaytime'])){
        # PlaylistTimer und Playlisten ändern
        # Prüfung ob erlaubt

        $_SESSION['editPlaytime'] = $_POST['editPlaytime'];

        $playlist = DB::queryFirstRow("SELECT * FROM liquid WHERE id=%s", $_SESSION['editPlaytime']);

        $app->render('station/userautodj.phtml', compact('playlist'));
        $app->render('playlist/timecontrol.phtml', compact('playlist'));
    }


    if (isset($_POST['stopPlaytimer'])){
        $_SESSION['stopPlaytimer'] = $_POST['stopPlaytimer'];
        DB::update('liquid', array(
            'timeplay_is_activ' => '0'
        ), "id=%s", $_SESSION['stopPlaytimer']);

        # PlayTimer löschen
        # Playliste entladen
        $spgrowel->writeGrowl('success','editPlaytime','STOP Playliste');


        unset($_SESSION['stopPlaytimer']);
        $app->render('station/userautodj.phtml', compact('Users'));

    }

    if (isset($_POST['startPlayTimer'])){
        $_SESSION['startPlayTimer'] = $_POST['startPlayTimer'];
        DB::update('liquid', array(
            'timeplay_is_activ' => '1'
        ), "id=%s", $_SESSION['startPlayTimer']);
        # PlayTimer setzen
        # Verschiedene Playlisten zur Auswahl geben
        # Prüfen ob alle Playlisten gesetzt ist und Zeit Valid bzw auch kleiner ist

        $spgrowel->writeGrowl('success','editPlaytime','start Playliste');
        unset($_SESSION['startPlayTimer']);
        $app->render('station/userautodj.phtml', compact('Users'));
    }


})->name('user');





$app->get('/playlist/setTime', function() use ($app){
    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('station/userautodj.phtml', compact('Users'));
})->name('user');


$app->post('/playlist/setTime', function() use ($app){
    $vmstarth = $app->request()->post('vmstart');
    $vmstop = $app->request()->post('vmstop');

    $dstart = $app->request()->post('dstart');
    $dstop = $app->request()->post('dstop');

    $nstart = $app->request()->post('nstart');
    $nstop = $app->request()->post('nstop');

    # Do Action if User ==

        DB::update('liquid', array(
            'vmstart' => $vmstarth,
            'vmstop' => $vmstop,
            'dstart' => $dstart,
            'dstop' => $dstop,
            'nstart' => $nstart,
            'nstop' => $nstop
        ), "id=%s", $_SESSION['editPlaytime']);


    unset($_SESSION['editPlaytime']);
    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('station/userautodj.phtml', compact('Users'));

})->name('user');
