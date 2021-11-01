<?php
namespace Console\App\Google;


class WriteDataToSpreadSheet 
{
  private $sheets;
  private $client;
  
  public function __construct($client) 
  {
    $this->sheets = new \Google\Service\Sheets($client);
	$this->client = $client;
  }
  
  public function writeData($spreadSheetId,$data)
  {
	$range = "sheet1!A1:R".count($data);
	$body = new \Google_Service_Sheets_ValueRange(['values' => $data]);
	$params = ['valueInputOption' => "RAW"];
	$result = $this->sheets->spreadsheets_values->update($spreadSheetId, $range,$body, $params);
	return 1;
  }

}