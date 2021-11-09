<?php

namespace Console\App\Xml;

use Sabre\Xml\Reader;
use Sabre\Xml\Service;

class ParseXml
{
    private $xmlFile;


    public function __construct($filePath)
    {
        $this->xmlFile = $filePath;
    }

    public function ParseXmlFile()
    {
        //Read
        $xmlString = file_get_contents($this->xmlFile);
        $xmlReader = new Reader();
        $xmlReaderService = new Service();
        $xmlReaderService->elementMap = [
            'item' => function (Reader $reader) {
                return \Sabre\Xml\Deserializer\keyValue($reader, '');
            }
        ];

        //Parse
        $xmlStringArray = $xmlReaderService->parse($xmlString);

        return $xmlStringArray;
    }
}
