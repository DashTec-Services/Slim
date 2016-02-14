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
# Einfaches erstellen von sicheren Passswörtern. Dabei is der "salt" Key jeweils zu ersetzen! min. 22 Char!

namespace core\password;

class password
{
    public static $options = ['cost' => 7, 'salt' => 'EDGFgagha23562398aSAFPÖEQqwdweaaw'];

    public static function createPassword($clearTextPassword)
    {
        $hash = password_hash($clearTextPassword, PASSWORD_BCRYPT, static::$options);

        return $hash;
    }

    public static function verifyPassword($clearTextPassword, $hash)
    {
        return password_verify($clearTextPassword, $hash);
    }

    public function generatePassword($length = 5, $capitals = true, $specialSigns = false)
    {
        $array = array();


        if($length < 8)
            $length = mt_rand(8,20);

        # Zahlen
        for($i=48;$i<58;$i++)
            $array[] = chr($i);

        # kleine Buchstaben
        for($i=97;$i<122;$i++)
            $array[] = chr($i);

        # Großbuchstaben
        if($capitals )
            for($i=65;$i<90;$i++)
                $array[] = chr($i);

        # Sonderzeichen:
        if($specialSigns)
        {
            for($i=33;$i<47;$i++)
                $array[] = chr($i);
            for($i=59;$i<64;$i++)
                $array[] = chr($i);
            for($i=91;$i<96;$i++)
                $array[] = chr($i);
            for($i=123;$i<126;$i++)
                $array[] = chr($i);
        }

        mt_srand((double)microtime()*1000000);
        $passwort = '';

        for ($i=1; $i<=$length; $i++)
        {
            $rnd = mt_rand( 0, count($array)-1 );
            $passwort .= $array[$rnd];
        }

        return $passwort;
    }
}