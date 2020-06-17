<?php


namespace Mlp\Cli\Console\Command;

use Amazon\Core\Logger\Logger;
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
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Mlp\Cli\Helper\Auferma\AufermaCategories as aufermaCategories;
use Mlp\Cli\Helper\imagesHelper as imagesHelper;
use Mlp\Cli\Helper\CategoriesConstants as Cat;

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
    const GET_PRODUCT_IMAGES = 'get-product-images';

    const AUFERMA_INTERNO_XLSX = '/app/code/Mlp/Cli/Csv/Auferma/aufermaInterno.xlsx';
    const AUFERMA_STOCK_XLSX = '/app/code/Mlp/Cli/Csv/Auferma/aufermaStock.xlsx';
    const AUFERMA_INTERNO_CSV = '/app/code/Mlp/Cli/Csv/Auferma/aufermaInterno.csv';

    private $directory;

    private $categoryManager;
    private $productRepository;
    private $state;
    private $produtoInterno;
    private $loadCsv;
    private $imagesHelper;
    private $sqlHelper;

    public function __construct(imagesHelper $imagesHelper,
                                \Mlp\Cli\Helper\SqlHelper $sqlHelper,
                                DirectoryList $directory,
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
        $this->imagesHelper = $imagesHelper;
        $this->sqlHelper = $sqlHelper;

        parent ::__construct();
    }

    protected function configure()
    {
        $this->setName('Mlp:Auferma')
            ->setDescription('Manage Auferma XLSX')
            ->setDefinition([
                new InputOption(
                    self::ADD_PRODUCTS_XLSX,
                    '-A',
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
                    '-u',
                    InputOption::VALUE_NONE,
                    'Update Stocks and State (Active or inactive) on XLSX and Csv' 
                ),
                new InputOption(
                    self::GET_PRODUCT_IMAGES,
                    '-I',
                    InputOption::VALUE_NONE,
                    'GET PRODUCT IMAGES' 
                )
            ])->addArgument('categories', InputArgument::OPTIONAL, 'Categories?');;
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $writer = new \Zend\Log\Writer\Stream($this->directory->getRoot().'/var/log/Auferma.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $categories = $input->getArgument('categories');
        $addProducts = $input->getOption(self::ADD_PRODUCTS);
        if ($addProducts) {
            print_r("Adding products");
            $this->addAufermaProducts($logger, $categories);
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

    protected function addAufermaProducts($logger,$categoriesFilter = null) {
        print_r("Updating Expert products" . "\n");
        $row = 0;
        $statusAttributeId = $this->sqlHelper->sqlGetAttributeId('status');
        $priceAttributeId = $this->sqlHelper->sqlGetAttributeId('price');
        
        foreach ($this->loadCsv->loadCsv('/Auferma/aufermaInterno.csv',",") as $data) {
            $row++;
            $sku = trim($data[0]);
            print_r($row." - ");
            if ($this->sqlHelper->sqlUpdateStatus($sku,$statusAttributeId[0]["attribute_id"])){
                //update price anda stock
                $price = (int)trim($data[3]);
                if ($price == 0){
                    print_r(" price 0\n");
                    $logger->info(Cat::ERROR_PRICE_ZERO.$sku);
                    continue;
                }
                $this->sqlHelper->sqlUpdatePrice($sku,$priceAttributeId[0]["attribute_id"],$price);
                $this->produtoInterno->sku = $sku;
                $this->setStock(trim($data[11]));    
                $this->produtoInterno->setStock($logger,"auferma");
                print_r("updated - stock\n");
            }else {
            //Add product
                if(!$this->setAufermaData($data,$logger)){
                    continue;
                }                
                [$this->produtoInterno->image, $this->produtoInterno->imageEnergetica, 
                    $this->produtoInterno->height, $this->produtoInterno->width, $this->produtoInterno->length, 
                    $this->produtoInterno->weight, $this->produtoInterno->classeEnergetica] = $this->getProductInfo($logger,trim($data[1]));                
                
                $this->produtoInterno -> add_product($logger, $this->produtoInterno->sku);
                $this->produtoInterno->setStock($logger, 'auferma');
                print_r("\n");
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
        print_r("Interno: ".$intSheet->getHighestRow(1)."\n");
        print_r("Stocks: ".$aSheet->getHighestRow(1)."\n");
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
                    print_r("Sem Codigo->".$codProduto."\n");
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


    private function setStock($stock){
        if (preg_match("/sim/i",$stock) == 1){
            $this->produtoInterno->stock = 1;
            $this->produtoInterno->status = 1;
        }else {
            $this->produtoInterno->stock = 0;
            $this->produtoInterno->status = 2;
        }
    }

    private function setAufermaData($data,$logger) {
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

        $data = array_map($functionTrim,$data);


        $this->setStock($data[11]);
        

        $this->produtoInterno->sku = $data[0];
        $this->produtoInterno->manufacturer = $data[5];
        $this->produtoInterno->price = (int)trim($data[3]);

        if($this->produtoInterno->price == 0) {
            $logger->info(Cat::ERROR_PRICE_ZERO.$this->produtoInterno->sku);
            $stock = 0;
        }
        if (
            !preg_match("/BEKO/i", $data[5]) &&
            !preg_match("/GRUNDIG/i", $data[5])           
        ) {
            return 0;
        }
        
        [$gama,$familia,$subFamilia] =  aufermaCategories::getCategories(
                                            $data[8],$data[9],$data[10],
                                            $logger,$this->produtoInterno->sku);    
 
        $this->produtoInterno->name = strtoupper($data[1]);
        $this->produtoInterno->gama = $gama;
        $this->produtoInterno->familia = $familia;
        $this->produtoInterno->subFamilia = $subFamilia;
        $this->produtoInterno->description = mb_strtoupper($data[7], 'UTF-8');
        $this->produtoInterno->meta_description = mb_strtoupper($data[7], 'UTF-8');
        
        $this->produtoInterno->length = null;
        $this->produtoInterno->width = null;
        $this->produtoInterno->height = null;
        $this->produtoInterno->classeEnergetica = null;
        $this->produtoInterno->weight = null;
        return 1;
    }

    private function getProductInfo($logger, $name) {
        if (preg_match("/Beko (.*)$/",$name,$codeMatches) == 1){
            //Por cada linha do csv auferma vamos tentar extrair o codigo que é usado no icecat
            $code = str_replace(" ","",$codeMatches[1]);
            $jsonProduct = $this->getImageUrl($code);
            $product = json_decode($jsonProduct,true);

            $imageUrl = $energyLabelUrl = $altura = $largura = $comprimento = $peso = $classeEnergetica = null;
            try{
                if($product["data"]["Image"]["HighPic"]){
                    $imageUrl = $product["data"]["Image"]["HighPic"];
                }else{
                    print_r(" - Sem imagem - ");
                    $logger->info("Sem imagem: ".$name);
                }
                foreach($product["data"]["Multimedia"] as $multimedia) {
                    if (preg_match("/EU Energy Label/i",$multimedia["Type"]) == 1) {
                        $energyLabelUrl = $multimedia["URL"];
                        break;
                    }
                }
                foreach($product["data"]["FeaturesGroups"] as $featureGroup){
                    if((int)$featureGroup["ID"] == 6747) {
                        foreach($featureGroup["Features"] as $feature){
                            if ((int)$feature["Feature"]["ID"] == 1464) {
                                //altura
                                $altura = $feature["Value"];
                                continue;
                            }
                            if ((int)$feature["Feature"]["ID"] == 1650) {
                                //largura
                                $largura = $feature["Value"];
                                continue;
                            }
                            if ((int)$feature["Feature"]["ID"] == 1649) {
                                //comprimento
                                $comprimento = $feature["Value"];
                                continue;
                            }
                            if ((int)$feature["Feature"]["ID"] == 94) {
                                //peso
                                $peso = $feature["Value"];
                                continue;
                            }
                        }
                    }
                    if ((int)$featureGroup["ID"] == 6755) {
                        //classe energética
                        foreach($featureGroup["Features"] as $feature){
                            if ($feature["Feature"]["ID"] == 2705)
                            $classeEnergetica = $feature["Value"];
                            continue 2;
                        }
                        
                    }
                    
                }
                return [$imageUrl, $energyLabelUrl, $altura, $largura, $comprimento, $peso, $classeEnergetica];
            }catch(\Exception $e) {
                print_r($e->getMessage());
                $logger->info("Get Images Url Error: ".$name);
                return [$imageUrl, $energyLabelUrl, $altura, $largura, $comprimento, $peso, $classeEnergetica];
            }
            
            
        }
         
    }

    private function rotateLogs(){
        //Copiar log antigo para o arquivo tar logs 
        $fileToRotate = $this->directory->getRoot()."/var/log/Auferma.log"; 
        $tarArchive = $this->directory->getRoot()."/var/log/mlp_cli.tar";
        exec("tar -rf $tarArchive $fileToRotate");
    }
    
    private function getImageUrl($code) {
        $ch = curl_init("https://live.icecat.biz/api/?UserName=mlpbarreiro&Language=en&Brand=beko&ProductCode=".$code);            
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_TIMEOUT,0);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
        $jsonProduct = curl_exec($ch);
        curl_close($ch);
        return $jsonProduct;
    }
}
