<?php
/**
 * Created by PhpStorm.
 * User: Dave
 * Date: 24.05.2015
 * Time: 12:05
 */



$app->get('/settings', function () use ($app) {



    $conf = \DB::queryFirstRow("SELECT * FROM config WHERE id='1'");

    $projectsettings['welcomeText'] = $conf['welcomeMessage'];
    $projectsettings['prowotopic'] = $conf['proWoTopic'];
    $projectsettings['startVote'] = $conf['voteStart'];
    $projectsettings['stopVote'] = $conf['voteEnd'];
    $projectsettings['votersLogin'] = $conf['votersLogin'];
    $projectsettings['showVoteableProject'] = $conf['showVoteableProject'];
    $projectsettings['userLimit'] = $conf['userLimit'];
    $projectsettings['voteStartTime'] = $conf['voteStartTime'];
    $projectsettings['voteEndTime'] = $conf['voteEndTime'];

    $datetime = strtotime($projectsettings['startVote']);
    $projectsettings['startVote'] = date('d.m.Y', $datetime);

    $datetime = strtotime($projectsettings['stopVote']);
    $projectsettings['stopVote'] = date('d.m.Y', $datetime);



    $jsColletor = new \SP\Views\MyUltimateView();

    $jsColletor->clearJsFiles();
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/clockface/js/clockface.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-daterangepicker/moment.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.de.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js');


    $jsColletor = $jsColletor->getJSFiles();


    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('adm/settings.phtml', compact('error_message' , 'projectsettings', 'success_message'));

    $app->render('footer.phtml',compact('jsColletor'));


})->name('restricted');




$app->post('/settings', function () use ($app) {

    $error_message = false;
    $success_message = false;

    $delCheck1 = $app->request()->post('delCheck1');
    $delCheck2 = $app->request()->post('delCheck2');
    $delCheck3 = $app->request()->post('delCheck3');
    $votersLogin = $app->request()->post('votersLogin');
    $startVote = $app->request()->post('startVote');
    $stopVote = $app->request()->post('stopVote');
    $prowotopic = $app->request()->post('prowotopic');
    $welcomeText = $app->request()->post('welcomeText');
    $showVoteableProject = $app->request()->post('showVoteableProject');
    $userLimit = $app->request()->post('userLimit');
    $voteStartTime = $app->request()->post('voteStartTime');
    $voteEndTime = $app->request()->post('voteEndTime');

    # add VAR to Arry for ErrorReporting
    $projectsettings['welcomeText'] = $welcomeText;
    $projectsettings['prowotopic'] = $prowotopic;
    $projectsettings['startVote'] = $startVote;
    $projectsettings['stopVote'] = $stopVote;
    $projectsettings['votersLogin'] = $votersLogin;
    $projectsettings['showVoteableProject'] = $showVoteableProject;
    $projectsettings['userLimit'] = $userLimit;
    $projectsettings['voteStartTime'] = $voteStartTime;
    $projectsettings['voteEndTime'] = $voteEndTime;



    # Prüfung ob gelöscht werden soll

    if(isset($delCheck1) && isset($delCheck2) && isset($delCheck3) AND $_SESSION['deltheStorage'] == false){
        $error_message ='ACHTUNG!!! Es werden alle Daten GELÖSCHT!!!  ---->bitte wiederholen!';
        $_SESSION['deltheStorage'] = 1;
    }elseif(isset($_SESSION['deltheStorage']) AND $_SESSION['deltheStorage']  == true AND isset($delCheck1) && isset($delCheck2) && isset($delCheck3)){
        $error_message ='Alles wurde gelöscht!';
        session_unset($_SESSION['deltheStorage']);

        DB::update('config', array(
            'votersLogin' => '0',
            'voteStart' => date('Y-m-d'),
            'voteEnd' => date('Y-m-d'),
            'showVoteableProject' => '0',
            'voteStartTime' => '00:00:00',
            'voteEndTime' => '00:00:00',
            'userLimit' => '0',
            'proWoTopic' => 'Titel der Projektwoche',
            'welcomeMessage' => 'Nachricht für die Schüler (auf der Startseite).',
        ), "id=%s", '1');

    }else{
        if(isset($_SESSION['deltheStorage'])){
            session_unset($_SESSION['deltheStorage']);
        }
    }

    # Wahl - Datum setzen
    function valiDate($date)
    {
        if (!preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', trim($date), $matches)) {
            return false;
        }
        return checkdate($matches[2], $matches[1], $matches[3]);
    }

    if (!valiDate($startVote) OR !valiDate($stopVote)) {
       $error_message ='Ungültiges Datum!';
    }

    if($startVote > $stopVote){
        $error_message ='Ungültiges Datum!';
    }

    if(empty($welcomeText) or empty($prowotopic)){
        $error_message ='Bitte Thema und Text eintragen!';
    }



if($error_message == false){

    $datetime = strtotime($startVote);
    $startVote = date('Y-m-d', $datetime);

    $datetime = strtotime($stopVote);
    $stopVote = date('Y-m-d', $datetime);

    if($votersLogin == 'on'){
        $votersLogin = true;
    }else{
        $votersLogin = false;
    }

    if($userLimit == 'on'){
        $userLimit = true;
    }else{
        $userLimit = false;
    }


    if($showVoteableProject == 'on'){
        $showVoteableProject = true;
    }else{
        $showVoteableProject = false;
    }

    DB::update('config', array(
        'votersLogin' => $votersLogin,
        'voteStart' => $startVote,
        'voteEnd' => $stopVote,
        'showVoteableProject' => $showVoteableProject,
        'proWoTopic' => $prowotopic,
        'voteStartTime' => $voteStartTime,
        'voteEndTime' => $voteEndTime,
        'userLimit' => $userLimit,
        'welcomeMessage' => $welcomeText,
    ), "id=%s", '1');
    $success_message = true;
}

    $conf = \DB::queryFirstRow("SELECT * FROM config WHERE id='1'");

    $projectsettings['welcomeText'] = $conf['welcomeMessage'];
    $projectsettings['prowotopic'] = $conf['proWoTopic'];
    $projectsettings['startVote'] = $conf['voteStart'];
    $projectsettings['stopVote'] = $conf['voteEnd'];
    $projectsettings['votersLogin'] = $conf['votersLogin'];
    $projectsettings['showVoteableProject'] = $conf['showVoteableProject'];
    $projectsettings['userLimit'] = $conf['userLimit'];


    $datetime = strtotime($projectsettings['startVote']);
    $projectsettings['startVote'] = date('d.m.Y', $datetime);

    $datetime = strtotime($projectsettings['stopVote']);
    $projectsettings['stopVote'] = date('d.m.Y', $datetime);

    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('adm/settings.phtml', compact('error_message' , 'projectsettings', 'success_message'));

    $jsColletor = new \SP\Views\MyUltimateView();

    $jsColletor->clearJsFiles();
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/clockface/js/clockface.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-daterangepicker/moment.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.de.js');


    $jsColletor = $jsColletor->getJSFiles();


    $app->render('footer.phtml',compact('jsColletor'));

})->name('restricted');