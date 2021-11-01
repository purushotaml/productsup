<?php
namespace Console\App\Google;

use Console\App\ProductsupConfig;
use Console\App\Google\AccountAuthorizationInterface;

class ServiceAccountAuthorization implements AccountAuthorizationInterface
{

	private $client;
	
	public function authorization()
	{		
	    $this->client = new \Google\Client();
		$this->client->setApplicationName("Productsup XML Data");	
		$this->client->setScopes(['https://www.googleapis.com/auth/spreadsheets','https://www.googleapis.com/auth/drive']);
		$this->client->setAuthConfig(ProductsupConfig::getInstance()->getgoogleApiCredentialsFile());
		return 1;
	}
	
	public function getClient()
	{		
		return $this->client;
	}
	
	public function returnAuthorizedClient(){
		$this->authorization();
		return $this->client;
	}
	
}