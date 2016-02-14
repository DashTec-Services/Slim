<?php

$app->get('/addUser', function () use ($app) {


    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('adm/admaddUser.phtml');

    $jsColletor = new \SP\Views\MyUltimateView();

    $jsColletor->clearJsFiles();
    $jsColletor->addJsFiles('../../assets/global/plugins/select2/select2.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');

    $jsColletor = $jsColletor->getJSFiles();

    $app->render('footer.phtml',compact('jsColletor'));

})->name('restricted');

$app->post('/addUser', function () use ($app) {


    $vname = $app->request()->post('vname');
    $surname = $app->request()->post('surname');
    $class = $app->request()->post('class');
    $username = $app->request()->post('username');
    $projectname = $app->request()->post('projectname');
    $bday = $app->request()->post('bday');

    $projectsettings['vname'] = $vname;
    $projectsettings['surname'] = $surname;
    $projectsettings['class'] = $class;
    $projectsettings['username'] = $username;
    $projectsettings['bday'] = $bday;



    if (!empty($vname) AND !empty($surname) AND !empty($class) AND !empty($username) AND !empty($bday)){

        $bday = str_replace(".", "", $bday);
        $password = new \core\password\password();


        $account = DB::queryFirstRow("SELECT * FROM users WHERE surename=%s AND vname=%s", $surname, $vname );

        if($account['vname'] != $vname AND $account['surename'] != $surname AND  $account['bday'] != $bday){
            DB::insert('users', array(
                'usrgroup' => 'usr',
                'vname' => $vname,
                'surename' => $surname,
                'loginnumb' => $username,
                'bday' => $password->createPassword($bday),
                'class' => preg_replace('/[^0-9]+/', '',  $class)
            ));
        }else{
            $error_message = 'Eintrag ist schon vorhanden!';
        }

    }else{
        $error_message = 'Bitte alle Daten eingeben!!';
    }

    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml', compact('error_message' , 'projectsettings' , 'success_message'));

    $app->render('adm/admaddUser.phtml');
    $jsColletor = new \SP\Views\MyUltimateView();

    $jsColletor->clearJsFiles();
    $jsColletor->addJsFiles('../../assets/global/plugins/select2/select2.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');

    $jsColletor = $jsColletor->getJSFiles();

    $app->render('footer.phtml',compact('jsColletor'));

})->name('restricted');





$app->get('/userList', function () use ($app) {


    $results = DB::query("SELECT * FROM users WHERE usrgroup='usr'");


    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('adm/admviewUser.phtml', compact('results'));


    $jsColletor = new \SP\Views\MyUltimateView();

    $jsColletor->clearJsFiles();
    $jsColletor->addJsFiles('../../assets/global/plugins/select2/select2.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');


    $jsColletor = $jsColletor->getJSFiles();


    $app->render('footer.phtml',compact('jsColletor'));


})->name('restricted');

$app->post('/userlistedit', function () use ($app) {





    if (isset($_POST['delUser'])){
        DB::delete('users', "id=%s", $_POST['delUser']);
        $voteExist = DB::queryFirstRow("SELECT * FROM voteRelas WHERE userId=%s", $_POST['delUser']);

        if ($voteExist['projectId'] == true){
            $voteProjec = DB::queryFirstRow("SELECT * FROM project WHERE id=%s", $voteExist['projectId']);
            $voteProjec['nowUsers']--;
            DB::update('project', array(
                'nowUsers' => $voteProjec['nowUsers']--
            ), "id=%s", $voteExist['projectId']);
        }

       DB::delete('voteRelas', "userId=%s", $_POST['delUser']);

    }


    if (isset($_POST['resetVote'])){
        DB::update('users', array(
            'voteDone' => '0'
        ), "id=%s", $_POST['resetVote']);

        $voteExist = DB::queryFirstRow("SELECT * FROM voteRelas WHERE userId=%s", $_POST['resetVote']);

        if ($voteExist['projectId'] == true) {
            $voteProjec = DB::queryFirstRow("SELECT * FROM project WHERE id=%s", $voteExist['projectId']);
            $voteProjec['nowUsers']--;
            DB::update('project', array(
                'nowUsers' => $voteProjec['nowUsers']--
            ), "id=%s", $voteExist['projectId']);

            DB::delete('voteRelas', "userId=%s", $_POST['resetVote']);
        }


    }

    $results = DB::query("SELECT * FROM users WHERE usrgroup='usr'");

    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('adm/admviewUser.phtml', compact('results'));


    $jsColletor = new \SP\Views\MyUltimateView();
    $jsColletor->clearJsFiles();
    $jsColletor->addJsFiles('../../assets/global/plugins/select2/select2.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js');
    $jsColletor->addJsFiles('../../assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');


    $jsColletor = $jsColletor->getJSFiles();

    $app->render('footer.phtml',compact('jsColletor'));

})->name('restricted');





$app->get('/import', function () use ($app) {

    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('adm/admImportFile.phtml');


    $app->render('footer.phtml');

})->name('restricted');

$app->post('/import', function () use ($app) {



    #echo '<pre>';
   # print_r($_FILES);

    $csv = new \core\parseCSV\parseCSV();
    $csv->auto($_FILES['uploadedFile']['tmp_name']);
    #print_r($csv->data);


    $passwort = new \core\password\password();

    if($_FILES['uploadedFile']['type'] == 'application/vnd.ms-excel'){
        foreach ($csv->data as $DatenImport){

            $DatenImport['Geburtsdatum'] = str_replace(".", "", $DatenImport['Geburtsdatum']);

            $account = DB::queryFirstRow("SELECT * FROM users WHERE surename=%s AND vname=%s", $DatenImport['Nachname'], $DatenImport['Vorname'] );

            if($account['vname'] != $DatenImport['Vorname'] AND $account['surename'] != $DatenImport['Nachname'] AND  $account['bday'] != $DatenImport['Geburtsdatum']){
                DB::insert('users', array(
                    'usrgroup' => 'usr',
                    'vname' => $DatenImport['Vorname'],
                    'surename' => $DatenImport['Nachname'],
                    'loginnumb' => $DatenImport['Merkmal B4'],
                    'bday' => $passwort->createPassword($DatenImport['Geburtsdatum']),
                    'class' => preg_replace('/[^0-9]+/', '',  $DatenImport['Klasse'])
                ));
            }

        }
    }else{
        $error_message = 'Der Dateityp ist leider falsch!';
    }


    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('adm/admImportFile.phtml', compact('error_message' , 'success_message'));



    $app->render('footer.phtml');

})->name('restricted');