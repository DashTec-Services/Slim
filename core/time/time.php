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
namespace core\time;


class time {


    # Alte Zeitformate
   public function onlineTime($time) {
        $time=explode(':',$time);

       $sec = '0';
       $sec = $time[0] * 60;
       $sec = $sec + $time[1];

        return $sec;
    }

    public function seconds2Time($sekunden)
    {
        $zeit['jhr'] = floor($sekunden / 31536000);
        $zeit['tag'] = floor(($sekunden%31536000) / 86400);
        $zeit['std'] = floor(($sekunden%86400) / 3600);
        $zeit['min'] = floor(($sekunden%3600) / 60);
        $zeit['sek'] = $sekunden%60;

        return "".$zeit['std'].":".$zeit['min'].":".$zeit['sek'];

    }

    public function seconds2TimeDay($sekunden)
    {
        $zeit['jhr'] = floor($sekunden / 31536000);
        $zeit['tag'] = floor(($sekunden%31536000) / 86400);
        $zeit['std'] = floor(($sekunden%86400) / 3600);
        $zeit['min'] = floor(($sekunden%3600) / 60);
        $zeit['sek'] = $sekunden%60;

        return $zeit['tag']." Tage  ".$zeit['std']." Stunden
               ".$zeit['min']." Minuten ".$zeit['sek']." Sekunden";

    }



}