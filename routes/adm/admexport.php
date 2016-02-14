<?php
/**
 * Created by PhpStorm.
 * User: Dave
 * Date: 26.05.2015
 * Time: 10:14
 */





$app->get('/exporttoExcel', function () use ($app) {



    $results = DB::query("SELECT * FROM users ");

// using the results from the last query
// you get an array of associative arrays, so you can interate over the rows
// with foreach
    foreach ($results as $row) {
        echo $row['vname'] . ";";
        echo $row['surename'] . ";";
        echo $row['class'] . ";";
        $projectId = DB::queryFirstRow("SELECT * FROM voteRelas WHERE userId=%s", $row['id']);
        $projectName =DB::queryFirstRow("SELECT * FROM project WHERE id=%s", $projectId['projectId']);
        echo $projectName['title'] . ";"; // will be Joe, obviously
        echo"<br>";
    }













})->name('login');














$app->get('/getdata', function () use ($app) {


    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('/adm/admexport.phtml');


    $app->render('footer.phtml');


})->name('restricted');


$app->post('/getdata', function () use ($app) {


    require_once './core/TCPDF/TCPDF.php';
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->SetFont('helvetica', '', 9);


    $allUsers[] = '';
    $results = DB::query("SELECT * FROM project");

    foreach ($results as $votRels) {
        $pdf->AddPage();
        $Kursleiter = $votRels['projectLeader'];
        $ProjekLand = $votRels['projectLand'];
        $KursNummer = $votRels['projectNr'];
        $Class = $votRels['startClass'] . ' - ' . $votRels['endClass'];
        $MaxUsers = $votRels['maxUsers'];
        $NowUsers = $votRels['nowUsers'];
        $RestUsers = $MaxUsers - $NowUsers;
        $results = DB::query("SELECT * FROM voteRelas WHERE projectId=%s", $votRels['id']);
                $autoNumb = 1;
        foreach ($results as $row) {
            $account = DB::queryFirstRow("SELECT * FROM users WHERE id=%s", $row['userId']);

            $allUsers[] = '   <tr>
                                <td>' . $autoNumb++ . '</td>
                                <td>' . $account['vname'] . ' ' . $account['surename'] . '</td>
                                <td>' . $account['class'] . '</td>
                            </tr>';
        }


        $html = '<html>
<head></head>
<body>
<table border="0" cellspacing="0" cellpadding="0">
    <tr>
        <th><img src="http://www.raabe-schule.info/assets/wrs/img/wrs-logo.png" height="42" width="25"></th>
        <th></th>
        <th></th>
        <th><h1>' . $votRels['title'] . '</h1></th>
    </tr>
</table>

                        <h5><b>Kursleiter:</b> ' . $Kursleiter . '</h5><br>
                        <h5><b>Kurs-Nr:'.$KursNummer.'</b></h5>

                        <p>
                        <b>Land:</b> ' . $ProjekLand . '<br>

                        <b>Klasse:</b>' . $Class . '<br>
                        <b>Raumwunsch:</b>' . $votRels['room'] . '<br>
                        <b>Max. Teilnehmer:</b> ' . $MaxUsers . '<br>
                        <b>Teilnehmer (aktuell):</b> ' . $NowUsers . '<br>
                        <b>Restplätze:</b> ' . $RestUsers . '<br>
                        </p>
                        <table border="1" cellspacing="1" cellpadding="2">
                            <tr>
                                <th bgcolor="#cccccc" align="center"><b>Nr.</b></th>
                                <th bgcolor="#cccccc" align="center"><b>Schüler</b></th>
                                <th bgcolor="#cccccc" align="center"><b>Klasse</b></th>
                            </tr>
                            ';
        if (isset($allUsers) AND is_array($allUsers)) {
            foreach ($allUsers as $setAllData) {
                $html .= $setAllData;
            }
        }

        while ($NowUsers < $MaxUsers) {
            $html .= '      <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>';
            $NowUsers++;
        }

        $html .= '
                        </table>
</body>
</html>';
        $pdf->writeHTML($html, true, 0, true, 0);
        $pdf->lastPage();
        unset($allUsers);
    }

    $FileName = 'Export-' . date('d.m.Y-H:i');
    $pdf->Output($_SERVER['DOCUMENT_ROOT'] . 'blablub/' . $FileName . '.pdf', 'F');
    $app->render('header.phtml');

    $app->render('usrMenu.phtml');
    $app->render('adm/admsideBar.phtml');

    $app->render('/adm/admexport.phtml');


    $app->render('footer.phtml');
})->name('restricted');
