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

# IceCast Stream Stop
$app->post('/station/icecast_on_off', function() use ($app) {
    $sp_growl = new core\sp_special\growl();

    if (isset($_POST['iceonoffselc'])) {

        # Aktion Stream Starten u. Stoppen
        $changer = explode(".", $_POST['iceonoffselc']);
        $icecastServ = new \core\sp_special\icecastserv();

        if ($changer['1'] == '1') {
            $icecastServ->startSc_Serv($changer['0']);
            if ($_SESSION['group'] == 'adm') {
                $SPMenu = new SP\Menu\MenuInclusion();
                $SPMenu->MenuInclude($app);
                $app->render('station/adminshowlist.phtml', compact('license'));
                $sp_growl->writeGrowl('success', _('Server gestartet'), '');
            } elseif ($_SESSION['group'] == 'user') {
                $SPMenu = new SP\Menu\MenuInclusion();
                $SPMenu->MenuInclude($app);
                $app->render('station/usershowstreams.phtml', compact('license'));
                $sp_growl->writeGrowl('success', _('Server gestartet'), '');
            }

        } elseif ($changer['1'] == '0') {

            $icecastServ->killIceServ($changer['0']);

            # Laden der Übersicht nach Änderungen
            if ($_SESSION['group'] == 'adm') {
                $SPMenu = new SP\Menu\MenuInclusion();
                $SPMenu->MenuInclude($app);
                $app->render('station/adminshowlist.phtml', compact('license'));
            } elseif ($_SESSION['group'] == 'user') {
                $SPMenu = new SP\Menu\MenuInclusion();
                $SPMenu->MenuInclude($app);
                $app->render('station/usershowstreams.phtml', compact('license'));
            }
            $sp_growl->writeGrowl('info', _('Server gestoppt'), '');
        }

    }





})->name('userrestricted');

# IceCast Löschen
$app->post('/station/iceshowstream', function() use ($app){
    $sp_growl = new core\sp_special\growl();

    $changer = explode(".", $_POST['icechangeConfServ']);

    # Stream löschen

        # Wenn Action == clear
        if ($changer[1] == 'clear' ) {
            $changer = explode(".", $_POST['icechangeConfServ']);

            $_SESSION['sec_rel_id'] = $_POST['icechangeConfServ'];

            $sc_rel = DB::queryFirstRow("SELECT * FROM icecast_rel WHERE id=%s", $changer['0']);
            $serverPort = DB::queryFirstRow("SELECT * FROM icecast_serv WHERE id=%s", $sc_rel['icecast_serv_id']);

            // Ordner löschen

            $iceDel = new \core\icecast\icecast();
            $iceDel->rmDir($serverPort['port']);

            DB::delete('icecast_rel', "id=%s", $changer['0']);
            DB::delete('icecast_serv', "id=%s", $sc_rel['icecast_serv_id']);
            DB::delete('liquid', "id=%s", $sc_rel['liquid_id']);





            $SPMenu = new SP\Menu\MenuInclusion();
            $SPMenu->MenuInclude($app);
            $app->render('station/adminshowlist.phtml', compact('license'));
            $sp_growl->writeGrowl('success', _('Server gelöscht'), '');

        }

        # Benutzer ändern
    if ($changer[1] == 'changeUser' ) {
        $_SESSION['sec_rel_id'] = $changer[0];
        $SPMenu = new SP\Menu\MenuInclusion();
        $SPMenu->MenuInclude($app);
        $app->render('icecast/icechangeStreamUser.phtml', compact('license'));
        $app->render('station/adminshowlist.phtml', compact('license'));
    }
})->name('restricted');

# IceCast Benutzer ändern
$app->post('/station/icechangeowner', function() use ($app){

    DB::update('icecast_rel', array(
        'accounts_id' => $_POST['ownerchange'],
    ), "id=%s", $_SESSION['sec_rel_id']);

    unset($_SESSION['sec_rel_id']);

    $growl = new \core\sp_special\growl();
    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('station/adminshowlist.phtml', compact('license'));
    $growl->writeGrowl('success',_('IceCast-Server'),'Änderungen wurden übernommen');

})->name('restricted');



