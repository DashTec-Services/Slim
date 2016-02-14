<?php
/**
 * Created by PhpStorm.
 * User: Dave
 * Date: 27.04.2015
 * Time: 15:06
 */

/*
 *      @vars
 *      projectname
 *      projectleader
 *      maxpeo
 *      minclass
 *      maxclass
 *      roomwish
 *      projectdesc
 *      addproject
 */


$app->get('/addProject', function () use ($app) {

    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('adm/addProject.phtml');

    $app->render('footer.phtml');

})->name('restricted');

$app->post('/addProject', function () use ($app) {


    # Get POST @var
    $projectname = $app->request()->post('projectname');
    $projectleader = $app->request()->post('projectLeader');
    $maxpeo = $app->request()->post('maxpeo');
    $minclass = $app->request()->post('minclass');
    $maxclass = $app->request()->post('maxclass');
    $roomwish = $app->request()->post('roomwish');
    $projectdesc = $app->request()->post('projectdesc');
    $projectLand = $app->request()->post('projectLand');
    $projectNr = $app->request()->post('projectNr');


    if(isset($_POST['addproject']) OR isset($_POST['editproject'])){


        # add VAR to Arry for ErrorReporting
        $userdata['projectname'] = $projectname;
        $userdata['projectLeader'] = $projectleader;
        $userdata['maxpeo'] = $maxpeo;
        $userdata['minclass'] = $minclass;
        $userdata['maxclass'] = $maxclass;
        $userdata['roomwish'] = $roomwish;
        $userdata['projectdesc'] = $projectdesc;
        $userdata['projectLand'] = $projectLand;
        $userdata['projectNr'] = $projectNr;

        $error_message = false;

        if(is_numeric($maxpeo) == false OR is_numeric($minclass) == false OR is_numeric($maxclass) == false){

            $error_message = 'Es werden <b>NUR</b> - Numerische Werte zugelassen!';
        }

        if($maxclass < $minclass){
            $error_message = 'Die Angaben der Klasse sind nicht zulässig!';
        }
        if(empty($projectname) OR empty($projectleader) OR empty($projectLand) OR empty($projectNr) ){

            $error_message = 'Es werden <b>ALLE</b> - Angaben benötigt!';
        }


        if($error_message == false){

            # Prüfen ob Projekt vorhanden ist!

            if(isset($_POST['addproject'])){
                DB::insert('project', array(
                    'title' => $projectname,
                    'projectLeader' => $projectleader,
                    'maxUsers' => $maxpeo,
                    'startClass' => $minclass,
                    'endClass' => $maxclass,
                    'text' => $projectdesc,
                    'room' => $roomwish,
                    'projectLand' => $projectLand,
                    'projectNr' => $projectNr,
                    'nowUsers' => '0'
                ));
            }elseif(isset($_POST['editproject'])){
                DB::update('project', array(
                    'title' => $projectname,
                    'projectLeader' => $projectleader,
                    'maxUsers' => $maxpeo,
                    'startClass' => $minclass,
                    'endClass' => $maxclass,
                    'text' => $projectdesc,
                    'room' => $roomwish,
                    'projectLand' => $projectLand,
                    'projectNr' => $projectNr,
                ), "id=%s", $_SESSION['updateProject_id']);
            }

            $success_message = 'Projekt wurde angelegt.';
            unset($userdata);
        }

        $app->render('header.phtml');
        $app->render('usrMenu.phtml');
        $app->render('adm/admsideBar.phtml');
        # Prüfung ob ein Projekt nur editiert wird WENN JA unset Session
        if(isset($_SESSION['updateProject']) AND isset($_SESSION['updateProject_id'])){
            unset($_SESSION['updateProject']);
            unset($_SESSION['updateProject_id']);
            $results = DB::query("SELECT * FROM project");
            $app->render('adm/admchoice.phtml', compact('results'));
        }elseif(isset($_SESSION['updateProject']) OR isset($_SESSION['updateProject_id'])){
            unset($_SESSION['updateProject']);
            unset($_SESSION['updateProject_id']);
            $results = DB::query("SELECT * FROM project");
            $app->render('adm/admchoice.phtml', compact('results'));
        }else{
            $app->render('adm/addProject.phtml' , compact('error_message' , 'userdata', 'success_message'));
        }
        $app->render('footer.phtml');



    }



})->name('restricted');


$app->post('/admEditProjekt', function () use ($app) {


    if(isset($_POST['delProjekt'])){

        // TODO: Sicher??

        DB::delete('project', "id=%s", $_POST['delProjekt']);


        $results = DB::query("SELECT * FROM project");


        $app->render('header.phtml');

        $app->render('usrMenu.phtml');
        $app->render('adm/admsideBar.phtml');

        $app->render('adm/admchoice.phtml', compact('results'));


        $app->render('footer.phtml');





    }elseif(isset($_POST['editProjekt'])){

        $projectname = DB::queryFirstRow("SELECT * FROM project WHERE id=%s", $_POST['editProjekt']);

        $userdata['projectname'] = $projectname['title'];
        $userdata['projectLeader'] = $projectname['projectLeader'];
        $userdata['maxpeo'] = $projectname['maxUsers'];
        $userdata['minclass'] = $projectname['startClass'];
        $userdata['maxclass'] = $projectname['endClass'];
        $userdata['roomwish'] = $projectname['room'];
        $userdata['projectdesc'] = $projectname['text'];
        $userdata['projectLand'] = $projectname['projectLand'];
        $userdata['projectNr'] = $projectname['projectNr'];

        # setzen der EDIT sesion
        $_SESSION['updateProject'] = true;
        $_SESSION['updateProject_id'] = $projectname['id'];

        $app->render('header.phtml');

        $app->render('usrMenu.phtml');
        $app->render('adm/admsideBar.phtml');

        $app->render('adm/editProject.phtml' , compact('error_message' , 'userdata', 'success_message'));

        $app->render('footer.phtml');

    }


})->name('restricted');


$app->get('/importProj', function () use ($app) {

    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('adm/admImportProject.phtml');


    $app->render('footer.phtml');

})->name('restricted');

$app->post('/importProj', function () use ($app) {



    #echo '<pre>';
     #print_r($_FILES);

    $csv = new \core\parseCSV\parseCSV();
    $csv->auto($_FILES['uploadedFile']['tmp_name']);
     #print_r($csv->data);
    # Nr;Name;Land;Kategorie;Start-Klasse;End-Klasse;Beschreibung;Anzahl;Lehrer;Raumwunsch;
    if($_FILES['uploadedFile']['type'] == 'application/vnd.ms-excel'){

       # echo '<pre>';
        #print_r($csv->data);

        foreach ($csv->data as $DatenImport){

             #print_r($DatenImport);


                DB::insert('project', array(
                    'projectNr' => $DatenImport['Nr'],
                    'title' => $DatenImport['Name'],
                    'projectLand' => $DatenImport['Land'],
                    'startClass' => $DatenImport['Start-Klasse'],
                    'endClass' => $DatenImport['End-Klasse'],
                    'text' => $DatenImport['Beschreibung'],
                    'maxUsers' => $DatenImport['Anzahl'],
                    'nowUsers' => '0',
                    'projectLeader' => $DatenImport['Lehrer'],
                    'room' => $DatenImport['Raumwunsch']
                ));


        }
    }else{
        $error_message = 'Der Dateityp ist leider falsch!';
    }


    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('adm/admImportProject.phtml', compact('error_message' , 'success_message'));



    $app->render('footer.phtml');

})->name('restricted');