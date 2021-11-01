<?php
namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Sabre\Xml\Reader;
use Console\App\ProductsupConfig;
use Console\App\ProductsupLogger;
use Console\App\Google\CreateSpreadSheet;
use Console\App\Google\SetSpreadSheetPermission;
use Console\App\Google\WriteDataToSpreadSheet;
use Console\App\Google\AccountAuthorizationInterface;
use Console\App\Google\ServiceAccountAuthorization;
class  ReadXmlCommand extends Command
{
    protected $defaultDataFile;
	
    public function __construct()
	{
		parent::__construct();
		$this->defaultDataFile = ProductsupConfig::getInstance()->getdefaultDataFile();

	}
	
	protected function configure()
    {
		$this->setName('read-xml')
			->setDescription('Read the given xml file')
			->setHelp('Read the xml file. Pass the --filename parameter to specify the filename.')
			->addOption(
					'filename',
					'f',
					InputOption::VALUE_OPTIONAL,
					'Pass the file name',
					''
					);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

		try
		{			
			try
			{		

				$filename = $this->getFileName($input->getOption('filename'));
				if($this->checkFileExists($filename)){
                    $output->writeln('Starting to read the file '.$filename);					
					$googleSheetUrl = $this->processFile($filename);
                    $output->writeln('File Processed Successfully.');
					$output->writeln('Google Sheet URL: '.$googleSheetUrl);
                    $output->writeln('Complete.');
				}
				else			    
					throw new \Exception("File Not Found");
			}
			catch(\Exception $e)
			{
                $output->writeln('Error Occured, File Not Found');	
                ProductsupLogger::getInstance()->getLogger()->warning($e->getMessage()."===".$e->getLine());				
			}				

		}
		catch(\Exception $e)
		{  
			$output->writeln('Error Occured');
			ProductsupLogger::getInstance()->getLogger()->warning($e->getMessage()."===".$e->getLine());
		}
		
		return 1;
    }
	
	protected function getFileName($givenFileName)
	{
		return (filter_var($givenFileName, FILTER_VALIDATE_URL)? $givenFileName : ProductsupConfig::getInstance()->getdataFeedFolder().($givenFileName ? $givenFileName : $this->defaultDataFile) );
	}
	
	protected function checkFileExists($givenFileName)
	{
		$file_headers = @get_headers($givenFileName);
		$exists = ((!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') ? false : true); 
        return  (file_exists($givenFileName) || $exists );
		
	}

	protected function processFile($file)
	{
		$xmlStringArray = $this->readAndParseXmlFile($file);
		$spreadSheetData = $this->prepareSpreadSheetDataFromArray($xmlStringArray);
        return ($this->checkIfAccountAuthorizedToCreateSpreadSheet() ? $this->createGoogleSpreadSheet($spreadSheetData) : "Problem With Authorization");
	}

	protected function readAndParseXmlFile($fileName)
	{ 
        //Read
		$xmlString = file_get_contents($fileName);
		
		$xmlReader = new \Sabre\Xml\Reader();
		$xmlReaderService = new \Sabre\Xml\Service();
		$xmlReaderService->elementMap = [
			'item' => function(\Sabre\Xml\Reader $reader) {
				return \Sabre\Xml\Deserializer\keyValue($reader, '');
			}
		];
        
		//Parse
        $xmlStringArray = $xmlReaderService->parse($xmlString);
		
		return $xmlStringArray;

	}
	
	protected function prepareSpreadSheetDataFromArray($catalogItemArray)
	{

        $spreadSheetHeader = array();
		$spreadSheetContent = array();
		$spreadSheetBody = array();
		
		foreach($catalogItemArray as $key => $item)
		{
			if($key == 0)
				array_push($spreadSheetHeader,array_keys($item['value']));

			$itemData = array_values($item['value']);
			foreach($itemData as $key => $value){
				$itemData[$key] = strval($value);
			}

			array_push($spreadSheetContent,$itemData);
		}
		
		$spreadSheetBody = array_merge($spreadSheetHeader,$spreadSheetContent);

        return $spreadSheetBody;		
	}

	protected function checkIfAccountAuthorizedToCreateSpreadSheet()
	{
         $serviceAccountAuthorizationObj = new ServiceAccountAuthorization();
		 $authorized = $serviceAccountAuthorizationObj->authorization();
		 return ($authorized ? $authorized : 0);
	}	
	protected function createGoogleSpreadSheet($data)
	{        
		
		//Set Google Client With Credentials
		$serviceAccountAuthorizationObj = new ServiceAccountAuthorization();
		$client = $serviceAccountAuthorizationObj->returnAuthorizedClient();
		
		//Create Spreadsheet
		$name = "Productsup_".date("Ymdhis");
		$createSpreadSheetObj = new CreateSpreadSheet($serviceAccountAuthorizationObj,$name,$client);
		$spreadSheetId = $createSpreadSheetObj->create()->getId();

		//Set Write Permission
		$permissionObj = new SetSpreadSheetPermission($client);
		$permissionObj->setWritePermission($spreadSheetId);
		
		//Write Data to Spreadsheet
		$writDataToSpreadSheetObj = new WriteDataToSpreadSheet($client);
		$writDataToSpreadSheetObj->writeData($spreadSheetId,$data);

        return "https://docs.google.com/spreadsheets/d/".$spreadSheetId;

	}	
	
}