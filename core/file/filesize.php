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

namespace core\file;

class filesize{

    public function format_filesize($size) {
        $arr_units = array(
            'KB',
            'Kibi',
            'MB',
            'GB',
            'TB',
            'TB'
        );
        for ($i = 0; $size > 1024; $i++) {
            $size /= 1024;
        }
        return number_format($size, 2, ',', '').' '.$arr_units[$i];
    }





}