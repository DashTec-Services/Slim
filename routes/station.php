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

/*
 *      ADMIN ROUTES
 */



$app->get('/station/admedit', function() use ($app){
    $app->render('station/admineditstream.phtml', compact('license'));
    # Demoeinstellungen
    $demo = new \core\demo\demomod();
    $demo->CheckDemo($app->config('demo_mod'));
})->name('restricted');

$app->get('/station/list', function() use ($app){
    unset($_SESSION['stationAddmerker']);
    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('station/adminshowlist.phtml', compact('license'));

    # Demoeinstellungen
    $demo = new \core\demo\demomod();
    $demo->CheckDemo($app->config('demo_mod'));
})->name('restricted');

$app->get('/station/showstream', function() use ($app){
    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('station/usershowstreams.phtml', compact('license'));

    # Demoeinstellungen
    $demo = new \core\demo\demomod();
    $demo->CheckDemo($app->config('demo_mod'));
})->name('user');

/*
 *      USER ROUTES
 */

$app->get('/station/autodj', function() use ($app){
    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('station/userautodj.phtml', compact('license'));

    # Demoeinstellungen
    $demo = new \core\demo\demomod();
    $demo->CheckDemo($app->config('demo_mod'));


})->name('license');

$app->get('/station/editserv', function() use ($app){
    $app->render('station/usereditserver.phtml', compact('license'));
    # Demoeinstellungen
    $demo = new \core\demo\demomod();
    $demo->CheckDemo($app->config('demo_mod'));
})->name('license');

