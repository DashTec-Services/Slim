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
namespace core\url;


class urledit {

    public function getUrlParm($URL){ // $_SERVER ['REQUEST_URI']

        $schluesselwoerter = preg_split("/\//", $URL);

        unset($schluesselwoerter[0]);
        return $schluesselwoerter;
    }
}