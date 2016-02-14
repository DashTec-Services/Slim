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
namespace core\usercontrol;

use core\password\password;

class user
{

    public function addUserToDb($VAR, $UNSET_VAR_NAME)
    {

        #Passwort absichern
        $PassSec = new password();
        $passwort = $PassSec->createPassword($VAR['0']['password']);

        #Neues Passwort an Array
        $VAR['0']['password'] = $passwort;

        #Senden var löschen
        unset($VAR['0'][$UNSET_VAR_NAME]);
        #User in die DB eintragen
        \DB::insert('accounts', $VAR);

    }

    public function updateUserToDb($VAR, $id)
    {
        #Passwort absichern
        $PassSec = new password();

        if (!empty($VAR['0']['password'])) {
            $passwort = $PassSec->createPassword($VAR['0']['password']);
            #Neues Passwort an Array
            $VAR['0']['password'] = $passwort;
        } else {
            unset($VAR['0']['password']);
        }

        #Senden var löschen
        unset($VAR['0']['update']);
        #User in die DB eintragen
        \DB::update('accounts', $VAR, "id=%s", $id);
    }

    public function delUserToDb($id)
    {
        #User aus der DB löschen
        \DB::delete('accounts', "id=%s", $id);
    }

    public function  checkTypo($var)
    {
        $var = htmlentities($var);
        return $var;
    }

}