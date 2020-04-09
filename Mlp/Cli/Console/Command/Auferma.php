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
use Magento\Framework\Exception\NoSuchEntityException;


/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
class Auferma extends Command
{

    /**
     * Filter Prodcuts
     */
    const ADD_PRODUCTS = 'add-products';
    const ADD_PRODUCTS_XLSX = 'add-products-xlsx';
    const UPDATE_STOCKS_XLSX = 'update-stocks-xlsx';

    const AUFERMA_INTERNO_XLSX = '/app/code/Mlp/Cli/Csv/aufermaInterno.xlsx';
    const AUFERMA_STOCK_XLSX = '/app/code/Mlp/Cli/Csv/aufermaStock.xlsx';
    const AUFERMA_INTERNO_CSV = '/app/code/Mlp/Cli/Csv/aufermaInterno.csv';

    private $directory;

    private $categoryManager;
    private $productRepository;
    private $state;
    private $produtoInterno;
    private $loadCsv;


    public function __construct(DirectoryList $directory,
                                \Mlp\Cli\Helper\Category $categoryManager,
                                \Magento\Framework\App\State $state,
                                \Mlp\Cli\Model\ProdutoInterno $productoInterno,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
                                \Mlp\Cli\Helper\LoadCsv $loadCsv)
    {

        $this -> directory = $directory;
        $this -> categoryManager = $categoryManager;
        $this -> productRepository = $productRepositoryInterface;
        $this -> state = $state;
        $this -> produtoInterno = $productoInterno;
        $this->loadCsv = $loadCsv;
        parent ::__construct();
    }

    protected function configure()
    {
        $this->setName('Mlp:Auferma')
            ->setDescription('Manage Auferma XLSX')
            ->setDefinition([
                new InputOption(
                    self::ADD_PRODUCTS_XLSX,
                    '-ax',
                    InputOption::VALUE_NONE,
                    'ADD NEW PRODUCTS TO XLSX'
                ),
                new InputOption(
                    self::ADD_PRODUCTS,
                    '-a',
                    InputOption::VALUE_NONE,
                    'Add new Products TO SITE'
                ),
                new InputOption(
                    self::UPDATE_STOCKS_XLSX,
                    '-ux',
                    InputOption::VALUE_NONE,
                    'Update Stocks and State (Active or inactive) on XLSX and Csv' 
                )
            ])->addArgument('categories', InputArgument::OPTIONAL, 'Categories?');;
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $categories = $input->getArgument('categories');
        $addProducts = $input->getOption(self::ADD_PRODUCTS);
        if ($addProducts) {
            print_r("Adding products");
            $this->addAufermaProducts($categories);
            print_r("finished");
            exit;
        }
    
        $addProducts = $input->getOption(self::ADD_PRODUCTS_XLSX);
        if ($addProducts) {
            $this->addProductsXlsx();
            print_r("finished");
            exit;
        }
        $updateStocks = $input->getOption(self::UPDATE_STOCKS_XLSX);
        if ($updateStocks){
            $this->updateStocksXlsx();
            print_r("finished");
            exit;
        }
        else {
            throw new \InvalidArgumentException('Option is missing.');
        }
    }

    protected function addAufermaProducts($categoriesFilter) {
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Auferma.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $row = 0;
        foreach ($this->loadCsv->loadCsv('aufermaInterno.csv',",") as $data) {
            $row++;
            print_r($row." - ");
            $this->setAufermaData($data);
            if (!is_null($categoriesFilter)){
                if (strcmp($categoriesFilter,$this->produtoInterno->subFamilia) != 0){
                    print_r("\n");
                    continue;
                }
            }
            try {
                $this -> productRepository -> get($this->produtoInterno->sku, true, null, true);
            } catch (NoSuchEntityException $exception) {
                $product = $this->produtoInterno -> add_product($logger, $this->produtoInterno->sku);
            }
            if(isset($product)){
                try {
                    print_r(" - Setting stock: " . $this->produtoInterno->stock . "\n");
                    $this->produtoInterno->setStock($this->produtoInterno->sku, 'auferma');
                } catch (\Exception $ex) {
                    print_r("Update stock exception - " . $ex -> getMessage() . "\n");
                }
            }
        }
    }

    private function addProductsXlsx()
    {
    
        try {
            $fileUrl = $this -> directory -> getRoot() . self::AUFERMA_INTERNO_XLSX;
            $spreadsheetInterno = IOFactory ::load($fileUrl);
            $fileUrl = $this -> directory -> getRoot() . self::AUFERMA_STOCK_XLSX;
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
            $fileUrl = $this -> directory -> getRoot() . self::AUFERMA_INTERNO_XLSX;
            $writer->save($fileUrl);
        }catch (\Exception $e){
            print_r($e->getMessage());
        }

    }

    private function updateStocksXlsx()
    {
        $fileUrl = $this -> directory -> getRoot() . self::AUFERMA_INTERNO_XLSX;
        $spreadsheetInterno = IOFactory ::load($fileUrl);
        $fileUrl = $this -> directory -> getRoot() . self::AUFERMA_STOCK_XLSX;
        $spreadsheetAuferma = IOFactory ::load($fileUrl);

        $intSheet= $spreadsheetInterno->getActiveSheet();
        $aSheet=$spreadsheetAuferma->getActiveSheet();

        foreach ($intSheet->getRowIterator() as $iRow) {
            if ($iRow->getRowIndex() == 1) {
                continue;
            }
            $codProduto = $intSheet->getCell('A'.$iRow->getRowIndex())->getValue();
            $intSheet->setCellValue('L'.$iRow->getRowIndex(),'não');

            foreach ($aSheet->getRowIterator() as $aRow ){
                if ($aRow->getRowIndex() == 1) {
                    continue;
                }

                $codProdAuf = $aSheet->getCell('A'.$aRow->getRowIndex())->getCalculatedValue();
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
        try{
            $fileUrl = $this -> directory -> getRoot() . self::AUFERMA_INTERNO_XLSX;
            $writer->save($fileUrl);
        }catch (\Exception $e){
            print_r($e->getMessage());
        }
        $csv_writer = new Csv($spreadsheetInterno);
        $csv_writer->save('aufermaInterno.csv');
    }


    private function setAufermaData($data) {
        /*
        0 codigo
        1 nome
        2 codcurtp
        3 PVP BASE
        4 PesoBruto
        5 marca
        6
        7 Descricao
        8 Gama
        9 familia
        10 subfamilia
        11 status e stock
        */
        $functionTrim = function ($data){
            return trim($data);
        };

        if (preg_match("/sim/i",$data[11]) == 1){
            $stock = 1;
            $status = 2;
        }else {
            $stock = 0;
            $status = 1;
        }

        $data = array_map($functionTrim,$data);
        $this->produtoInterno->sku = $data[0];
        $this->produtoInterno->name = $data[1];
        $this->produtoInterno->gama = $data[8];
        $this->produtoInterno->familia = $data[9];
        $this->produtoInterno->subFamilia = $data[10];
        $this->produtoInterno->description = $data[7];
        $this->produtoInterno->meta_description = $data[7];
        $this->produtoInterno->manufacturer = $data[5];
        $this->produtoInterno->length = null;
        $this->produtoInterno->width = null;
        $this->produtoInterno->height = null;
        $this->produtoInterno->weight = (int)$data[4];
        $this->produtoInterno->price = (int)trim($data[3]);
        $this->produtoInterno->status = $status;
        $this->produtoInterno->image = $data[10];
        $this->produtoInterno->classeEnergetica = null;
        $this->produtoInterno->imageEnergetica = null;
        $this->produtoInterno->stock = $stock;
    }

}
