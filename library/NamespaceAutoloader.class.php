<?php
class NamespaceAutoloader {
    const BASE_DIR = __DIR__;
    const FILE_EXTENSION = '.php';

    public static function autoload($className) {

        $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $filePath = NamespaceAutoloader::BASE_DIR . DIRECTORY_SEPARATOR . $className . NamespaceAutoloader::FILE_EXTENSION;

        if (file_exists($filePath)) {
           include_once $filePath;
        }
    }
}