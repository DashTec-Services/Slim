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
namespace core\phparray;


class simplifyArray {


    /*      Multidimensionale Arrays, variabler Tiefe, in eindimensionale Array umwandeln
     *
     *              $array = array(1, 2, array('bli', 'bla', 'blub'));
     *              $output = array();
     *              simplifyArray($array, $output);
     */

   public function simplifyArray(array $array, array &$output) {
        foreach ($array as $v) {
            if (is_array($v)) {
                simplifyArray($v, $output);
            } else {
                $output[] = $v;
            }
        }
    }
}