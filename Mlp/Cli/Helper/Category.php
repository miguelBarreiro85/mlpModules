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
    private $storeManager;
    private $state;
    private $categoryFactory;
    private $categoryRepositoryInterface;
    private $categoryLinkManagement;
    private $categoryLinkRepositoryInterface;

    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\App\State $state,
                                \Magento\Catalog\Model\CategoryFactory $categoryFactory,
                                \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepositoryInterface,
                                \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement,
                                \Magento\Catalog\Api\CategoryLinkRepositoryInterface $categoryLinkRepositoryInterface)
    {
        $this -> storeManager = $storeManager;
        $this -> state = $state;
        $this -> categoryFactory = $categoryFactory;
        $this -> categoryRepositoryInterface = $categoryRepositoryInterface;
        $this -> categoryLinkManagement = $categoryLinkManagement;
        $this->categoryLinkRepositoryInterface = $categoryLinkRepositoryInterface;
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

    public function changeProductCategories($oldCat, $newCat) {
        print_r($oldCat. " - ");
        $categories = $this->getCategoriesArray();
        $oldCatID = $categories[$oldCat];
        $newCatId = $categories[$newCat];
        $categoryLinks = $this->categoryLinkManagement->getAssignedProducts($oldCatID);
        $row = 0;
        foreach($categoryLinks as $categoryLink){
            $sku = $categoryLink->getSku();
            print_r($row++." - ".$sku." \n");
            try{
                $this->categoryLinkRepositoryInterface->deleteByIds($oldCatID, $sku);
            }catch(\Exception $e){
                print_r($e->getMessage());
            }
            $categoryLink->setCategoryId($newCatId);
            $this->categoryLinkRepositoryInterface->save($categoryLink);
        }
    }
}
