<?php

namespace SlimSettingsLoader;



class SlimSettingsLoader
{

    /*
     *      Current Url from Adress
     *      @ string
     */
    static final function getCurrentUrl($pos){

        $rest = explode("/", $_SERVER['REQUEST_URI']);
        $rest = '/'.$rest[$pos];
        return $rest;
    }

    /*
     *      get AppVersoion from Slimconf
     */
    static final function getSettingsAppVersion($appSettings){

        $appSettings = $appSettings['app.version'];

        return $appSettings;
    }



}