# Bearbeiten
$app->get('/iceedit/stream', function() use ($app){
    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('station/adminshowlist.phtml', compact('license'));
})->name('restricted');
$app->post('/iceedit/stream', function() use ($app){
    $changer = explode(".", $_POST['icestreamtoEdit']);
    $iceServeData = DB::queryFirstRow("SELECT * FROM icecast_serv WHERE id=%s", $changer['0']);

    $_SESSION['update_id'] = $changer['0'];

    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('icecast/iceedit_adm.phtml', compact('iceServeData'));
    $growl = new \core\sp_special\growl();
})->name('restricted');
$app->post('/station/icecast_edit', function() use ($app){



    DB::update('icecast_serv', array(
        'clients' => $_POST['icecast']['clients'],
        'sources' => $_POST['icecast']['sources'],
        'port' => $_POST['icecast']['port'],
    ), "id=%s", $_SESSION['update_id']);


    unset($_SESSION['update_id']);

    $growl = new \core\sp_special\growl();
    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('station/adminshowlist.phtml', compact('license'));
    $growl->writeGrowl('success',_('IceCast-Server'),'Änderungen wurden übernommen');
})->name('restricted');





# Benutzerrouten

$app->post('/stationaddeditcontrol/iceedeituser', function() use ($app){
    $changer = explode(".", $_POST['icestreamtoEdit']);
    $iceServeData = DB::queryFirstRow("SELECT * FROM icecast_serv WHERE id=%s", $changer['0']);

    $_SESSION['update_id'] = $changer['0'];

    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('icecast/iceedit_user.phtml', compact('iceServeData'));
})->name('userrestricted');

$app->post('/station/icecast_edituser', function() use ($app){

DB::update('icecast_serv', array(
    'source-password' => $_POST['icecast']['source-password'],
    'relay-password' => $_POST['icecast']['relay-password'],
    'admin-user' => $_POST['icecast']['admin-user'],
    'admin-password' => $_POST['icecast']['admin-password'],
), "id=%s", $_SESSION['update_id']);

    unset($_SESSION['update_id']);
    $sp_growl = new core\sp_special\growl();

    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('station/usershowstreams.phtml', compact('license'));
    $sp_growl->writeGrowl('success', _('Server gelöscht'), '');

})->name('userrestricted');

