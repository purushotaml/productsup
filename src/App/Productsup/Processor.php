<?php

namespace Console\App\Productsup;

use Console\App\Xml\ParseXml;
use Console\App\Google\PrepareSpreadSheetData;
use Console\App\Google\ServiceAccountAuthorization;
use Console\App\Google\CreateSpreadSheet;
use Console\App\Google\SetSpreadSheetPermission;
use Console\App\Google\WriteDataToSpreadSheet;

class Processor
{
    protected $parseXmlObj;
    protected $prepareSpreadSheetDataObj;
    protected $parsedXmlArray;

    public function __construct($fileName)
    {
        $this->parseXmlObj = new ParseXml($fileName);
    }

    public function parseXml()
    {
        $this->parsedXmlArray = $this->parseXmlObj->ParseXmlFile();
    }

    public function getParsedXmlArray()
    {
        $this->parsedXmlArray = $this->parseXmlObj->ParseXmlFile();
        return $this->parsedXmlArray;
    }

    public function PrepareSpreadSheetData()
    {
        $this->prepareSpreadSheetDataObj = new PrepareSpreadSheetData($this->getParsedXmlArray());
        return $this->prepareSpreadSheetDataObj->prepareSpreadSheetDataFromArray();
    }

    public function CreateGoogleSpreadSheetFromData()
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

            //Write Data to Spreadsheet
            $writDataToSpreadSheetObj = new WriteDataToSpreadSheet($client);
            $writDataToSpreadSheetObj->writeData($spreadSheetId, $this->PrepareSpreadSheetData());

            return "https://docs.google.com/spreadsheets/d/".$spreadSheetId;
        } else {
            return "Problem With Authorization";
        }
    }

    protected function checkIfAccountAuthorizedToCreateSpreadSheet()
    {
        $serviceAccountAuthorizationObj = new ServiceAccountAuthorization();
        $authorized = $serviceAccountAuthorizationObj->authorization();
        return ($authorized ? $authorized : 0);
    }

    public function execute()
    {
        $this->parseXml();
        $this->PrepareSpreadSheetData();
        return $this->CreateGoogleSpreadSheetFromData();
    }
}