# USER - POST Action SC_Serv
$app->post('/station/showstream', function () use ($app) {

    $trans = new core\sp_special\sctrans();
    $serv = new core\sp_special\scserv();
    $sp_growl = new core\sp_special\growl();


    # Starten der Server + Transcoder nach ReBoot
    if (isset($_POST['rebooton']) AND $app->config('demo_mod') == false) {
        $pid = DB::query("SELECT * FROM sc_rel WHERE sc_serv_pid!='0' ");

        foreach ($pid as $row) {
            $serv->startSc_Serv($row['id']);
            $trans->startSc_Trans($row['id']);
        }
        $SPMenu = new SP\Menu\MenuInclusion();
        $SPMenu->MenuInclude($app);
        $app->render('station/adminshowlist.phtml', compact('license'));
    }

    # Alle starten + Stoppen
    if (isset($_POST['switch']) AND $app->config('demo_mod') == false) {
        if ($_POST['switch'] == 'off') {
            $pid = DB::query("SELECT * FROM sc_rel WHERE sc_serv_pid!='0' ");
            foreach ($pid as $row) {
                $serv->killSc_Serv($row['id']);
            }
        }

        if ($_POST['switch'] == 'on') {
            $pid = DB::query("SELECT * FROM sc_rel WHERE sc_serv_pid='0' ");
            foreach ($pid as $row) {
                $serv->startSc_Serv($row['id']);
            }
        }
        $SPMenu = new SP\Menu\MenuInclusion();
        $SPMenu->MenuInclude($app);
        $app->render('station/adminshowlist.phtml', compact('license'));
    }

    # Server + Transc conf sichern nach bearbeitung
    if (isset($_POST['servconfsave']) AND $app->config('demo_mod') == false) {

        $serv_id = DB::queryFirstRow("SELECT * FROM sc_rel WHERE id=%s", $_SESSION['sec_rel_id']);

        DB::update('sc_trans_conf', array(
            'unlockkeyname' => $_POST['unlockkeyname'],
            'unlockkeycode' => $_POST['unlockkeycode'],
            'irc' => $_POST['irc'],
            'icq' => $_POST['icq'],
            'aim' => $_POST['aim'],
            'streamtitle' => $_POST['streamtitle'],
            'streamurl' => $_POST['streamurl'],
            'encoder_1' => $_POST['encoder_1'],
            'genre' => $_POST['genre'],
            'djpassword' => $_POST['djpassword'],
            'shuffle' => $_POST['shuffle'],
            'public' => $_POST['public']

        ), "id=%s", $serv_id['sc_trans_id']);

        DB::update('sc_serv_conf', array(
            'PublicServer' => $_POST['PublicServer'],
            'URLFormat' => $_POST['URLFormat'],
            'TitleFormat' => $_POST['TitleFormat']
        ), "id=%s", $serv_id['sc_serv_conf_id']);

        DB::update('sc_rel', array(
            'stream_userName' => $_POST['stream_userName']
        ), "id=%s", $_SESSION['sec_rel_id']);


        # Laden der Übersicht nach Änderungen
        if($_SESSION['group'] == 'adm'){
            $SPMenu = new SP\Menu\MenuInclusion();
            $SPMenu->MenuInclude($app);
            $app->render('station/adminshowlist.phtml', compact('license'));
        }elseif($_SESSION['group'] == 'user'){
            $SPMenu = new SP\Menu\MenuInclusion();
            $SPMenu->MenuInclude($app);
            $app->render('station/usershowstreams.phtml', compact('license'));
        }


    }

    # Server + Transc conf sichern nach bearbeitung und Neu-Starten
    if (isset($_POST['editsrvandreboot']) AND $app->config('demo_mod') == false) {

        $serv_id = DB::queryFirstRow("SELECT * FROM sc_rel WHERE id=%s", $_SESSION['sec_rel_id']);

        DB::update('sc_trans_conf', array(
            'unlockkeyname' => $_POST['unlockkeyname'],
            'unlockkeycode' => $_POST['unlockkeycode'],
            'irc' => $_POST['irc'],
            'icq' => $_POST['icq'],
            'aim' => $_POST['aim'],
            'streamtitle' => $_POST['streamtitle'],
            'streamurl' => $_POST['streamurl'],
            'encoder_1' => $_POST['encoder_1'],
            'genre' => $_POST['genre'],
            'public' => $_POST['public'],
            'shuffle' => $_POST['shuffle'],
            'djpassword' => $_POST['djpassword']
        ), "id=%s", $serv_id['sc_trans_id']);

        DB::update('sc_serv_conf', array(
            'PublicServer' => $_POST['PublicServer'],
            'URLFormat' => $_POST['URLFormat'],
            'TitleFormat' => $_POST['TitleFormat']
        ), "id=%s", $serv_id['sc_serv_conf_id']);

        DB::update('sc_rel', array(
            'stream_userName' => $_POST['stream_userName']
        ), "id=%s", $_SESSION['sec_rel_id']);

        // And Now Reboot SRV + Transcoder

        $serv->killSc_Serv($_SESSION['sec_rel_id']);
        $trans->killSc_Trans($_SESSION['sec_rel_id']);
        $serv->startSc_Serv($_SESSION['sec_rel_id']);
        $trans->startSc_Trans($_SESSION['sec_rel_id']);

        # Laden der Übersicht nach Änderungen
        if($_SESSION['group'] == 'adm'){
            $SPMenu = new SP\Menu\MenuInclusion();
            $SPMenu->MenuInclude($app);
            $app->render('station/adminshowlist.phtml', compact('license'));
        }elseif($_SESSION['group'] == 'user'){
            $SPMenu = new SP\Menu\MenuInclusion();
            $SPMenu->MenuInclude($app);
            $app->render('station/usershowstreams.phtml', compact('license'));
        }
    }

    # Start - Stop Server           # USER Befehl
    if (isset($_POST['onoffselc']) AND $app->config('demo_mod') == false ) {
        $changer = explode(".", $_POST['onoffselc']);


        if ($changer['1'] == '1') {

            #$serv->startSc_Serv($changer['0']);

            $serv->writeStationConf($changer['0']);


            $config = \DB::queryFirstRow("SELECT * FROM config WHERE id='1'");

            $SSHConf['ip'] = $config['server_ip'];
            $SSHConf['user'] = $config['root_user'];
            $SSHConf['pass'] = $config['root_password'];
            $SSHConf['port'] = $config['ssh_port'];
            $connection = ssh2_connect($SSHConf['ip'], $SSHConf['port']);
            ssh2_auth_password($connection, $SSHConf['user'], $SSHConf['pass']);
            // Stream Conf
            $sc_serv_rel = \DB::queryFirstRow("SELECT * FROM sc_rel WHERE id=%s", $changer['0']);

            // StreamDaten
            $sc_serv_id = \DB::queryFirstRow("SELECT * FROM sc_serv_conf WHERE id=%s", $sc_serv_rel['sc_serv_conf_id']);

            $scservname = \DB::queryFirstRow("SELECT file_name FROM sc_version WHERE id=%s", $sc_serv_rel['sc_serv_version_id']);

            // STREAMPORT
            $streamPort = $sc_serv_id['PortBase'];
            $ssh2_exec_com = ssh2_exec($connection, $_SERVER['DOCUMENT_ROOT'] . '/shoutcast/./' . $scservname['file_name'] . ' ' . $_SERVER['DOCUMENT_ROOT'] . '/streamconf/'.$streamPort.'/sc_serv.conf </dev/null 2>/dev/null >/dev/null & echo $!');



            #$ssh2_exec_com = ssh2_exec($connection, $_SERVER['DOCUMENT_ROOT'] . '/shoutcast/./1.9.8_sc_serv.bin ' . $_SERVER['DOCUMENT_ROOT'] . '/streamconf/8080/sc_serv.conf </dev/null 2>/dev/null >/dev/null & echo $!');
            sleep(3);

            $pid = stream_get_contents($ssh2_exec_com);
            \DB::update('sc_rel', array(
                'sc_serv_pid' => $pid
            ), "id=%s", $changer['0']);

            # Laden der Übersicht nach Änderungen
            if($_SESSION['group'] == 'adm'){
                $SPMenu = new SP\Menu\MenuInclusion();
                $SPMenu->MenuInclude($app);
                $app->render('station/adminshowlist.phtml', compact('license'));
                $sp_growl->writeGrowl('success',  _('Server gestartet'), '');

            }elseif($_SESSION['group'] == 'user'){
                $SPMenu = new SP\Menu\MenuInclusion();
                $SPMenu->MenuInclude($app);
                $app->render('station/usershowstreams.phtml', compact('license'));
                $sp_growl->writeGrowl('success',  _('Server gestartet'), '');
            }

        } elseif ($changer['1'] == '0') {
            $serv->killSc_Serv($changer['0']);
            # Laden der Übersicht nach Änderungen
            if($_SESSION['group'] == 'adm'){
                $SPMenu = new SP\Menu\MenuInclusion();
                $SPMenu->MenuInclude($app);
                $app->render('station/adminshowlist.phtml', compact('license'));
            }elseif($_SESSION['group'] == 'user'){
                $SPMenu = new SP\Menu\MenuInclusion();
                $SPMenu->MenuInclude($app);
                $app->render('station/usershowstreams.phtml', compact('license'));
            }
            $sp_growl->writeGrowl('info',  _('Server gestoppt'), '');
        }


    }

    # Laden der Konfiguration für die Ausgabe der Servereinstellungen
    # Verwendung in adminshowlist
    if (isset($_POST['changeConfServ'])) {
        # Übergabe der Action an VAR
        $changer = explode(".", $_POST['changeConfServ']);

        # Wenn Action == edit
        if ($changer[1] == 'edit') {
            $changer = explode(".", $_POST['changeConfServ']);
            // TODO: Sichern prüfen ob Server den User gehört!
            $_SESSION['sec_rel_id'] = $_POST['changeConfServ'];

            if($_SESSION['group'] == 'adm'){
                $SPMenu = new SP\Menu\MenuInclusion();
                $SPMenu->MenuInclude($app);
                $sc_rel = DB::queryFirstRow("SELECT * FROM sc_rel WHERE id=%s", $changer['0']);
                $sc_serv = DB::queryFirstRow("SELECT * FROM sc_serv_conf WHERE id=%s", $sc_rel['sc_serv_conf_id']);
                $sc_trans = DB::queryFirstRow("SELECT * FROM sc_trans_conf WHERE id=%s", $sc_rel['sc_trans_id']);
                $app->render('station/admineditstream.phtml', compact('sc_serv', 'sc_trans', 'sc_rel'));
                # Demoeinstellungen
                $demo = new \core\demo\demomod();
                $demo->CheckDemo($app->config('demo_mod'));

            }elseif($_SESSION['group'] == 'user'){
                $SPMenu = new SP\Menu\MenuInclusion();
                $SPMenu->MenuInclude($app);
                $sc_rel = DB::queryFirstRow("SELECT * FROM sc_rel WHERE id=%s", $changer['0']);
                $sc_serv = DB::queryFirstRow("SELECT * FROM sc_serv_conf WHERE id=%s", $sc_rel['sc_serv_conf_id']);
                $sc_trans = DB::queryFirstRow("SELECT * FROM sc_trans_conf WHERE id=%s", $sc_rel['sc_trans_id']);
                $app->render('station/usereditserver.phtml', compact('sc_serv', 'sc_trans', 'sc_rel'));
                # Demoeinstellungen
                $demo = new \core\demo\demomod();
                $demo->CheckDemo($app->config('demo_mod'));
            }
        }

        # Wenn Action == clear
        if ($changer[1] == 'clear' AND $app->config('demo_mod') == false  ) {
            $changer = explode(".", $_POST['changeConfServ']);
            // TODO: Sichern prüfen ob Server den User gehört!
            $_SESSION['sec_rel_id'] = $_POST['changeConfServ'];
            $sc_rel = DB::queryFirstRow("SELECT * FROM sc_rel WHERE id=%s", $changer['0']);
            $serverPort = DB::queryFirstRow("SELECT * FROM sc_serv_conf WHERE id=%s", $sc_rel['sc_serv_conf_id']);
            DB::delete('sc_rel', "id=%s", $changer['0']);
            DB::delete('sc_serv_conf', "id=%s", $sc_rel['sc_serv_conf_id']);
            DB::delete('sc_trans_conf', "id=%s", $sc_rel['sc_trans_id']);

            // Ordner löschen
            $config = DB::queryFirstRow("SELECT doc_root FROM config WHERE id=%s", '1');
            $DocRoot = $config['doc_root'];
            $FolderDir = $DocRoot . "/streamconf/" . $serverPort['PortBase'];
            function deleteDirectory($dir) {
                if (!file_exists($dir)) return true;
                if (!is_dir($dir)) return unlink($dir);
                foreach (scandir($dir) as $item) {
                    if ($item == '.' || $item == '..') continue;
                    if (!deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) return false;
                }
                return rmdir($dir);
            }
            deleteDirectory($FolderDir);

            $SPMenu = new SP\Menu\MenuInclusion();
            $SPMenu->MenuInclude($app);
            $sc_rel = DB::queryFirstRow("SELECT * FROM sc_rel WHERE id=%s", $changer['0']);
            $sc_serv = DB::queryFirstRow("SELECT * FROM sc_serv_conf WHERE id=%s", $sc_rel['sc_serv_conf_id']);
            $sc_trans = DB::queryFirstRow("SELECT * FROM sc_trans_conf WHERE id=%s", $sc_rel['sc_trans_id']);
            $app->render('station/adminshowlist.phtml', compact('sc_serv', 'sc_trans', 'sc_rel'));
            $sp_growl->writeGrowl('success', _('Server gelöscht'), '');

        }

        # Benutzer eines Streams ändern
        if ($changer[1] == 'changeUser'){

            $_SESSION['StreamID'] = $changer[0];
            # Seite laden
            $SPMenu = new SP\Menu\MenuInclusion();
            $SPMenu->MenuInclude($app);
            $sc_rel = DB::queryFirstRow("SELECT * FROM sc_rel WHERE id=%s", $changer['0']);
            $sc_serv = DB::queryFirstRow("SELECT * FROM sc_serv_conf WHERE id=%s", $sc_rel['sc_serv_conf_id']);
            $sc_trans = DB::queryFirstRow("SELECT * FROM sc_trans_conf WHERE id=%s", $sc_rel['sc_trans_id']);
            $app->render('station/adminshowlist.phtml', compact('sc_serv', 'sc_trans', 'sc_rel'));

            $app->render('station/changeStreamUser.phtml', compact('sc_serv'));
        }
    }



})->name('usr');

