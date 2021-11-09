<?php

namespace Console\App\Google;

use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class WriteDataToSpreadSheet
{
    private $sheets;
    private $client;

    public function __construct($client)
    {
        $this->sheets = new Sheets($client);
        $this->client = $client;
    }

    public function writeData($spreadSheetId, $data)
    {
        $range = "sheet1!A1:R".count($data);
        $body = new ValueRange(['values' => $data]);
        $params = ['valueInputOption' => "RAW"];
        $result = $this->sheets->spreadsheets_values->update($spreadSheetId, $range, $body, $params);
        return 1;
    }
}
