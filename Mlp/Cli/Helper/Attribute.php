<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 14-07-2018
 * Time: 12:58
 */

namespace Mlp\Cli\Helper;


class Attribute
{

    private $valueFactory;

    private $dataAttributeOptions;

    private $attributeSetCollection;

    public function __construct(\Magento\Framework\Api\AttributeValueFactory $valueFactory,
                                \Mlp\Cli\Helper\Data $dataAttributeOptions,
                                \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection)
    {
        $this->valueFactory = $valueFactory;
        $this->dataAttributeOptions = $dataAttributeOptions;
        $this->attributeSetCollection = $attributeSetCollection;
    }

    public function addSorefozAttributes($description , $familia, $subfamilia)
    {
        $attributes = [];
        switch ($familia){
            case 'TELEVISÃO':
                preg_match('/Diagonal de Ecrã: (\d+) cm/',$description,$matches);
                if (isset($matches[1])){
                    $optionId = $this->dataAttributeOptions->createOrGetId('diagonal',$matches[1]);
                    $attributeValue['attribute_code']='diagonal';
                    $attributeValue['option_id'] = $optionId;
                    array_push($attributes,$attributeValue);
                    return $attributes;
                }
                break;
        }
        switch ($subfamilia){
            case 'PORTÁTEIS':
                preg_match('/Processador: (\w+)/',$description,$matches);
                if (isset($matches[1])){
                    $optionId = $this->dataAttributeOptions->createOrGetId('processador',$matches[1]);
                    $attributeValue['attribute_code']='processador';
                    $attributeValue['option_id'] = $optionId;
                    array_push($attributes,$attributeValue);
                }
                preg_match('/Memória RAM: (\w+)/',$description,$matches2);
                if (isset($matches2[1])){
                    $optionId = $this->dataAttributeOptions->createOrGetId('ram',$matches2[1]);
                    $attributeValue['attribute_code']='ram';
                    $attributeValue['option_id'] = $optionId;
                    array_push($attributes,$attributeValue);
                }
                preg_match('/Capacidade: (\d+)/',$description,$matches3);
                if (isset($matches3[1])){
                    $optionId = $this->dataAttributeOptions->createOrGetId('disco',$matches3[1]);
                    $attributeValue['attribute_code']='disco';
                    $attributeValue['option_id'] = $optionId;
                    array_push($attributes,$attributeValue);
                }
                return $attributes;
                break;
        }
    }

    public function getAttributeSetId($familia,$subfamilia){
        switch ($familia){
            case 'TELEVISÃO':
                $attributeSet = $this->attributeSetCollection->create()
                    ->addFieldToSelect('attribute_set_id')
                    ->addFieldToFilter('attribute_set_name', 'TV')
                    ->getFirstItem()
                    ->toArray();
                $attributeSetId = (int) $attributeSet['attribute_set_id'];
                return $attributeSetId;
        }
        switch ($subfamilia){
            case 'PORTÁTEIS':
                $attributeSet = $this->attributeSetCollection->create()
                    ->addFieldToSelect('attribute_set_id')
                    ->addFieldToFilter('attribute_set_name', 'PC')
                    ->getFirstItem()
                    ->toArray();
                $attributeSetId = (int) $attributeSet['attribute_set_id'];
                return $attributeSetId;
        }
        return 4; //Default

    }
}