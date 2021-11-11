<?php

namespace Console\App\Productsup;

use Console\App\Xml\ReadFeed;
use Console\App\Google\PrepareSpreadSheetData;
use Console\App\Google\ServiceAccountAuthorization;
use Console\App\Google\CreateSpreadSheet;
use Console\App\Google\SetSpreadSheetPermission;
use Console\App\Google\WriteDataToSpreadSheet;

class Processor
{
    protected $readXmlObj;
    protected $prepareSpreadSheetDataObj;
    protected $xmlArray;
    protected $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
        $this->readFeedObj = new ReadFeed($fileName);
    }

    public function execute()
    {
        $this->readFeed();
        $spreadSheetId = $this->CreateSpreadSheet("Productsup_".date("Ymdhis"));
        $this->writeDataToSpreadSheet($spreadSheetId, $this->getData());
        return "https://docs.google.com/spreadsheets/d/".$spreadSheetId;
    }

    public function readFeed()
    {
        $this->xmlArray = $this->readFeedObj->ReadFeedFile();
    }

    protected function CreateSpreadSheet($name)
    {
        if ($this->checkIfAccountAuthorizedToCreateSpreadSheet()) {

            //Set Google Client With Credentials
            $serviceAccountAuthorizationObj = new ServiceAccountAuthorization();
            $client = $serviceAccountAuthorizationObj->returnAuthorizedClient();

            //Create Spreadsheet
            $name = "Productsup_".date("Ymdhis");
            $createSpreadSheetObj = new CreateSpreadSheet($serviceAccountAuthorizationObj, $name, $client);
            $spreadSheetId = $createSpreadSheetObj->create()->getId();

            //Set Write Permission
            $permissionObj = new SetSpreadSheetPermission($client);
            $permissionObj->setWritePermission($spreadSheetId);

            return $spreadSheetId;
        } else {
            return "Problem With Authorization";
        }
    }

    protected function writeDataToSpreadSheet($spreadSheetId, $result)
    {
        $serviceAccountAuthorizationObj = new ServiceAccountAuthorization();
        $client = $serviceAccountAuthorizationObj->returnAuthorizedClient();

        $writDataToSpreadSheetObj = new WriteDataToSpreadSheet($client);
        $writDataToSpreadSheetObj->writeData($spreadSheetId, $result);
    }

    public function getXmlArray()
    {
        $this->xmlArray = $this->readFeedObj->ReadFeedFile();
        return $this->xmlArray;
    }

    public function getData()
    {
        $this->prepareSpreadSheetDataObj = new PrepareSpreadSheetData($this->getXmlArray());
        return $this->prepareSpreadSheetDataObj->prepareSpreadSheetDataFromArray();
    }


    protected function checkIfAccountAuthorizedToCreateSpreadSheet()
    {
        $serviceAccountAuthorizationObj = new ServiceAccountAuthorization();
        $authorized = $serviceAccountAuthorizationObj->authorization();
        return ($authorized ? $authorized : 0);
    }
}
