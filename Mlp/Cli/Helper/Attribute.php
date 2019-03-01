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
            //Grandes domesticos
            case 'MAQUINAS LAVAR ROUPA':
                $attributeSet = $this->attributeSetCollection->create()
                    ->addFieldToSelect('attribute_set_id')
                    ->addFieldToFilter('attribute_set_name', 'MLR')
                    ->getFirstItem()
                    ->toArray();
                $attributeSetId = (int) $attributeSet['attribute_set_id'];
                return $attributeSetId;
            case 'MAQUINAS SECAR ROUPA':
                $attributeSet = $this->attributeSetCollection->create()
                    ->addFieldToSelect('attribute_set_id')
                    ->addFieldToFilter('attribute_set_name', 'MSR')
                    ->getFirstItem()
                    ->toArray();
                $attributeSetId = (int) $attributeSet['attribute_set_id'];
                return $attributeSetId;
            case 'MAQUINAS LAVAR LOUÇA':
                $attributeSet = $this->attributeSetCollection->create()
                    ->addFieldToSelect('attribute_set_id')
                    ->addFieldToFilter('attribute_set_name', 'MLL')
                    ->getFirstItem()
                    ->toArray();
                $attributeSetId = (int) $attributeSet['attribute_set_id'];
                return $attributeSetId;
            case 'FOGÕES':
                $attributeSet = $this->attributeSetCollection->create()
                    ->addFieldToSelect('attribute_set_id')
                    ->addFieldToFilter('attribute_set_name', 'FOGÕES')
                    ->getFirstItem()
                    ->toArray();
                $attributeSetId = (int) $attributeSet['attribute_set_id'];
                return $attributeSetId;
            CASE 'MICROONDAS':
                $attributeSet = $this->attributeSetCollection->create()
                    ->addFieldToSelect('attribute_set_id')
                    ->addFieldToFilter('attribute_set_name', 'FOGÕES')
                    ->getFirstItem()
                    ->toArray();
                $attributeSetId = (int) $attributeSet['attribute_set_id'];
                return $attributeSetId;
            case 'FRIGORIFICOS/COMBINADOS':
                $attributeSet = $this->attributeSetCollection->create()
                    ->addFieldToSelect('attribute_set_id')
                    ->addFieldToFilter('attribute_set_name', 'FRIGORIFICOS')
                    ->getFirstItem()
                    ->toArray();
                $attributeSetId = (int) $attributeSet['attribute_set_id'];
                return $attributeSetId;
            case 'CONGELADORES':
                $attributeSet = $this->attributeSetCollection->create()
                    ->addFieldToSelect('attribute_set_id')
                    ->addFieldToFilter('attribute_set_name', 'CONGELADORES')
                    ->getFirstItem()
                    ->toArray();
                $attributeSetId = (int) $attributeSet['attribute_set_id'];
                return $attributeSetId;
            case 'ESQUENTADORES/CALDEIRAS':
                switch ($subfamilia){
                    case 'CALDEIRAS C/GÁS':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'CALDEIRAS_GAS')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                    case 'ESQUENTADORES C/GÁS':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'CALDEIRAS_GAS')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                    case 'ESQUENTADORES - ELÉCTRICOS':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'ESQUENTADORES')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                    default:
                        return 4;//Default
                }
            case 'TERMOACUMULADORES':
                switch ($subfamilia){
                    case 'TERMOACUMULADORES - ELÉCTRICOS':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'TERMOACUMULADORES_ELETRICOS')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                    default:
                        return 4;
                }
            case 'TELEVISÃO':
                $attributeSet = $this->attributeSetCollection->create()
                    ->addFieldToSelect('attribute_set_id')
                    ->addFieldToFilter('attribute_set_name', 'TV')
                    ->getFirstItem()
                    ->toArray();
                $attributeSetId = (int) $attributeSet['attribute_set_id'];
                return $attributeSetId;
            case 'ENCASTRE':
                switch ($subfamilia){
                    case 'FORNOS':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'FORNOS')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                    case 'PLACAS':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'PLACAS')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                    case 'EXAUSTORES':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'EXAUSTORES')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                    case 'MÁQUINAS DE LOIÇA':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'MLL_ENCASTRE')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                    case 'COMBINADOS':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'FRIGORIFICOS')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                    case 'FRIGORIFICOS':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'FRIGORIFICOS')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                    case 'MAQ.LAVAR/SECAR ROUPA':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'MLSR')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                    CASE 'CONGELADORES VERTICAIS':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'CONGELADORES')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                    case 'MAQ.LAVAR ROUPA':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'MLR')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                    case 'MAQ.SECAR ROUPA':
                        $attributeSet = $this->attributeSetCollection->create()
                            ->addFieldToSelect('attribute_set_id')
                            ->addFieldToFilter('attribute_set_name', 'MSR')
                            ->getFirstItem()
                            ->toArray();
                        $attributeSetId = (int) $attributeSet['attribute_set_id'];
                        return $attributeSetId;
                }



            case 'AR CONDICIONADO':
                $attributeSet = $this->attributeSetCollection->create()
                    ->addFieldToSelect('attribute_set_id')
                    ->addFieldToFilter('attribute_set_name', 'AC')
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
            case 'PAINEIS SOLARES':
                $attributeSet = $this->attributeSetCollection->create()
                    ->addFieldToSelect('attribute_set_id')
                    ->addFieldToFilter('attribute_set_name', 'PAINEIS_SOLARES')
                    ->getFirstItem()
                    ->toArray();
                $attributeSetId = (int) $attributeSet['attribute_set_id'];
                return $attributeSetId;



        }
        return 4; //Default

    }
}