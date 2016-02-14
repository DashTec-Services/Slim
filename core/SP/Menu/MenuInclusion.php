<?php
/**
 * Created by David Schomburg (DashTec - Services)
 *      www.dashtec.de
 *
 *  S:P (StreamersPanel)
 *  Support: http://board.streamerspanel.de
 *
 *  v 4.0.0
 *
 *  Kundennummer:   @KDNUM@
 *  Lizenznummer:   @RECHNR@
 *  Lizenz: http://login.streamerspanel.de/user/terms
 */

namespace SP\Menu;



class MenuInclusion
{


    public function MenuInclude ($app){
        $this->includeMainHeader($app);
        if(isset($_SESSION['group'])){
            $app->flash('success', _('Login erfolgreich'));
            $Users = \DB::query("SELECT * FROM accounts");
            if($_SESSION['group'] == 'adm'){
                $app->render('menu/panelHeader.phtml', compact('Users'));
                $app->render('menu/admin.phtml', compact('Users'));
            }elseif($_SESSION['group']== 'usr' ){
                $app->render('menu/panelHeader.phtml', compact('Users'));
                $app->render('menu/user.phtml', compact('Users'));
            }
        }else{
            $app->redirect('/logout', 303);
        }
    }


    public function includeMainHeader($app){
        $app->render('header.phtml');
    }










}


