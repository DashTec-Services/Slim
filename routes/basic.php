<?php




$app->get('/', function () use ($app) {

    if ($_SESSION['group'] == 'usr'){
        $app->render('header.phtml');
        $app->render('usrMenu.phtml');
        $app->render('sideBar.phtml');
        $app->render('home.phtml');
        $app->render('footer.phtml');
    }elseif($_SESSION['group'] == 'adm'){
        $app->render('header.phtml');
        $app->render('usrMenu.phtml');
        $app->render('adm/admsideBar.phtml');
        $app->render('/adm/home.phtml');
        $app->render('footer.phtml');
    }else{
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

            session_destroy();

        }else{
            \DB::insert('userLog', array(
                'user' => $_SERVER['HTTP_USER_AGENT']. ' '.$_SERVER['REMOTE_HOST'],
                'ip-address' =>$_SERVER['REMOTE_ADDR'],
                'otherData' => 'Zugriff auf nicht erlaubte Ressource'
            ));
            session_destroy();
        }

    }


})->name('both');

$app->get('/choice', function () use ($app) {

    $app->render('header.phtml');
    $app->render('usrMenu.phtml');
    $app->render('sideBar.phtml');



    if($_SESSION['voteDone'] == true){
        $yourVote= DB::queryFirstRow("SELECT * FROM voteRelas WHERE userId=%s", $_SESSION['account_id']);
        $yourVote= DB::queryFirstRow("SELECT * FROM project WHERE id=%s", $yourVote['projectId']);

      #  if(){
      #      $_SESSION['voteDone'] = false;
       #     $_SESSION['proofCheck'] = false;
      #  }else{

            $app->render('yourVote.phtml', compact('error_message' , 'yourVote' , 'success_message' ,'modelDialog'));
       # }

    }else{
        $_SESSION['proofCheck'] = false;
        $app->render('choice.phtml', compact('error_message' , 'results' , 'success_message' ,'modelDialog'));
    }


    # Wenn keine Vorwahl und keine Abstimmung stattgefunden hat dann Seite Wahl laden
    if(!isset($_SESSION['proofCheck']) AND $_SESSION['voteDone'] == false){
        $results = DB::queryFirstRow("SELECT * FROM project WHERE startClass=%s AND maxUsers > nowUsers", $_SESSION['startClass']);
        $app->render('choice.phtml', compact('error_message' , 'results' , 'success_message' ,'modelDialog'));
    }
 #   if ($_SESSION['proofCheck'] == false AND $_SESSION['voteDone'] == false){
 #       $results = DB::queryFirstRow("SELECT * FROM project WHERE startClass=%s AND maxUsers > nowUsers", $_SESSION['startClass']);
 #       $app->render('choice.phtml', compact('error_message' , 'results' , 'success_message' ,'modelDialog'));
 #   }

    if(isset($_SESSION['proofCheck']) AND $_SESSION['proofCheck']  == true AND isset($_SESSION['voteIdToSession']) AND $_SESSION['voteDone'] == false){
        $voteresult = DB::queryFirstRow("SELECT * FROM project WHERE id=%s", $_SESSION['voteIdToSession']);
        $app->render('checkVote.phtml', compact('error_message' , 'voteresult' , 'success_message' ,'modelDialog'));
    }



    $jsColletor = new \SP\Views\MyUltimateView();
    $jsColletor->clearJsFiles();

    $jsColletor->addJsFiles('../../assets/global/plugins/fuelux/js/spinner.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/jquery.input-ip-address-control-1.0.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/typeahead/handlebars.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/typeahead/typeahead.bundle.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/ckeditor/ckeditor.js');

    $jsColletor = $jsColletor->getJSFiles();

    $app->render('footer.phtml',compact('jsColletor'));

})->name('consumer');

