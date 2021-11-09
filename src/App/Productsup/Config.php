<?php

namespace Console\App\Productsup;

class Config
{
    private static $instance;
    private static $appRoot;
    private static $appLogPath;
    private static $defaultDataFile;
    private static $dataFeedFolder;
    private static $googleApiCredentialsFile;

    private function __construct()
    {
        self::$appRoot = realpath(__DIR__.'/../../..');
        self::$appLogPath = self::$appRoot.'data/logs/productsup_application.log';
        self::$dataFeedFolder = self::$appRoot.'/data/feed/';
        self::$defaultDataFile = 'coffee_feed.xml';
        self::$googleApiCredentialsFile = self::$appRoot.'/configs/google_api_service_account/credentials.json';
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getappLogPath()
    {
        return self::$appLogPath;
    }
    public function getdefaultDataFile()
    {
        return self::$defaultDataFile;
    }

    public function getgoogleApiCredentialsFile()
    {
        return self::$googleApiCredentialsFile;
    }

    public function getdataFeedFolder()
    {
        return self::$dataFeedFolder;
    }
}
