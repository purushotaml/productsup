<?php

namespace Console\App\Xml;

use Sabre\Xml\Reader;
use Sabre\Xml\Service;

class ReadFeed
{
    private $feedFile;


    public function __construct($filePath)
    {
        $this->feedFile = $filePath;
    }

    public function ReadFeedFile()
    {
        $xmlString = file_get_contents($this->feedFile);
        $xmlReader = new Reader();
        $xmlReaderService = new Service();
        $xmlReaderService->elementMap = [
            'item' => function (Reader $reader) {
                return \Sabre\Xml\Deserializer\keyValue($reader, '');
            }
        ];

        $xmlStringArray = $xmlReaderService->parse($xmlString);
        unset($xmlReader);
        unset($xmlReaderService);
        return $xmlStringArray;
    }
}
