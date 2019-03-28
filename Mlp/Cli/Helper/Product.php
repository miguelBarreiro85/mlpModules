<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 19-03-2019
 * Time: 10:10
 */

namespace Mlp\Cli\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Catalog\Model\ProductRepository as ProductRepository;
use Magento\Catalog\Model\ProductFactory as ProductFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use \Mlp\Cli\Helper\Category as CategoryManager;
use \Mlp\Cli\Helper\Data as DataAttributeOptions;
use \Mlp\Cli\Helper\Attribute as Attribute;

class Product
{
    //atributos
    private $sku;
    private $name;
    private $gama;
    private $familia;
    private $subfamilia;
    private $description;
    private $meta_description;
    private $manufacter;
    private $length;
    private $width;
    private $height;
    private $weight;
    private $price;
    //stock
    //Classes
    private $productRepository;
    private $productFactory;
    private $categoryManager;
    private $dataAttributeOptions;
    private $attributeManager;
    private $stockRegistry;
    private $config;
    private $optionFactory;
    private $productRepositoryInterface;

    public function __construct($sku, $name, $gama, $familia, $subfamilia,
                                $description, $meta_description, $manufacter,
                                $length, $width, $height, $weight, $price,
                                ProductRepository $productRepository,
                                ProductFactory $productFactory,
                                CategoryManager $categoryManager,
                                DataAttributeOptions $dataAttributeOptions,
                                Attribute $attributeManager,
                                StockRegistryInterface $stockRegistry,
                                Config $config,
                                OptionFactory $optionFactory,
                                ProductRepositoryInterface $productRepositoryInterface)
    {
        $this->sku = $sku;
        $this->name = $name;
        $this->gama = $gama;
        $this->familia = $familia;
        $this->subfamilia = $subfamilia;
        $this->description = $description;
        $this->meta_description = $meta_description;
        $this->manufacter = $manufacter;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->weight = $weight;
        $this->price = $price;

        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->categoryManager = $categoryManager;
        $this->dataAttributeOptions = $dataAttributeOptions;
        $this->attributeManager = $attributeManager;
        $this->stockRegistry = $stockRegistry;
        $this->config = $config;
        $this->optionFactory = $optionFactory;
        $this->productRepositoryInterface = $productRepositoryInterface;
    }

    public function add_product($categories, $logger, $imgName) {
                $product = $this->productFactory->create();
                $product->setSku($this->sku);
                $product->setName($this->name);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $subFamilia = $this->categoryManager->setSubFamiliaSorefoz($this->subfamilia);
                $familia = $this->categoryManager->setFamiliaSorefoz($this->familia);
                $gama = $this->categoryManager->setGamaSorefoz($this->gama);
                $product->setCustomAttribute('description', $this->description);
                $product->setCustomAttribute('meta_description', $this->meta_description);
                $optionId = $this->dataAttributeOptions->createOrGetId('manufacturer', $this->manufacter);
                $product->setCustomAttribute('manufacturer', $optionId);
                $product->setCustomAttribute('ts_dimensions_length', $this->length / 10);
                $product->setCustomAttribute('ts_dimensions_width', $this->width / 10);
                $product->setCustomAttribute('ts_dimensions_height', $this->height / 10);
                $product->setCustomAttribute('tax_class_id', 2); //taxable goods id
                $product->setWeight($this->weight);
                $product->setWebsiteIds([1]);
                $attributeSetId = $this->attributeManager->getAttributeSetId($familia, $subFamilia);
                $product->setAttributeSetId($attributeSetId); // Attribute set id
                $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                $product->setTaxClassId(2); // Tax class id
                $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
                $product->setCreatedAt(date("Y/m/d"));
                $product->setCustomAttribute('news_from_date', date("Y/m/d"));
                try {
                    $product->setCategoryIds([$categories[$gama], $categories[$familia], $categories[$subFamilia]]);
                } catch (\Exception $ex) { //Adicionar nova categoria
                    try{
                        $this->categoryManager->createCategory($gama, $familia, $subFamilia, $categories);
                        $categories = $this->categoryManager->getCategoriesArray();
                        $product->setCategoryIds([$categories[$gama], $categories[$familia], $categories[$subFamilia]]);
                    }catch (\Exception $ex){
                        print_r("\nErro ao adicionar nova categtoria ". $ex->getMessage() . "\n");
                    }

                }
                $this->setImages($product, $logger, $imgName . "_e.jpeg");
                $this->setImages($product, $logger, $imgName . ".jpeg");
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                //Preço
                $product->setPrice($this->price);
                $attributes = $this->getSpecialAttributes($this->gama, $this->familia, $this->subfamilia, $this->description, $this->name);
                if (isset($attributes)){
                    foreach ($attributes as $attribute) {
                        $product->setCustomAttribute($attribute['code'], $attribute['value']);
                    }
                }
                //Salvar produto
                try {
                    $product->save();
                    print_r($this->sku . " - added" . "  -  ");
                } catch (\Exception $exception) {
                    $logger->info(" - " . $this->sku . " Save product: Exception:  " . $exception->getMessage());
                    print_r("- " . $exception->getMessage() . " Save product exception" . "\n");
                }
                $this->add_warranty_option($product, $gama, $familia, $subFamilia);
                $value = $this->getInstallationValue($familia);
                if ($value > 0){
                    $this->add_installation_option($product,$value);
                }
            }

