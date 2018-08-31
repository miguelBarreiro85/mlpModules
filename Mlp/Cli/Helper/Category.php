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

    public function createCategory($gama,$familia,$subfamlia,$categorias)
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
        $newSubFamilia = $this->categoryFactory->create();
        $newSubFamilia->setName($subfamlia);
        $newSubFamilia->setParentId($familiaId);
        $newSubFamilia->setIsActive(true);
        $familiaId = $this->categoryRepositoryInterface->save($newSubFamilia)->getId();
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

    public function setCategoriesCsv($categories,$gama,$familia,$subFamilia,$sku,$logger){
        $categoryId=[];
        print_r($categories[$subFamilia]);
        try{
            //array_push($categoryId,$categories[$gama]);
            //array_push($categoryId,$categories[$familia]);
            array_push($categoryId,$categories[$subFamilia]);
        }catch (\Exception $e){
            print_r($e."\n".$gama."\n".$familia."\n".$subFamilia."\n");
        }
        try{
            print_r("vou associar as categorias");
            $this->categoryLinkManagement->assignProductToCategories($sku,$categoryId);
            print_r("já associei as categporias");
        }catch (\Exception $exception){
            $logger->info("Category Exception: ".$sku);
            print_r("Set Categories: ".$exception->getMessage());
        }
    }

    public function setCategories($gama,$familia,$subFamilia,$sku,$logger){
        $categoryId = [];
        $subFamilia = $this->categoryFactory->create()->getCollection()->addAttributeToFilter('name',$subFamilia)->setPageSize(1);
        if ($subFamilia->getSize()) {
            print_r($subFamilia->getFirstItem()->getId()."\n");
            array_push($categoryId,$subFamilia->getFirstItem()->getId());
        }
        try{
            $this->categoryLinkManagement->assignProductToCategories($sku,$categoryId);
        }catch (\Exception $exception){
            //$logger->info("Category Exception: ".$sku);
            print_r("Set Categories: ".$exception->getMessage());
        }
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

}