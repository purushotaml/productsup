<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Console\App\Productsup\Config;
use Console\App\Productsup\ProductsUpLogger;
use Console\App\Productsup\Processor;



use Console\App\Google\AccountAuthorizationInterface;

class ReadXmlCommand extends Command
{
    protected $defaultDataFile;

    public function __construct()
    {
        parent::__construct();
        $this->defaultDataFile = Config::getInstance()->getdefaultDataFile();
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
        try {
            try {
                $filename = $this->getFileNameFromCommandLine($input->getOption('filename'));
                if ($this->checkFileExists($filename)) {
                    $output->writeln('Starting to read the file '.$filename);
                    $this->processorObj = new Processor($filename);
                    $googleSheetUrl = $this->processorObj->execute();
                    $output->writeln('File Processed Successfully.');
                    $output->writeln('Google Sheet URL: '.$googleSheetUrl);
                    $output->writeln('Complete.');
                } else {
                    throw new \Exception("File Not Found");
                }
            } catch (\Exception $e) {
                $output->writeln('Error Occured, File Not Found');
                ProductsUpLogger::getInstance()->getLogger()->warning($e->getMessage()."===".$e->getLine());
            }
        } catch (\Exception $e) {
            $output->writeln('Error Occured');
            ProductsUpLogger::getInstance()->getLogger()->warning($e->getMessage()."===".$e->getLine());
        }
        unset($output);
        unset($this->processorObj);
        return 1;
    }

    protected function getFileNameFromCommandLine($givenFileName)
    {
        return (filter_var($givenFileName, FILTER_VALIDATE_URL) ? $givenFileName : Config::getInstance()->getdataFeedFolder().($givenFileName ? $givenFileName : $this->defaultDataFile));
    }

    protected function checkFileExists($givenFileName)
    {
        $file_headers = @get_headers($givenFileName);
        $exists = ((!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') ? false : true);
        return  (file_exists($givenFileName) || $exists);
    }
}