        protected function setImages($product, $logger, $ImgName)
        {
            print_r($product->getSku());
            $baseMediaPath = $this->config->getBaseMediaPath();
            try {
                $images = $product->getMediaGalleryImages();
                if (!$images || $images->getSize() == 0) {
                    $product->addImageToMediaGallery($baseMediaPath . "/" . $ImgName, ['image', 'small_image', 'thumbnail'], false, false);
                }
            } catch (\RuntimeException $exception) {
                print_r("run time exception" . $exception->getMessage() . "\n");
            } catch (\Exception $localizedException) {
                $logger->info($product->getName() . "Image name" . $ImgName . "  Sem Imagem");
            }
        }

        protected function getSpecialAttributes($gama,$familia,$subfamilia, $description, $name){
        $attributes = [];
            switch ($gama){
                case 'GRANDES DOMÉSTICOS':
                    switch ($familia) {
                        case 'ENCASTRE - MESAS':
                            $attribute['code'] = 'tipo_placa_encastre';
                            switch ($subfamilia) {
                                case 'CONVENCIONAIS C/GÁS':
                                    $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Gás');
                                    array_push($attributes, $attribute);
                                    return $attributes;
                                case 'DE INDUÇÃO':
                                    $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Indução');
                                    array_push($attributes, $attribute);
                                    return $attributes;
                                case 'VITROCERÂMICAS C/GÁS':
                                    $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Vitrocerâmicas Gás');
                                    array_push($attributes, $attribute);
                                    return $attributes;
                                case 'DOMINÓS C/GÁS':
                                    $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Dominós Gás');
                                    array_push($attributes, $attribute);
                                    return $attributes;
                                case 'VITROCERÂMICAS - ELÉCTRICAS':
                                    $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Vitrocerâmicas');
                                    array_push($attributes, $attribute);
                                    return $attributes;
                                case 'DOMINÓS - ELÉCTRICOS':
                                    $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Dominós Eléctricos');
                                    array_push($attributes, $attribute);
                                    return $attributes;
                                default:
                                    $attribute['value'] = $this->dataAttributeOptions->createOrGetId('tipo_placa_encastre', 'Outras');
                                    array_push($attributes, $attribute);
                                    return $attributes;
                            }
                        case 'MAQUINAS LAVAR ROUPA':
                            if (preg_match('/(\d+)R\./', $name, $matches) == 1) {
                                if ((int)$matches[1] > 600) {
                                    $attribute1['code'] = 'rotacao_mlr';
                                    $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('rotacao_mlr', (int)$matches[1]);
                                    array_push($attributes, $attribute1);
                                }
                            }
                            if (preg_match('/R.(\d+)K/', $name, $matches1) == 1) {
                                if ((int)$matches1[1] > 1) {
                                    $attribute2['code'] = 'capacidade_kg';
                                    $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('capacidade_kg', (int)$matches1[1]);
                                    array_push($attributes, $attribute2);
                                }

                            }
                            if (preg_match('/(A\+{1,3})/', $name, $matches2) == 1) {
                                $attribute3['code'] = 'eficiencia_energetica';
                                $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches2[1]));
                                array_push($attributes, $attribute3);
                            }
                            if (preg_match('/Cor: (\w+)\s/', strip_tags($description), $matches3) == 1) {
                                print_r("- " . $matches3[1] . " - ");
                                $attribute4['code'] = 'color';
                                $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($matches3[1]));
                                array_push($attributes, $attribute4);
                            }
                            return $attributes;
                        case 'MAQUINAS LAVAR LOUÇA':
                            if (preg_match('/Cor: (\w+)\s/', strip_tags($description), $matches1) == 1) {
                                $attribute1['code'] = 'color';
                                $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($matches1[1]));
                                array_push($attributes, $attribute1);
                            }
                            if (preg_match('/(A\+{1,3})/', $name, $matches2) == 1) {
                                $attribute2['code'] = 'eficiencia_energetica';
                                $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches2[1]));
                                array_push($attributes, $attribute2);
                            }
                            if (preg_match('/(\d+)TA/', $name, $matches3) == 1) {
                                $attribute3['code'] = 'capacidade_mll';
                                $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('capacidade_mll', $matches3[1] . " Conjuntos");
                                array_push($attributes, $attribute3);
                            }
                            if (preg_match('/(\d)P/', $name, $matches4) == 1) {
                                print_r(" - programas: " . $matches4[1] . " - ");
                                $attribute4['code'] = 'programas_mll';
                                $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('programas_mll', $matches4[1] . " Programas");
                                array_push($attributes, $attribute4);
                            }
                            return $attributes;
                        case 'MAQUINAS SECAR ROUPA':
                            if (preg_match('/Cor: (\w+)/ui', strip_tags($description), $matches1) == 1) {
                                $attribute1['code'] = 'color';
                                $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($matches1[1]));
                                array_push($attributes, $attribute1);
                            }if (preg_match('/(A\+{1,3})/', $name, $matches2) == 1) {
                                $attribute2['code'] = 'eficiencia_energetica';
                                $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches2[1]));
                                array_push($attributes, $attribute2);
                            }elseif (preg_match('/Classe Energética: (\w)/',html_entity_decode(strip_tags($description)),$matches3) == 1) {
                                $attribute3['code'] = 'eficiencia_energetica';
                                $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches3[1]));
                                array_push($attributes, $attribute3);
                            }
                            if (preg_match('/(\d+)K/',$name,$matches4) == 1) {
                                $attribute4['code'] = 'capacidade_kg';
                                $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('capacidade_kg', $matches4[1]." kg");
                                array_push($attributes, $attribute4);
                            }
                            return $attributes;
                        case 'FOGÕES':
                            if (preg_match('/(\d+x\d+)/', $name, $matches1) == 1) {
                                $attribute1['code'] = 'medidas_fogao';
                                $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('medidas_fogao', trim($matches1[1]));
                                array_push($attributes, $attribute1);
                            }
                            if (preg_match('/Forno: (\w+)/ui', strip_tags($description), $matches2) == 1) {
                                $attribute2['code'] = 'tipo_forno';
                                $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('tipo_forno', trim($matches2[1]));
                                array_push($attributes, $attribute2);
                            }
                            if (preg_match('/Cor: (\w+)/ui', strip_tags($description), $matches3) == 1) {
                                $attribute3['code'] = 'color';
                                $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($matches3[1]));
                                array_push($attributes, $attribute3);
                            }
                            return $attributes;
                    }
                case 'CLIMATIZAÇÃO':
                    $attributes = [];
                    switch ($familia) {
                        case 'AR CONDICIONADO':
                            switch ($subfamilia) {
                                case 'AR COND.INVERTER':
                                case 'AR COND.MULTI-SPLIT':
                                    //$attribute1['code'] = 'tipo_ac';
                                    if (preg_match('/UNID.INT/', $name) == 1) {
                                        if (preg_match('/Arrefecimento: (\d+)./', $description, $matches) == 1){
                                            $potencia = $this->getPotencia((int)$matches[1]);
                                            if ($potencia != null){
                                                $attribute2['code'] = 'potencia_ac_int';
                                                $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('potencia_ac_int',
                                                    $potencia);
                                                array_push($attributes,$attribute2);
                                            }

                                        }
                                        return $attributes;
                                    } elseif (preg_match('/UNID.EXT/', $name) == 1) {
                                        if (preg_match('/Arrefecimento: (\d+)./', $description, $matches) == 1){
                                            $potencia = $this->getPotencia((int)$matches[1]);
                                            if ($potencia != null){
                                                $attribute2['code'] = 'potencia_ac_ext';
                                                $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('potencia_ac_ext',
                                                    $potencia);
                                                array_push($attributes,$attribute2);
                                            }

                                        }
                                        return $attributes;
                                    } else {
                                        if (preg_match('/Arrefecimento: (\d+)./', $description, $matches) == 1){
                                            $potencia = $this->getPotencia((int)$matches[1]);
                                            if ($potencia != null){
                                                $attribute2['code'] = 'potencia_ac_conj';
                                                $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('potencia_ac_conj',
                                                    $potencia);
                                                array_push($attributes,$attribute2);
                                            }

                                        }
                                        return $attributes;
                                    }
                            }
                    }
            }
            
        }

        protected function getPotencia($matches){
            if ($matches < 9) {
                return 'De 5KBTU a 7KBTU';
            } elseif ( 8 < $matches && $matches < 13){
                return 'De 9KBTU a 12KBTU';
            } elseif ( 13 < $matches && $matches < 18) {
                return 'De 12KBTU a 18KBTU';
            } elseif (  18 < $matches && $matches< 24 ) {
                return 'De 18KBTU a 24KBTU';
            }elseif (24 < $matches && $matches< 36) {
                return 'De 24KBTU a 36KBTU';
            }elseif (36 < $matches && $matches< 48){
                return 'De 36KBTU a 48KBTU';
            }elseif (48 < $matches && $matches< 60) {
                return 'De 48KBTU a 60KBTU';
            }elseif ($matches > 60){
                return 'Superior a 60KBTU';
            }else {
                return null;
            }


        }
        protected function add_warranty_option($product, $gama, $familia, $subfamilia){
        $one_year = $this->get_one_year_warranty_price((int)$product->getPrice(), $gama, $familia, $subfamilia);
        $three_years = $this->get_three_years_warranty_price((int)$product->getPrice(), $gama);
        //Se os valores forem 0 não adiciona
        if ($one_year == 0 && $three_years == 0){
            return;
        }
        elseif ($one_year != 0 && $three_years != 0) {
            $options = [
                [
                    'title' => 'Extensão de garantia',
                    'type' => 'checkbox',
                    'is_require' => false,
                    'sort_order' => 4,
                    'values' => [
                        [
                            'title' => '1 ano',
                            'price' => $one_year,
                            'price_type' => 'fixed',
                            'sort_order' => 0,
                        ],
                        [
                            'title' => '3 anos',
                            'price' => $three_years,
                            'price_type' => 'fixed',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ];
        }elseif ($one_year == 0 && $three_years != 0){
            $options = [
                [
                    'title' => 'Extensão de garantia',
                    'type' => 'checkbox',
                    'is_require' => false,
                    'sort_order' => 4,
                    'values' => [
                        [
                            'title' => '3 anos',
                            'price' => $three_years,
                            'price_type' => 'fixed',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ];
        }
        if(!isset($options)){
            return;
        }
        foreach ($options as $arrayOption) {
            $option = $this->optionFactory->create();
            $option->setProductId($product->getId())
                ->setStoreId($product->getStoreId())
                ->addData($arrayOption);
            $option->save();
            $product->addOption($option);
        }
        $this->productRepositoryInterface->save($product);

    }
        protected function get_one_year_warranty_price($preco, $gama, $familia, $subfamilia)
    {
        switch ($gama) {
            case 'GRANDES DOMÉSTICOS':
                if ($preco <= 200) {
                    return 14;
                } elseif ($preco > 200 && $preco <= 400) {
                    return 19;
                } elseif ($preco > 400 && $preco <= 600) {
                    return 29;
                } elseif ($preco > 600 && $preco <= 1000) {
                    return 49;
                } elseif ($preco > 1000 && $preco <= 1500) {
                    return 59;
                } elseif ($preco > 1500) {
                    return 79;
                }
                break;
            case 'IMAGEM E SOM':
                switch ($familia) {
                    case 'CÂMARAS':
                        if ($preco <= 100) {
                            return 19;
                        } elseif ($preco > 100 && $preco <= 200) {
                            return 24;
                        } elseif ($preco > 200 && $preco <= 400) {
                            return 39;
                        } elseif ($preco > 400 && $preco <= 600) {
                            return 49;
                        } elseif ($preco > 600 && $preco <= 800) {
                            return 69;
                        } elseif ($preco > 800) {
                            return 89;
                        }
                        break;
                    default:
                        if ($preco <= 200) {
                            return 19;
                        } elseif (200 < $preco && $preco <= 400) {
                            return 29;
                        } elseif ($preco > 400 && $preco <= 600) {
                            return 49;
                        } elseif ($preco > 600 && $preco <= 1000) {
                            return 69;
                        } elseif ($preco > 1000 && $preco <= 1500) {
                            return 79;
                        } elseif ($preco > 1500) {
                            return 119;
                        }
                        break;
                }
            case 'INFORMÁTICA':
                if ($preco <= 200) {
                    return 24;
                } elseif ($preco > 200 && $preco <= 400) {
                    return 39;
                } elseif ($preco > 400 && $preco <= 600) {
                    return 49;
                } elseif ($preco > 600 && $preco <= 1000) {
                    return 69;
                } elseif ($preco > 1000 && $preco <= 1500) {
                    return 89;
                } elseif ($preco > 1500) {
                    return 99;
                }
                break;
            case 'COMUNICAÇÕES':
                if (strcmp($familia, "TELEFONES FIXOS") == 0 || strcmp($subfamilia, "TELEMÓVEIS") == 0){
                    if ($preco <= 150) {
                        return 19;
                    } elseif ($preco > 150 && $preco <= 300) {
                        return 24;
                    } elseif ($preco > 300 && $preco <= 400) {
                        return 39;
                    } elseif ($preco > 400 && $preco <= 500) {
                        return 49;
                    } elseif ($preco > 500 && $preco <= 700) {
                        return 69;
                    } elseif ($preco > 700 && $preco <= 900) {
                        return 79;
                    } elseif ($preco > 900) {
                        return 89;
                    }
                }else{
                    return 0;
                }
                break;
            case 'PEQUENOS DOMÉSTICOS':
                if ($preco <= 50) {
                    return 9;
                } elseif ($preco > 50 && $preco <= 100) {
                    return 14;
                } elseif ($preco > 100 && $preco <= 200) {
                    return 19;
                } elseif ($preco > 200 && $preco <= 500) {
                    return 29;
                }
                break;
            case 'CLIMATIZAÇÃO':
                switch ($familia){
                    case 'AR CONDICIONADO':
                        if ($preco <= 200) {
                            return 14;
                        } elseif ($preco > 200 && $preco <= 400) {
                            return 19;
                        } elseif ($preco > 400 && $preco <= 600) {
                            return 29;
                        } elseif ($preco > 600 && $preco <= 1000) {
                            return 49;
                        } elseif ($preco > 1000 && $preco <= 1500) {
                            return 59;
                        } elseif ($preco > 1500) {
                            return 79;
                        }
                        break;
                    default:
                        return 0;
                }
                break;
            default:
                return 0;
        }
    }
        protected function get_three_years_warranty_price($preco, $gama){
        switch ($gama) {
            case 'GRANDES DOMÉSTICOS':
                if ($preco <= 200) {
                    return 29;
                } elseif ($preco > 200 && $preco <= 400) {
                    return 49;
                } elseif ($preco > 400 && $preco <= 600) {
                    return 69;
                } elseif ($preco > 600 && $preco <= 1000) {
                    return 99;
                } elseif ($preco > 1000 && $preco <= 1500) {
                    return 119;
                } elseif ($preco > 1500) {
                    return 149;
                }
                break;
            case 'IMAGEM E SOM':
                if ($preco <= 200) {
                    return 39;
                } elseif (200 < $preco && $preco <= 400) {
                    return 59;
                } elseif ($preco > 400 && $preco <= 600) {
                    return 69;
                } elseif ($preco > 600 && $preco <= 1000) {
                    return 99;
                } elseif ($preco > 1000 && $preco <= 1500) {
                    return 119;
                } elseif ($preco > 1500) {
                    return 169;
                }
                break;
            default:
                return 0;
        }
    }
        protected function getInstallationValue($familia)
    {
        switch ($familia){
            case 'ENCASTRE':
                return 54.90;
            case 'FOGÕES':
                return 39.90;
            case 'ESQUENTADORES/CALDEIRAS':
                return 74.90;
            case 'TERMOACUMULADORES':
                return 64.90;
            case 'AR CONDICIONADO':
                return 180;
            default:
                return 0;
        }
    }
        protected function add_installation_option($product, $value){
        $options = [
            [
                'title' => 'Serviço de instalação',
                'type' => 'checkbox',
                'is_require' => false,
                'sort_order' => 4,
                'values' => [
                    [
                        'title' => 'Instalação de equipamento',
                        'price' => $value,
                        'price_type' => 'fixed',
                        'sort_order' => 0,
                    ],
                ],
            ],
        ];

        foreach ($options as $arrayOption) {
            $option = $this->optionFactory->create();
            $option->setProductId($product->getId())
                ->setStoreId($product->getStoreId())
                ->addData($arrayOption);
            $option->save();
            $product->addOption($option);
        }
        $this->productRepositoryInterface->save($product);
    }
    }



