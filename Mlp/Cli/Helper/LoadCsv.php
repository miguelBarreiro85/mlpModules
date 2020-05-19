<?php


namespace Mlp\Cli\Helper;


use Braintree\Exception;

class LoadCsv
{

    private $directory;
    public function __construct(\Magento\Framework\Filesystem\DirectoryList $directory)
    {
        $this->directory = $directory;
    }

    public function loadCsv($csv,$delimiter){
        $fileUrl = $this->directory->getRoot()."/app/code/Mlp/Cli/Csv".$csv;
        if (($handle = fopen($fileUrl, "r")) !== FALSE) {
            //ignore 1st line
            fgetcsv($handle, 4000, $delimiter,'"');
            while (!feof($handle)) {
                if (($data = fgetcsv($handle, 4000, $delimiter)) !== FALSE) {
                    if($data == "\n" || $data == "\r\n" || $data == "")
                    {
                        throw new Exception("Empty line found.\n");
                    }
                    if($data === false && !feof($handle))
                    {
                        print_r("Error reading file besides EOF\n");
                    }
                    elseif($data === false && feof($handle))
                    {
                        print_r("We are at the end of the file.\n");

                        //check status of the stream
                        $meta = stream_get_meta_data($handle);
                    }
                    else {
                        try{
                            yield $data;
                        }catch (\Exception $ex){
                            print_r(" - " . $ex->getMessage() . " - ");
                        }

                    }
                }
            }
            fclose($handle);
        } else {
            print_r("Não abriu o ficheiro\n");
        }

    }

    private function getProduct($supplier,$data)
    {
        switch ($supplier) {
            case 'sorefoz':

        }
    }

    public function coutCsvLines($csv) {
        $fileUrl = $this->directory->getRoot()."/app/code/Mlp/Cli/Csv/".$csv;
        $fp = file($fileUrl);
        return count($fp);
    }
}
