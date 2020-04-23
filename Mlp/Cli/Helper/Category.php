<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 31-08-2018
 * Time: 10:21
 */

namespace Mlp\Cli\Helper;


use Exception;
use Magento\Newsletter\Model\SubscriberFactory;
use phpDocumentor\Reflection\Types\Self_;
use Vertex\Data\Seller;

class Category
{
   
    private $categoryFactory;
    private $categoryRepositoryInterface;
    private $categoryLinkManagement;
    private $categoryLinkRepositoryInterface;
    private $productRepositoryInterface;
    private $registry;

    public function __construct(\Magento\Framework\Registry $registry,
                                \Magento\Catalog\Model\CategoryFactory $categoryFactory,
                                \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepositoryInterface,
                                \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement,
                                \Magento\Catalog\Api\CategoryLinkRepositoryInterface $categoryLinkRepositoryInterface,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface)
    {
        $this -> categoryFactory = $categoryFactory;
        $this -> categoryRepositoryInterface = $categoryRepositoryInterface;
        $this -> categoryLinkManagement = $categoryLinkManagement;
        $this->categoryLinkRepositoryInterface = $categoryLinkRepositoryInterface;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->registry = $registry;
    }

    public function createCategory($gama, $familia, $subfamlia = null, $categorias)
    {
        try {
            $gamaId = $categorias[$gama];
        } catch (\Exception $ex) {
            $newGama = $this -> categoryFactory -> create();
            $newGama -> setName($gama);
            $newGama -> setParentId(2);
            $newGama -> setIsActive(true);
            $newGama -> setIsAnchor(false);
            $gamaId = $this -> categoryRepositoryInterface -> save($newGama) -> getId();
            
        }
        try {
            $familiaId = $categorias[$familia];
        } catch (\Exception $ex) {
            $newFamilia = $this -> categoryFactory -> create();
            $newFamilia -> setName($familia);
            $newFamilia -> setParentId($gamaId);
            $newFamilia -> setIsActive(true);
            $newFamilia -> setIsAnchor(false);
            $familiaId = $this -> categoryRepositoryInterface -> save($newFamilia) -> getId();
        }
        //Se deu erro é porque este tem de ser adicionado
        if ($subfamlia != null) {
            $newSubFamilia = $this -> categoryFactory -> create();
            $newSubFamilia -> setName($subfamlia);
            $newSubFamilia -> setParentId($familiaId);
            $newSubFamilia -> setIsActive(true);
            $newSubFamilia -> setIsAnchor(true);
            $this -> categoryRepositoryInterface -> save($newSubFamilia);           
        }
        
    }

    public function getCategoriesArray()
    {
        $categories = [];
        $categoriesCollection = $this -> categoryFactory -> create() -> getCollection();
        $categoriesCollection -> addFieldToSelect('*');
        foreach ($categoriesCollection as $cat) {
            $categories[$cat -> getName()] = $cat -> getId();
        }
        return $categories;
    }

    public function changeProductCategories($oldCatId, $newCatId) {
        print_r($oldCatId. " - ");
        $categoryLinks = $this->categoryLinkManagement->getAssignedProducts($oldCatId);
        $row = 0;
        foreach($categoryLinks as $categoryLink){
            $sku = $categoryLink->getSku();
            print_r($row++." - ".$sku." \n");
            try{
                $this->categoryLinkRepositoryInterface->deleteByIds($oldCatId, $sku);
            }catch(\Exception $e){
                print_r($e->getMessage());
            }
            $categoryLink->setCategoryId($newCatId);
            $this->categoryLinkRepositoryInterface->save($categoryLink);
        }
    }

    public function deleteProductsByCategoryId($catId) {
        //Para apagar é preciso registar
        print_r(": ".$catId);
        $this->registry->unregister('isSecureArea');
        $this->registry->register('isSecureArea', true);
        $categoryLinks = $this->categoryLinkManagement->getAssignedProducts($catId);
        $row = 0;
        foreach($categoryLinks as $categoryLink){
            $sku = $categoryLink->getSku();
            print_r($row++." - ".$sku." \n");
            try{
                $this->productRepositoryInterface->deleteById($sku);
            }catch(\Exception $e){
                print_r($e->getMessage());
            }
        }
    }
}
