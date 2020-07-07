<?php

namespace Mlp\Cli\Helper;

class SqlHelper {

    private $resourceConnection;
    
    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection) {
        $this->resourceConnection = $resourceConnection;
    }

    public function sqlUpdatePrice($sku,$priceAttributeId,$price){
        try {
            $sqlEntityId = 'SELECT entity_id from catalog_product_entity where sku like "'.$sku.'"';
            $connection =  $this->resourceConnection->getConnection();
            $entityId = $connection->fetchAll($sqlEntityId);
            if (!empty($entityId)) {
                $sqlUpdateStatus = 'UPDATE catalog_product_entity_decimal 
                        SET value = '.$price.'
                        WHERE attribute_id = '.$priceAttributeId.' AND entity_id = '.$entityId[0]["entity_id"];
                $connection->query($sqlUpdateStatus);
                print_r("updated price - ");
                return true;
            } else {
                return false;
            }
        }catch(\Exception $e){
            return false;
        }
        
    }

    public function sqlGetAttributeId($attribute) {
        $sqlStatusAttributeId = 'SELECT attribute_id from eav_attribute where attribute_code like "'.$attribute.'"';
        $connection =  $this->resourceConnection->getConnection();
        $statusAttributeId = $connection->fetchAll($sqlStatusAttributeId);
        return $statusAttributeId;
    }

    public function sqlUpdateStatus($sku,$statusId){
        try {
            $sqlEntityId = 'SELECT entity_id from catalog_product_entity where sku = \''.$sku.'\'';
            $connection =  $this->resourceConnection->getConnection();
            $entityId = $connection->fetchAll($sqlEntityId);
            if (!empty($entityId)) {
                $sqlUpdateStatus = 'UPDATE catalog_product_entity_int 
                        SET value = 1
                        WHERE attribute_id = '.$statusId.' AND entity_id = '.$entityId[0]["entity_id"];
                $connection->query($sqlUpdateStatus);
                print_r("Enabled Product - ");
                return true;
            } else {
                return false;
            }
        } catch(\Exception $e){
            return false;
        }
        
    }
}