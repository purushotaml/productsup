<?php

namespace Console\App\Productsup;

use Console\App\Productsup\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ProductsUpLogger
{
    private static $instance;
    private static $logger;

    private function __construct()
    {
        self::$logger = new Logger('Productsup');
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getLogger()
    {
        return self::$logger->pushHandler(new StreamHandler(Config::getInstance()->getappLogPath(), Logger::DEBUG));
    }
}
