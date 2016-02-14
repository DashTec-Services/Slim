<?php
/**
 * Created by David Schomburg (DashTec - Services)
 *      www.dashtec.de
 *
 *  S:P (StreamersPanel)
 *  Support: http://board.streamerspanel.de
 *
 *  v 0.13
 *
 *  Kundennummer:   @KDNUM@
 *  Lizenznummer:   @RECHNR@
 *  Lizenz: http://login.streamerspanel.de/user/terms
 */
namespace core\date;


class date {


    function date2timestamp($a = '')
    {
        if (empty($a)) return;
        $a = explode('.', $a);
        return mktime(0, 0, 0, $a[1], $a[0], $a[2]);
    }


    function anzahlKalenderWochen($jahr) // Gibt die Anzahl der Kalenderwochen eines gegebenen Jahrs (Format YYYY) zurück
    {
        $letzteKW = date("W", strtotime("31.12." . $jahr));
        $anzahlKW = ($letzteKW == 1) ? 52 : $letzteKW;
        return $anzahlKW;
    }


    function firstkw($jahr)
    {
        $erster = mktime(0, 0, 0, 1, 1, $jahr);
        $wtag = date('w', $erster);
        if ($wtag <= 4) {
            /** * Donnerstag oder kleiner: auf den Montag zurückrechnen. */
            $montag = mktime(0, 0, 0, 1, 1 - ($wtag - 1), $jahr);
        } else {
            /** * auf den Montag nach vorne rechnen. */
            $montag = mktime(0, 0, 0, 1, 1 + (7 - $wtag + 1), $jahr);
        }
        return $montag;
    }

    function mondaykw($kw, $jahr)
    {
        $firstmonday = firstkw($jahr);
        $mon_monat = date('m', $firstmonday);
        $mon_jahr = date('Y', $firstmonday);
        $mon_tage = date('d', $firstmonday);
        $tage = ($kw - 1) * 7;
        $mondaykw = mktime(0, 0, 0, $mon_monat, $mon_tage + $tage, $mon_jahr);
        return $mondaykw;
    }

    function date_german2mysql($date) {
        $d    =    explode(".",$date);

        return    sprintf("%04d-%02d-%02d", $d[2], $d[1], $d[0]);
    }

    function date_mysql2german($date) {
        $d    =    explode("-",$date);

        return    sprintf("%02d.%02d.%04d", $d[2], $d[1], $d[0]);
    }



}