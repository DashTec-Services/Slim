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
namespace core\demo;


use core\sp_special\growl;

class demomod extends growl {

     function CheckDemo($Session_Demo_Mod){
        if($Session_Demo_Mod == true)
        {
          self::writeGrowl('warning',_('DEMO-MODUS'),_('Starten, Stoppen, hinzufügen, bearbeiten und löschen nicht möglich! '));
        }
    }

} 