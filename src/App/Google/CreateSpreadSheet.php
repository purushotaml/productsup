<?php

namespace Console\App\Google;

use Console\App\Google\AccountAuthorizationInterface;
use Console\App\Google\ServiceAccountAuthorization;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;

class CreateSpreadSheet
{
    private $authorziation;
    private $spreadSheetName;
    private $client;

    public function __construct(AccountAuthorizationInterface $authorization, $spreadSheetName, $client)
    {
        $this->authorization = $authorization;
        $this->spreadSheetName = $spreadSheetName;
        $this->client = $client;
    }

    public function create()
    {
        $service = new Drive($this->client);
        $file = new DriveFile();
        $file->setParents(array($file->getDriveId()));
        $file->setMimeType('application/vnd.google-apps.spreadsheet');
        $file->setName($this->spreadSheetName);
        $results = $service->files->create($file);
        return $results;
    }
}