$app->post('/station/changeowner', function () use ($app) {

    if (isset($_POST['updateUser'])){

        DB::update('sc_rel', array(
            'accounts_id' => $_POST['ownerchange']
        ), "id=%s", $_SESSION['StreamID']);

        # Seite laden
        $SPMenu = new SP\Menu\MenuInclusion();
        $SPMenu->MenuInclude($app);
        $sc_rel = DB::queryFirstRow("SELECT * FROM sc_rel WHERE id=%s", $_SESSION['StreamID']);
        $sc_serv = DB::queryFirstRow("SELECT * FROM sc_serv_conf WHERE id=%s", $sc_rel['sc_serv_conf_id']);
        $sc_trans = DB::queryFirstRow("SELECT * FROM sc_trans_conf WHERE id=%s", $sc_rel['sc_trans_id']);
        $app->render('station/adminshowlist.phtml', compact('sc_serv', 'sc_trans', 'sc_rel'));
    }
})->name('restricted');


# User POST SC_Trans
$app->post('/station/autodj', function () use ($app) {

# Neue Playliste übernehmen
    if (isset($_POST['playlstswitch']) AND $_POST['playlstswitch'] != '' AND $app->config('demo_mod') == false) {
        # Trennen der übergebenen Par.
        $changer = explode(".", $_POST['playlstswitch']);

        #changer 0 = sc_rel ID
        #changer 1 = playlist ID
        #changer 3 = Playlistname

        # Auslesen der conf ID
        $servTrans = DB::queryFirstRow("SELECT sc_serv_conf_id, sc_trans_id FROM sc_rel WHERE id=%s", $changer['0']);

        # Setzen der neuen ID
        \DB::update('sc_rel', array(
            'play_list_id' => $changer['1']
        ), "id=%s", $changer['0']);

        # Port abfragen
        $PortBase = DB::queryFirstRow("SELECT PortBase FROM sc_serv_conf WHERE id=%s", $servTrans['sc_trans_id']);

        # Eintragen der Playliste in die DB
        \DB::update('sc_trans_conf', array(
            'playlistfile' => $_SERVER['DOCUMENT_ROOT'] . '/streamconf/' . $PortBase['PortBase'] . '/' . $changer['2'] . '.lst'
        ), "id=%s", $servTrans['sc_trans_id']);

        $trans = new core\sp_special\sctrans();

        # Neue Konfiguration schreiben
        $trans->writeSc_TransConf($servTrans['sc_serv_conf_id']);

    }

    # Start - Stop Transcoder
    if (isset($_POST['djSwitch']) AND $app->config('demo_mod') == false) {
        $changer = explode(".", $_POST['djSwitch']);
        if ($changer['1'] == '1') {
            $trans = new \core\sp_special\sctrans();
            $trans->startSc_Trans($changer['0']);
        } elseif ($changer['1'] == '0') {
            $trans = new \core\sp_special\sctrans();
            $trans->killSc_Trans($changer['0']);
        }
    }


    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('station/userautodj.phtml', compact('license'));
})->name('doLogin');

