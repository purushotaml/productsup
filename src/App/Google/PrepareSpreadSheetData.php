<?php

namespace Console\App\Google;

class PrepareSpreadSheetData
{
    private $xmlDataInArray;
    private $spreadSheetHeader = array();
    private $spreadSheetContent = array();

    public function __construct($xmlDataInArray)
    {
        $this->xmlDataInArray = $xmlDataInArray;
    }

    public function prepareSpreadSheetDataFromArray()
    {
        foreach ($this->xmlDataInArray as $key => $item) {
            if ($key == 0) {
                array_push($this->spreadSheetHeader, array_keys($item['value']));
            }

            $itemData = array_values($item['value']);
            foreach ($itemData as $key => $value) {
                $itemData[$key] = strval($value);
            }

            array_push($this->spreadSheetContent, $itemData);
            unset($itemData);
        }
        unset($this->xmlDataInArray);
        return array_merge($this->spreadSheetHeader, $this->spreadSheetContent);
    }
}
