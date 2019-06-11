<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 31-08-2018
 * Time: 10:21
 */

namespace Mlp\Cli\Helper;


class Category
{
    private $storeManager;
    private $state;
    private $categoryFactory;
    private $categoryRepositoryInterface;
    private $categoryLinkManagement;
    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\App\State $state,
                                \Magento\Catalog\Model\CategoryFactory $categoryFactory,
                                \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepositoryInterface,
                                \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement )
    {
        $this->storeManager = $storeManager;
        $this->state = $state;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->categoryLinkManagement = $categoryLinkManagement;
    }

    public function createCategory($gama,$familia,$subfamlia = null,$categorias)
    {
        try{
            $gamaId = $categorias[$gama];
        }catch (\Exception $ex){
            $newGama = $this->categoryFactory->create();
            $newGama->setName($gama);
            $newGama->setParentId(2);
            $newGama->setIsActive(true);
            $gamaId = $this->categoryRepositoryInterface->save($newGama)->getId();
        }
        try{
            $familiaId = $categorias[$familia];
        }catch (\Exception $ex){
            $newFamilia = $this->categoryFactory->create();
            $newFamilia->setName($familia);
            $newFamilia->setParentId($gamaId);
            $newFamilia->setIsActive(true);
            $familiaId = $this->categoryRepositoryInterface->save($newFamilia)->getId();
        }
        //Se deu erro é porque este tem de ser adicionado
        if ($subfamlia != null){
            try{
                $newSubFamilia = $this->categoryFactory->create();
                $newSubFamilia->setName($subfamlia);
                $newSubFamilia->setParentId($familiaId);
                $newSubFamilia->setIsActive(true);
                $subfamliaId = $this->categoryRepositoryInterface->save($newSubFamilia)->getId();
            } catch (\Exception $ex){
                print_r($ex->getMessage());
            }
        }
    }

    public function getCategoriesArray(){
        $categories = [];
        $categoriesCollection = $this->categoryFactory->create()->getCollection();
        $categoriesCollection->addFieldToSelect('*');
        foreach ($categoriesCollection as $cat){
            $categories[$cat->getName()] = $cat->getId();
        }
        return $categories;
    }


    public function setTelefacCategories($subFamilia,$sku,$logger){
        $categoryId = [];
        $subFamilia = $this->categoryFactory->create()->getCollection()->addAttributeToFilter('name',$subFamilia)->setPageSize(1);
        if ($subFamilia->getSize()) {
            array_push($categoryId,$subFamilia->getFirstItem()->getId());
        }
        try{
            $this->categoryLinkManagement->assignProductToCategories($sku,$categoryId);
        }catch (\Exception $exception){
            $logger->info("Category Exception: ".$sku);
        }
    }

    public function setGamaSorefoz($gama){
        switch ($gama){
            case 'TELEFONES E TELEMÓVEIS':
                return 'COMUNICAÇÕES';
                break;
            case 'SERVIÇOS TV/INTERNET/OUTROS':
                return 'COMUNICAÇÕES';
                break;
            default:
                return $gama;
        }
    }

    public function setFamiliaSorefoz($familia)
    {
        switch ($familia) {
            case 'ENCASTRE - FORNOS':
                return "ENCASTRE";
            case 'ENCASTRE - MESAS':
                return "ENCASTRE";
            case 'ENCASTRE - EXAUSTOR/EXTRATORES':
                return "ENCASTRE";
            case 'ENCASTRE - FRIO':
                return "ENCASTRE";
            case 'ENCASTRE - MAQ.LOUÇA':
                return "ENCASTRE";
            case 'ENCASTRE - MAQ.L.ROUPA':
                return "ENCASTRE";
            case 'ENCASTRE - MICROONDAS':
                return "ENCASTRE";
            case 'ENCASTRE - OUTRAS':
                return "ENCASTRE";
            default:
                return $familia;
        }
    }

    public function setSubFamiliaSorefoz($subFamilia){
            switch ($subFamilia){
                case 'INDEPENDENTES - ELÉCTRICOS':
                    return 'FORNOS';
                case 'PIROLITICOS':
                    return 'FORNOS';
                case 'INDEPENDENTES C/GÁS':
                    return 'FORNOS';
                case 'POLIVALENTES':
                    return 'FORNOS';

                case 'CONVENCIONAIS C/GÁS':
                    return 'PLACAS';
                case 'DE INDUÇÃO':
                    return 'PLACAS';
                case 'VITROCERÂMICAS C/GÁS':
                    return 'PLACAS';
                case 'DOMINÓS C/GÁS':
                    return 'PLACAS';
                case 'VITROCERÂMICAS - ELÉCTRICAS':
                    return 'PLACAS';
                case 'DOMINÓS - ELÉCTRICOS':
                    return 'PLACAS';
                case 'CONVENCIONAIS - ELÉCTRICAS':
                    return 'PLACAS';

                case 'EXAUST.DE CHAMINÉ':
                    return 'EXAUSTORES';
                case 'EXAUST.TELESCÓPICOS':
                    return 'EXAUSTORES';
                case 'EXAUST.CONVENCIONAIS':
                    return 'EXAUSTORES';
                case 'EXTRACTORES':
                    return 'EXAUSTORES';

                case 'MAQ.LAVAR LOUÇA 60 Cm':
                    return 'MÁQUINAS DE LOIÇA';
                case 'MAQ.LAVAR LOUÇA 45 Cm':
                    return 'MÁQUINAS DE LOIÇA';

                case 'TV LED 46"':
                    return 'TV LED+46"';
                case 'TV LED 27"':
                    return 'TV LED 28"';
                case 'TV LED 42"':
                    return 'TV LED+42"';
                case 'MICROONDAS':
                    return 'MICROONDAS ENCASTRE';
                case 'DE SECRETÁRIA':
                    return 'DESKTOPS';

                case 'AR COND.INVERTER':
                case 'AR COND.MULTI-SPLIT':
                    return 'FIXO';
                default:
                    return $subFamilia;
            }
        }
    public function setCategories($gama, $familia, $subfamilia, $name){
        $categories = [];
        //Especifico para alguns artigos que tem categorias totalmente diferentes
        //Informatica Acessorios Acessorios de som
        if (preg_match('/^COLUNA/', $name) == 1) {
            $categories['gama'] = 'IMAGEM E SOM';
            $categories['familia'] = 'COLUNAS';
            $categories['subfamilia'] = null;
            return $categories;
        }
        if (preg_match('/^AUSC/', $name) == 1) {
            $categories['gama'] = 'IMAGEM E SOM';
            $categories['familia'] = 'AUSCULTADORES';
            $categories['subfamilia'] = null;
            return $categories;
        }
        $categories['gama'] = $this->setGamaSorefoz($gama);
        $categories['familia'] = $this->setFamiliaSorefoz($familia);
        $categories['subfamilia'] = $this->setSubFamiliaSorefoz($subfamilia);
        return $categories;
    }

}