# Funktionen für DJ - Benutzer
$app->post('/station/djfunction', function () use ($app) {
    # Start - Stop Transcoder
    if (isset($_POST['djSwitch']) AND $app->config('demo_mod') == false) {
        $changer = explode(".", $_POST['djSwitch']);
        if ($changer['1'] == '1') {
            $trans = new \core\sp_special\sctrans();
            $trans->startSc_Trans($changer['0']);
        } elseif ($changer['1'] == '0') {
            $trans = new \core\sp_special\sctrans();
            $trans->killSc_Trans($changer['0']);
        }
    }

    # DJ - Nächsten Song spielen
    if(isset($_POST['kickdj'])){
        $sc_trans_con = DB::queryFirstRow("SELECT * FROM sc_trans_conf WHERE id=%s", $_POST['kickdj']);
        $adminport = $sc_trans_con['adminport'];
        $username = $sc_trans_con['adminuser'];
        $password = $sc_trans_con['adminpassword'];
        $context = stream_context_create(array(
            'http' => array(
                'header'  => "Authorization: Basic " . base64_encode("$username:$password")
            )
        ));
        $sc_trans_con = DB::queryFirstRow("SELECT * FROM sc_trans_conf WHERE id=%s", $_POST['kickdj']);
        $ServerIp = DB::queryFirstRow("SELECT * FROM config WHERE id=%s",'1');
        $data = file_get_contents($ServerIp['server_ip'].':'.$adminport.'/kickdj', false, $context);
    }


    # DJ - Nächsten Song spielen
    if(isset($_POST['dj_nextSong'])){
        $sc_trans_con = DB::queryFirstRow("SELECT * FROM sc_trans_conf WHERE id=%s", $_POST['dj_nextSong']);
        $adminport = $sc_trans_con['adminport'];
        $username = $sc_trans_con['adminuser'];
        $password = $sc_trans_con['adminpassword'];
        $context = stream_context_create(array(
            'http' => array(
                'header'  => "Authorization: Basic " . base64_encode("$username:$password")
            )
        ));
        $ServerIp = DB::queryFirstRow("SELECT * FROM config WHERE id=%s",'1');
        $data = file_get_contents($ServerIp['server_ip'].':'.$adminport.'/nextsong', false, $context);
    }


    $SPMenu = new SP\Menu\MenuInclusion();
    $SPMenu->MenuInclude($app);
    $app->render('spwelcome/dj.phtml', compact('license'));
})->name('dj');