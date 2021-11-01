<?php
namespace Console\App;

use Console\App\ProductsupConfig;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ProductsupLogger 
{
    private static $instance;
	private static $logger;

	private function __construct(){
	   self::$logger = new Logger('Productsup');
	}
	
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	public function getLogger(){
	
		return self::$logger->pushHandler(new StreamHandler(ProductsupConfig::getInstance()->getappLogPath(), Logger::DEBUG));
		
	}
}