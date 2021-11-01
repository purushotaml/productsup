<?php
namespace namespace Console\App;


class CreateGoogleSpreadSheet {
  private $dbConnection;
  public function __construct(DbConnectionInterface $dbConnection) {
    $this->dbConnection = $dbConnection;
        $this->dbConnection->connect();
  }
}
