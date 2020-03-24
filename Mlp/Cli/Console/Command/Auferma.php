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