# Playlist ändern
$app->post('/station/icecast_editplaylist', function() use ($app){


    if (isset($_POST['morningplst']) OR isset($_POST['dayplst']) OR isset($_POST['nightplst']) AND $app->config('demo_mod') == false){

        # Trennen der übergebenen Par.
        if (!empty($_POST['morningplst'])) {
            $changer = explode(".", $_POST['morningplst']);
        }
        if (!empty($_POST['dayplst'])) {
            $changer = explode(".", $_POST['dayplst']);
        }
        if (!empty($_POST['nightplst'])) {
            $changer = explode(".", $_POST['nightplst']);
        }
        #changer 0 = sc_rel ID
        #changer 1 = playlist ID
        #changer 3 = Playlistname

        # Auslesen der conf ID
        $servTrans = DB::queryFirstRow("SELECT icecast_serv_id, liquid_id  FROM icecast_rel WHERE id=%s", $changer['0']);

        # Port abfragen
        $PortBase = DB::queryFirstRow("SELECT port FROM icecast_serv WHERE id=%s", $servTrans['icecast_serv_id']);

        if (!empty($_POST['morningplst'])){
            # Setzen der neuen ID
            \DB::update('icecast_rel', array(
                'playlistM_id' => $changer['1']
            ), "id=%s", $changer['0']);

            # Eintragen der Playliste in die DB
            \DB::update('liquid', array(
                'myplaylistM' => $_SERVER['DOCUMENT_ROOT'] . '/streamconf/' . $PortBase['port'] . '/' . $changer['2'] . '.lst'
            ), "id=%s", $servTrans['liquid_id']);
        }

        if (!empty($_POST['dayplst'])){
            # Setzen der neuen ID
            \DB::update('icecast_rel', array(
                'playlistD_id' => $changer['1']
            ), "id=%s", $changer['0']);

            # Eintragen der Playliste in die DB
            \DB::update('liquid', array(
                'myplaylistD' => $_SERVER['DOCUMENT_ROOT'] . '/streamconf/' . $PortBase['port'] . '/' . $changer['2'] . '.lst'
            ), "id=%s", $servTrans['liquid_id']);
        }

        if (!empty($_POST['nightplst'])){
            # Setzen der neuen ID
            \DB::update('icecast_rel', array(
                'playlistN_id' => $changer['1']
            ), "id=%s", $changer['0']);

            # Eintragen der Playliste in die DB
            \DB::update('liquid', array(
                'myplaylistN' => $_SERVER['DOCUMENT_ROOT'] . '/streamconf/' . $PortBase['port'] . '/' . $changer['2'] . '.lst'
            ), "id=%s", $servTrans['liquid_id']);
        }


        $liquid = new \core\liquidsoap\liquidsoap();
        $liquid->createLiquiteConf($changer['0']);
        $liquid->createPlaylst($changer['0']);



        $sp_growl = new core\sp_special\growl();
        $SPMenu = new SP\Menu\MenuInclusion();
        $SPMenu->MenuInclude($app);
        $app->render('station/userautodj.phtml', compact('license'));
        $sp_growl->writeGrowl('success', _('Playliste'), 'Änderungen wurden übernommen.');

    }




# Neue Playliste übernehmen
    if (isset($_POST['iceplaylstswitch']) AND $_POST['iceplaylstswitch'] != '' AND $app->config('demo_mod') == false) {

        # Trennen der übergebenen Par.
        $changer = explode(".", $_POST['iceplaylstswitch']);

        #changer 0 = sc_rel ID
        #changer 1 = playlist ID
        #changer 3 = Playlistname

        # Auslesen der conf ID
        $servTrans = DB::queryFirstRow("SELECT icecast_serv_id, liquid_id  FROM icecast_rel WHERE id=%s", $changer['0']);

        # Setzen der neuen ID
        \DB::update('icecast_rel', array(
            'playlist_id' => $changer['1']
        ), "id=%s", $changer['0']);

        # Port abfragen
        $PortBase = DB::queryFirstRow("SELECT port FROM icecast_serv WHERE id=%s", $servTrans['icecast_serv_id']);

        # Eintragen der Playliste in die DB
        \DB::update('liquid', array(
            'myplaylist' => $_SERVER['DOCUMENT_ROOT'] . '/streamconf/' . $PortBase['port'] . '/' . $changer['2'] . '.lst'
        ), "id=%s", $servTrans['liquid_id']);


        $liquid = new \core\liquidsoap\liquidsoap();
        $liquid->createLiquiteConf($changer['0']);
        $liquid->createPlaylst($changer['0']);


        $sp_growl = new core\sp_special\growl();
        $SPMenu = new SP\Menu\MenuInclusion();
        $SPMenu->MenuInclude($app);
        $app->render('station/userautodj.phtml', compact('license'));
        $sp_growl->writeGrowl('success', _('Playliste'), 'Änderungen wurden übernommen.');





    }

# Start - Stop Logik
    if (isset($_POST['icedjSwitch']) AND $_POST['icedjSwitch'] != '' AND $app->config('demo_mod') == false){
        $changer = explode(".", $_POST['icedjSwitch']);


        # Start - Stop Transcoder
        if (isset($_POST['icedjSwitch']) AND $app->config('demo_mod') == false) {
            $changer = explode(".", $_POST['icedjSwitch']);
            if ($changer['1'] == '1') {
                $liquid = new \core\liquidsoap\liquidsoap();
                $liquid->startLiquid($changer['0']);



            } elseif ($changer['1'] == '0') {
                $liquid = new \core\liquidsoap\liquidsoap();
                $liquid->killSc_liquid($changer['0']);
            }
        }
        $sp_growl = new core\sp_special\growl();
        $SPMenu = new SP\Menu\MenuInclusion();
        $SPMenu->MenuInclude($app);
        $app->render('station/userautodj.phtml', compact('license'));
        $sp_growl->writeGrowl('success', _('Stream'), '');

    }

    if (isset($_POST['streamAs']) AND $_POST['streamAs'] != '' AND $app->config('demo_mod') == false){
        $changer = explode(".", $_POST['streamAs']);

        if($changer[1] == 'mp3'){
            $streamAs = '%mp3(bitrate=128, id3v2=true)';
        }elseif($changer[1] == 'aac'){
            $streamAs = '%aac(channels=2, samplerate=44100, bitrate=128, adts=true)';
        }elseif($changer[1] == 'oog'){
            $streamAs = '%vorbis.abr(samplerate=44100, channels=2, bitrate=128, max_bitrate=192, min_bitrate=64)';
        }

        $servTrans = DB::queryFirstRow("SELECT icecast_serv_id, liquid_id  FROM icecast_rel WHERE id=%s", $changer['0']);
        DB::update('liquid', array(
            'streamAs' => $streamAs,
        ), "id=%s", $servTrans['liquid_id']);

        $liquid = new \core\liquidsoap\liquidsoap();
        $liquid->createLiquiteConf($changer['0']);



        $sp_growl = new core\sp_special\growl();
        $SPMenu = new SP\Menu\MenuInclusion();
        $SPMenu->MenuInclude($app);
        $app->render('station/userautodj.phtml', compact('license'));
        $sp_growl->writeGrowl('success', _('Streamoutput ') . $changer[1],  _(' Bitte den AutoDj, Neustarten! '));

    }




})->name('userrestricted');