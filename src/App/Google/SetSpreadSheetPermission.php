<?php
namespace Console\App\Google;


class SetSpreadSheetPermission 
{
  private $permission;
  private $service;
  private $client;
  
  public function __construct($client) 
  {
    $this->permission = new \Google_Service_Drive_Permission();
    $this->service = new \Google_Service_Drive($client);
	$this->client = $client;
  }
  
  public function setWritePermission($spreadSheetId)
  {
	
	$this->permission->setRole( 'writer' );
	$this->permission->setType( 'anyone' );
	$this->service->permissions->create($spreadSheetId, $this->permission );
	return 1;
  }

}