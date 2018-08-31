<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 30-08-2018
 * Time: 17:24
 */

namespace Mlp\Cli\Console\Command;


class Sorefoz
{
    private $productRepository;
    private $productFactory;
    private $dataAttributeOptions;
    private $categoryLinkManagement;

    public function __construct(ProductRepository $productRepository,
                                ProductFactory $productFactory,
                                \Mlp\Cli\Helper\Data $dataAttributeOptions,
                                CategoryLinkManagementInterface $categoryLinkManagement)
    {
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->dataAttributeOptions = $dataAttributeOptions;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
    }
    protected function addSorefozProducts_csv(){
        /*
        Referencia - 0
        Descrição 1
        CodMarca 2
        NOME_MARCA 3
        Cod.Gama 4
        NOME_GAMA 5
        Cod.Familia 6
        NOME_FAMILIA 7
        SUBFAMILIA 8
        NOME_SUBFAMILIA 9
        PVP_MARCA 10
        PVP_CENTRAL 11
        PR_LIQUIDO 12
        EM_PROMOÇ+O 13
        EXCLUSIVO 14
        OFERTA 15
        FORA_GAMA 16
        PartNr 17
        EAN 18
        Peso(kg) 19
        Volume(dm3) 20
        Comprimento(mm) 21
        Largura(mm) 22
        Altura(mm) 23
        LinkImagem 24
        Caracteristicas_Resumo 25
        Caracteristicas_Completa 26
        Classe_energetica 27
        Link_Classe_Energetica 28
        Stock 29
        */
        $categories = $this->getCategoriesArray();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $writer = new \Zend\Log\Writer\Stream('/var/log/SorefozCSV.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        print_r("Adding Sorefoz products"."\n");

        $row = 0;
        if (($handle = fopen("/var/www/html/app/code/Mlp/Cli/Console/Command/tot_jlcb_utf.csv", "r")) !== FALSE) {
            print_r("abri ficheiro\n");
            //print_r($handle);
            while (($data = fgetcsv($handle,4000,";")) !== FALSE) {
                $row++;
                if ($row == 1 ){
                    continue;
                }
                print_r($row.":".$data[2]."\n");
                $num = count($data);
                //EAN - 18
                if (strcmp($data[5],"ACESSÓRIOS E BATERIAS")==0 || strcmp($data[7],"MAT. PROMOCIONAL / PUBLICIDADE")==0
                    || strcmp($data[7],"FERRAMENTAS")==0){
                    continue;
                    print_r("data: ".$data."\n");
                }
                $sku = trim($data[18]);
                if (strlen($sku) == 13) {
                    try {
                        $product = $this->productRepository->get($sku, true, null, true);
                        if ($product->getStatus() == 2){
                            print_r($sku."\n");
                            continue;
                        }
                    } catch (NoSuchEntityException $exception) {
                        $product = $this->productFactory->create();
                        $product->setSku($sku);
                        $this->getImages($product,$data);
                    }
                } else {
                    continue;
                }
                $product->setName(trim($data[1]));
                $optionId = $this->dataAttributeOptions->createOrGetId('manufacturer', trim($data[3]));
                $product->setCustomAttribute('manufacturer', $optionId);
                $subFamilia = trim($data[9]);
                $familia = trim($data[7]);
                $gama = $this->setGamaSorefoz(trim($data[5]));
                $preco = $data[12];
                $product->setPrice($preco);
                //GAMA
                switch ($data[16]){
                    case 'sim':
                        $product->setStatus(Status::STATUS_DISABLED);
                        break;
                    default:
                        $product->setStatus(Status::STATUS_ENABLED);
                }
                //STOCK
                switch ($data[29]) {
                    case 'Sim':
                        $product->setStockData(
                            array(
                                'use_config_manage_stock' => 0,
                                'manage_stock' => 1,
                                'is_in_stock' => 1,
                                'qty' => 999999999
                            )
                        );
                        break;
                    default:
                        $product->setStockData(
                            array(
                                'use_config_manage_stock' => 0,
                                'manage_stock' => 1,
                                'is_in_stock' => 0,
                                'qty' => 0
                            )
                        );
                        break;
                }
                $product->setCustomAttribute('description',$data[26]);
                $product->setCustomAttribute('meta_description',$data[25]);
                $product->setWebsiteIds([1]);
                //$attributeSetId = $this->attributeManager->getAttributeSetId($familia,$subFamilia);
                $product->setAttributeSetId(4); // Attribute set id
                $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                $product->setTaxClassId(0); // Tax class id
                $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
                $product->setCategoryIds([$categories[$gama],$categories[$familia],$categories[$subFamilia]]);
                $this->setImages($product,$logger,$product->getSku().".jpeg");
                try{
                    $product->save();

                } catch (\Exception $exception){
                    $logger->info($sku." Deu merda. Exception:  ".$exception->getMessage());
                    print_r($exception->getMessage());
                }
                print_r($sku."->".$row."->".microtime(true)."\n");
            }
            fclose($handle);
        }else{
            print_r("Não abriu o ficheiro");
        }
    }


}