$app->post('/choice', function () use ($app) {

    # Var von Vote1
    $Vote = $app->request()->post('radio1');


    # Laden der Header
    $app->render('header.phtml');
    $app->render('usrMenu.phtml');
    $app->render('sideBar.phtml');


    #   Prüfungen ob radio1 gedrückt wurde
    If(isset($_POST['radio1']) AND $_SESSION['voteDone'] == false){

        $VoteChecker = DB::queryFirstRow("SELECT * FROM project WHERE id=%s", $Vote);
        $ConfSettings = DB::queryFirstRow("SELECT * FROM config WHERE id=%s", '1');


        $_SESSION['voteIdToSession'] = $Vote;

        if($VoteChecker['maxUsers'] >  $VoteChecker['nowUsers'] AND $_SESSION['proofCheck'] == false OR $ConfSettings['userLimit'] == 0 ){
            $success_message = 'Bitte überprüfe deine Wahl!.';
            $_SESSION['proofCheck'] = true;

            $voteresult = DB::queryFirstRow("SELECT * FROM project WHERE id=%s", $Vote);
        }else{
            $error_message = 'Das Angebot ist leider Vergeben.';
            $_SESSION['proofCheck'] = false;
        }


    }



        # 2. if im Array    check MaxUsers Change DB usersers sucessMessage

        if(isset($_POST['doVote'])){
            $ConfSettings = DB::queryFirstRow("SELECT * FROM config WHERE id=%s", '1');


            $VoteChecker = DB::queryFirstRow("SELECT * FROM project WHERE id=%s",  $_SESSION['voteIdToSession']);
            if($VoteChecker['maxUsers'] >  $VoteChecker['nowUsers'] OR $ConfSettings['userLimit'] == 0){

                $success_message = 'Wir haben deine Wahl erhalten!.';
                $votersCounter = $VoteChecker['nowUsers'];

                DB::update('project', array(
                    'nowUsers' => ++$votersCounter
                ), "id=%s", $_SESSION['voteIdToSession']);

                                DB::update('users', array(
                                    'voteDone' => 1
                                ), "id=%s", $_SESSION['account_id']);

                                DB::insert('voteRelas', array(
                                    'userId' => $_SESSION['account_id'],
                                    'projectId' =>  $_SESSION['voteIdToSession']
                                ));

                $_SESSION['voteDone'] = true;
                $_SESSION['proofCheck'] = false;
                $_SESSION['voteIdToSession'] = false;
                $_SESSION['voteIdToSession'] = false;
            }else{
                $_SESSION['proofCheck'] = false;
                $_SESSION['voteIdToSession'] = false;
                $_SESSION['voteIdToSession'] = false;
                $error_message = 'Das Projekt ist leider schon voll!';
            }
        }



    # Wahl abbrechen
    if(isset($_POST['reset'])){
        $_SESSION['proofCheck'] = false;
        $_SESSION['voteIdToSession'] = false;
    }


        if($_SESSION['voteDone'] == true){
            $app->render('yourVote.phtml', compact('error_message' , 'voteresult' , 'success_message' ,'modelDialog'));
        }


        if($_SESSION['proofCheck'] == true AND isset($_SESSION['voteIdToSession']) AND $_SESSION['voteDone'] == false){
            $VoteChecker = DB::queryFirstRow("SELECT * FROM project WHERE id=%s", $_SESSION['voteIdToSession']);
            $app->render('checkVote.phtml', compact('error_message' , 'voteresult' , 'success_message' ,'modelDialog'));
        }

        # Wenn keine Vorwahl und keine Abstimmung stattgefunden hat dann Seite Wahl laden
        if ($_SESSION['proofCheck'] == false AND $_SESSION['voteDone'] == false){
            $app->render('choice.phtml', compact('error_message' , 'results' , 'success_message' ,'modelDialog'));
        }





    $jsColletor = new \SP\Views\MyUltimateView();
    $jsColletor->clearJsFiles();

    $jsColletor->addJsFiles('../../assets/global/plugins/fuelux/js/spinner.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/jquery.input-ip-address-control-1.0.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/typeahead/handlebars.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/typeahead/typeahead.bundle.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/ckeditor/ckeditor.js');

    $jsColletor = $jsColletor->getJSFiles();

    $app->render('footer.phtml',compact('jsColletor'));

})->name('consumer');

$app->get('/consumer', function () use ($app) {

    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('sideBar.phtml');

    $app->render('home.phtml');


    $app->render('footer.phtml');

})->name('consumer');
