<?php


namespace Mlp\Cli\Console\Command;


use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \Magento\Framework\Filesystem\DirectoryList;


/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
class Auferma extends Command
{

    /**
     * Filter Prodcuts
     */
    const FILTER_PRODUCTS = 'filter-products';
    const ADD_PRODUCTS = 'add-products';
    const UPDATE_STOCKS = 'update-stocks';

    private $directory;

    public function __construct(DirectoryList $directory){
        $this->directory = $directory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('Mlp:Auferma')
            ->setDescription('Manage Auferma XLSX')
            ->setDefinition([
                new InputOption(
                    self::FILTER_PRODUCTS,
                    '-f',
                    InputOption::VALUE_NONE,
                    'Filter Auferma File'
                ),
                new InputOption(
                    self::ADD_PRODUCTS,
                    '-a',
                    InputOption::VALUE_NONE,
                    'Add new Products'
                ),
                new InputOption(
                    self::UPDATE_STOCKS,
                    '-u',
                    InputOption::VALUE_NONE,
                    'Update Stocks and State (Active or inactive)'
                )
            ]);
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filterProducts = $input->getOption(self::FILTER_PRODUCTS);
        if ($filterProducts) {
            $this->filterProducts();
        }
        $addProducts = $input->getOption(self::ADD_PRODUCTS);
        if ($addProducts) {
            $this->addProducts();
        }
        $updateStocks = $input->getOption(self::UPDATE_STOCKS);
        if ($updateStocks){
            $this->updateStocks();
        }
        else {
            throw new \InvalidArgumentException('Option ' . self::FILTER_PRODUCTS . ' is missing.');
        }
    }

    protected function addAufermaProdCSV()
    {
        /*
            Codigo,0
            Nome,1
            CodCurto,2
            PVPBase,3
            PesoBrt,4
            Marca,5
            FamiliaAuferma,6
            NomeXtra 7
            Gama,8
            Familia, 9
            subfamilia 10
            stock 11
        */


        $categories = $this->categoryManager->getCategoriesArray();
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Auferma.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Auferma products" . "\n");
        $row = 0;
        if (($handle = fopen($this->directory->getRoot()."/app/code/Mlp/Cli/Console/Command/aufermaInterno.csv", "r")) !== FALSE) {
            while (!feof($handle)) {
                $row++;
                if (($data = fgetcsv($handle, 4000, ",")) !== FALSE) {
                    if ($row == 1 || strcmp($data[8], "ACESSÓRIOS E BATERIAS") == 0 ||
                        strcmp($data[9], "AR CONDICIONADO") == 0) {
                        continue;
                    }
                    $sku = trim($data[0]);
                    try {
                        $product = $this->productRepository->get($sku, true, null, true);
                        if ($product->getStatus() == 2) {
                            continue;
                        }
                    } catch (NoSuchEntityException $exception) {
                        $name = trim($data[1]);
                        $gama = trim($data[8]);
                        $familia = trim($data[9]);
                        $subfamilia = trim($data[10]);
                        $description = trim($data[7]);
                        $meta_description = "";
                        $manufacter = trim($data[5]);
                        $length = 0;
                        $width = 0;
                        $height = 0;
                        $weight = trim($data[4]);
                        $price = (int)trim($data[3]);

                        try{
                            $productInterno = new \Mlp\Cli\Helper\Product($sku, $name, $gama, $familia, $subfamilia, $description,
                                $meta_description, $manufacter, $length, $width, $height, $weight, $price,
                                $this->productRepository, $this->productFactory, $this->categoryManager,
                                $this->dataAttributeOptions, $this->attributeManager, $this->stockRegistry,
                                $this->config, $this->optionFactory, $this->productRepositoryInterface, $this->directory);

                            $product = $productInterno->add_product($categories, $logger, $data[2]);
                        }catch (\Exception $e){
                            continue;
                        }
                    }
                    $this->updateStock($product, $data[11]);
                }
            }
            fclose($handle);
        }
    }

    private function updateStocks(){
        try {
            $fileUrl = $this -> directory -> getRoot() . "/app/code/Mlp/Cli/Console/Command/aufermaInterno.xlsx";
            $spreadsheetInterno = IOFactory ::load($fileUrl);
            $fileUrl = $this -> directory -> getRoot() . "/app/code/Mlp/Cli/Console/Command/aufermaStock.xlsx";
            $spreadsheetAuferma = IOFactory ::load($fileUrl);
            $intSheet = $spreadsheetInterno -> getActiveSheet();
            $aSheet = $spreadsheetAuferma -> getActiveSheet();
        }catch (\Exception $e){
            print_r($e->getMessage());
        }
        foreach ($intSheet->getRowIterator() as $iRow) {
            if ($iRow->getRowIndex() == 1) {
                continue;
            }
            try {
                $codProduto = $intSheet->getCell('A' . $iRow->getRowIndex())->getValue();
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                print_r($e->getMessage());
            }
            $intSheet->setCellValue('L'.$iRow->getRowIndex(),'não');
            foreach ($aSheet->getRowIterator() as $aRow ){
                if ($aRow->getRowIndex() == 1) {
                    continue;
                }
                try {
                    $codProdAuf = $aSheet->getCell('A' . $aRow->getRowIndex())-> getCalculatedValue();
                } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                    print_r($e->getMessage());
                }
                if(strcmp($codProduto,$codProdAuf) == 0){
                    print_r($codProduto."<->".$codProdAuf."->".$aRow->getRowIndex()."SIM\n");
                    $intSheet->setCellValue('L'.$iRow->getRowIndex(),'sim');
                    break;
                }elseif ($aRow->getRowIndex() == $aSheet->getHighestRow()){
                    print_r("Sem Codigo->".$codProdAuf."\n");
                }
            }
        }
        $writer = new Xlsx($spreadsheetInterno);
        $writer->save('aufermaInterno.xlsx');
        $csv_writer = new Csv($spreadsheetInterno);
        $csv_writer->save('aufermaInterno.csv');
    }

    private function addProducts()
    {
        try {
            $fileUrl = $this -> directory -> getRoot() . "/app/code/Mlp/Cli/Console/Command/aufermaInterno.xlsx";
            $spreadsheetInterno = IOFactory ::load($fileUrl);
            $fileUrl = $this -> directory -> getRoot() . "/app/code/Mlp/Cli/Console/Command/aufermaStock.xlsx";
            $spreadsheetAuferma = IOFactory ::load($fileUrl);
            $intSheet= $spreadsheetInterno->getActiveSheet();
            $aSheet=$spreadsheetAuferma->getActiveSheet();
        }catch (\Exception $e){
            print_r($e->getMessage());
        }
        print_r("Interno: ".$intSheet->getHighestRow()."\n");
        print_r("Stocks: ".$aSheet->getHighestRow()."\n");
        try{
            foreach ($aSheet->getRowIterator() as $aRow) {
                if ($aRow->getRowIndex() == 1) {
                    continue;
                }
                //Se for Acessórios Tvs ou pequenos domésticos etc.. salta fora
                $codFamilia = $aSheet->getCell('G'.$aRow->getRowIndex())->getValue();
                if (in_array((int)(string)$codFamilia,  [800,295,250,210,190,150,105,220,930,235])){
                    print_r("Salta Fora!!\n");
                    continue;
                }
                $codProdAuf = $aSheet->getCell('A'.$aRow->getRowIndex())->getValue();
                foreach ($intSheet->getRowIterator() as $iRow ){
                    if ($iRow->getRowIndex() == 1) {
                        continue;
                    }
                    $codProd = $intSheet->getCell('A'.$iRow->getRowIndex())->getValue();
                    if(strcmp($codProd,$codProdAuf) == 0){
                        print_r("encontrado!!\n");
                        break;
                        //Se for a ultima linha e não encontrou adiciona o artigo!
                    }elseif ($iRow->getRowIndex() == $intSheet->getHighestRow()){
                        print_r("new  Product: ".$codProdAuf."\n");
                        $intSheet->insertNewRowBefore($intSheet->getHighestRow()+1,1);
                        $intSheet->setCellValue('A'.$intSheet->getHighestRow(),$codProdAuf);
                        $intSheet->setCellValue('B'.$intSheet->getHighestRow(),$aSheet->getCell('B'.$aRow->getRowIndex())->getValue());
                        $intSheet->setCellValue('C'.$intSheet->getHighestRow(),$aSheet->getCell('C'.$aRow->getRowIndex())->getValue());
                        $intSheet->setCellValue('D'.$intSheet->getHighestRow(),$aSheet->getCell('D'.$aRow->getRowIndex())->getValue());
                        $intSheet->setCellValue('E'.$intSheet->getHighestRow(),$aSheet->getCell('E'.$aRow->getRowIndex())->getValue());
                        $intSheet->setCellValue('F'.$intSheet->getHighestRow(),$aSheet->getCell('F'.$aRow->getRowIndex())->getValue());
                        $intSheet->setCellValue('G'.$intSheet->getHighestRow(),$aSheet->getCell('G'.$aRow->getRowIndex())->getValue());
                        $intSheet->setCellValue('H'.$intSheet->getHighestRow(),$aSheet->getCell('H'.$aRow->getRowIndex())->getValue());
                        $intSheet->setCellValue('L'.$intSheet->getHighestRow(),'sim');
                    }
                }
            }
        }catch (\Exception $e){
            print_r($e->getMessage());
        }

        $writer = new Xlsx($spreadsheetInterno);
        try{
            $fileUrl = $this -> directory -> getRoot() . "/app/code/Mlp/Cli/Console/Command/aufermaInterno.xlsx";
            $writer->save($fileUrl);
        }catch (\Exception $e){
            print_r($e->getMessage());
        }

    }

    private function filterProducts()
    {
    }

}
