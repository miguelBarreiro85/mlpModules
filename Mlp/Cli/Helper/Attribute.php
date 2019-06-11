<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 14-07-2018
 * Time: 12:58
 */

namespace Mlp\Cli\Helper;


use Braintree\Exception;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Mlp\Cli\Helper\Data;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Model\AttributeSetManagement;
use Magento\Eav\Model\AttributeSetRepository;


class Attribute
{

    private $valueFactory;

    private $dataAttributeOptions;

    private $attributeSetCollection;

    private $eavTypeFactory;

    private $attributeSetFactory;

    private $attributeSetManagement;

    public function __construct(\Magento\Framework\Api\AttributeValueFactory $valueFactory,
                                Data $dataAttributeOptions,
                                CollectionFactory $attributeSetCollection,
                                typeFactory $eavTypeFactory,
                                SetFactory $attributeSetFactory,
                                AttributeSetManagement $attributeSetManagement)
    {
        $this->valueFactory = $valueFactory;
        $this->dataAttributeOptions = $dataAttributeOptions;
        $this->attributeSetCollection = $attributeSetCollection;
        $this->eavTypeFactory = $eavTypeFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeSetManagement = $attributeSetManagement;
    }

    public function addSorefozAttributes($description, $familia, $subfamilia)
    {
        $attributes = [];
        switch ($familia) {
            case 'TELEVISÃO':
                preg_match('/Diagonal de Ecrã: (\d+) cm/', $description, $matches);
                if (isset($matches[1])) {
                    $optionId = $this->dataAttributeOptions->createOrGetId('diagonal', $matches[1]);
                    $attributeValue['attribute_code'] = 'diagonal';
                    $attributeValue['option_id'] = $optionId;
                    array_push($attributes, $attributeValue);
                    return $attributes;
                }
                break;
        }
        switch ($subfamilia) {
            case 'PORTÁTEIS':
                preg_match('/Processador: (\w+)/', $description, $matches);
                if (isset($matches[1])) {
                    $optionId = $this->dataAttributeOptions->createOrGetId('processador', $matches[1]);
                    $attributeValue['attribute_code'] = 'processador';
                    $attributeValue['option_id'] = $optionId;
                    array_push($attributes, $attributeValue);
                }
                preg_match('/Memória RAM: (\w+)/', $description, $matches2);
                if (isset($matches2[1])) {
                    $optionId = $this->dataAttributeOptions->createOrGetId('ram', $matches2[1]);
                    $attributeValue['attribute_code'] = 'ram';
                    $attributeValue['option_id'] = $optionId;
                    array_push($attributes, $attributeValue);
                }
                preg_match('/Capacidade: (\d+)/', $description, $matches3);
                if (isset($matches3[1])) {
                    $optionId = $this->dataAttributeOptions->createOrGetId('disco', $matches3[1]);
                    $attributeValue['attribute_code'] = 'disco';
                    $attributeValue['option_id'] = $optionId;
                    array_push($attributes, $attributeValue);
                }
                return $attributes;
                break;
        }
    }

    public function getAttributeSetId($familia, $subfamilia)
    {
        try {
            switch ($familia) {
                //Imagem SOM
                case 'TELEVISÃO':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'TV')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'CÂMARAS':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'CAMARAS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'SIST.HOME CINEMA':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'HOME_CINEMA')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'DVD /BLURAY /TDT':
                    switch ($subfamilia){
                        case 'LEITOR DE DVD':
                        case 'LEITOR BLURAY/HDDVD':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'LEITOR_DVD')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'RECEPTORES TDT':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'TDT')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                    }

                case 'DVD /BLURAY /TDT':
                //Informatica
                case 'COMPUTADORES E TABLET\'S':
                    switch ($subfamilia) {
                        case 'PORTÁTEIS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'PORTATEIS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'TABLET\'S':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'TABLETS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'CALCULADORAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'CALCULADORAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'DESKTOPS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'COMPUTADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'ACESSÓRIOS':
                            switch ($subfamilia){
                                case 'RATOS':
                                    $attributeSet = $this->attributeSetCollection->create()
                                        ->addFieldToSelect('attribute_set_id')
                                        ->addFieldToFilter('attribute_set_name', 'RATOS')
                                        ->getFirstItem()
                                        ->toArray();
                                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                                    return $attributeSetId;
                                case 'TECLADOS':
                                    $attributeSet = $this->attributeSetCollection->create()
                                        ->addFieldToSelect('attribute_set_id')
                                        ->addFieldToFilter('attribute_set_name', 'TECLADOS')
                                        ->getFirstItem()
                                        ->toArray();
                                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                                    return $attributeSetId;
                            }
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'COMPUTADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;


                    }
                case 'MONITORES':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'MONITORES')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'MEMÓRIAS':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'MEMORIAS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'IMPRESSORAS':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'IMPRESSORAS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'GPS':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'GPS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                //COMUNICAÇÕES
                case 'TELEMÓVEIS / CARTÕES':
                    switch ($subfamilia) {
                        case 'TELEMÓVEIS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'TELEMOVEIS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;
                    }
                //Pequenos Domésticos
                case 'APARELHOS DE COZINHA':
                    switch ($subfamilia) {
                        case 'GRELHADORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'GRELHADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'BALANÇAS DE COZINHA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'BALANCAS_COZINHA')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                    }
                case 'CUIDADO DE ROUPA':
                    switch ($subfamilia) {
                        case 'FERROS A VAPOR':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'FERROS_VAPOR')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'FERROS COM CALDEIRA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'FERROS_CALDEIRA')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;

                    }
                case 'ASSEIO PESSOAL':
                    switch ($subfamilia) {
                        case 'SECADORES DE CABELO':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'SECADORES_CABELO')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'BALANÇAS DE W.C.':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'BALANCAS_WC')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MODELADORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MODULADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'DEPILADORAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'DEPILADORAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MAQUINAS DE BARBEAR':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MAQUINAS_BARBEAR')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'APARADORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'APARADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                    }
                case 'APARELHOS DE LIMPEZA':
                    switch ($subfamilia) {
                        case 'ASPIRADOR SEM SACO':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'ASPIRADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'ASPIRADOR COM SACO':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'ASPIRADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                    }
                case 'ARTIGOS DE MENAGE':
                    switch ($subfamilia) {

                    }


                //Grandes domesticos
                case 'MAQUINAS LAVAR ROUPA':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'MLR')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'MAQUINAS SECAR ROUPA':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'MSR')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'MAQUINAS LAVAR LOUÇA':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'MLL')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'FOGÕES':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'FOGÕES')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'MICROONDAS':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'MICROONDAS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'FRIGORIFICOS/COMBINADOS':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'FRIGORIFICOS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'CONGELADORES':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'CONGELADORES')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'ESQUENTADORES/CALDEIRAS':
                    switch ($subfamilia) {
                        case 'CALDEIRAS C/GÁS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'CALDEIRAS_GAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'ESQUENTADORES C/GÁS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'ESQUENTADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'ESQUENTADORES - ELÉCTRICOS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'ESQUENTADORES_ELETRICOS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;//Default
                    }
                case 'TERMOACUMULADORES':
                    switch ($subfamilia) {
                        case 'TERMOACUMULADORES - ELÉCTRICOS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'TERMOACUMULADORES_ELETRICOS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;
                    }
                case 'ENCASTRE':
                    switch ($subfamilia) {
                        case 'FORNOS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'FORNOS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'PLACAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'PLACAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'EXAUSTORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'EXAUSTORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MÁQUINAS DE LOIÇA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MLL_ENCASTRE')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'COMBINADOS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'FRIGORIFICOS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'FRIGORIFICOS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'FRIGORIFICOS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MAQ.LAVAR/SECAR ROUPA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MLSR')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        CASE 'CONGELADORES VERTICAIS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'CONGELADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MAQ.LAVAR ROUPA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MLR')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'GARRAFEIRAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'GARRAFEIRAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MISTURADORAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MISTURADORAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'LAVA LOUÇAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'LAVA_LOUÇAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'MAQ.SECAR ROUPA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'MSR')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;
                    }
                //CLIMATIZAÇÃO
                case 'AQUECIMENTO':
                    switch ($subfamilia) {
                        case 'SALAMANDRAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'SALAMANDRAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'EMISSORES TÉRMICOS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'EMISSORES_TERMICOS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'RECUPERADORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'RECUPERADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'CONVECTORES/TERMOVENT':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'TERMOVENTILADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'RADIADORES A OLEO':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'RADIADORES_OLEO')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                    }
                case 'TRATAMENTO DE AR':
                    switch ($subfamilia){
                        case 'DESUMIDIFICADORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'DESUMIDIFICADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'HUMIDIFICADORES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'HUMIDIFICADORES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                    }

                case 'AR CONDICIONADO':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'AC')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'SISTEMAS AQUEC.SOLAR':
                    switch ($subfamilia) {
                        case 'PAINEIS SOLARES':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'PAINEIS_SOLARES')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        case 'ACUM.DE ÁGUA':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'ACUMULADORES_AGUA')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;
                    }
                case 'VENTILAÇÃO':
                    switch ($subfamilia) {
                        case 'VENTOINHAS':
                            $attributeSet = $this->attributeSetCollection->create()
                                ->addFieldToSelect('attribute_set_id')
                                ->addFieldToFilter('attribute_set_name', 'VENTOINHAS')
                                ->getFirstItem()
                                ->toArray();
                            $attributeSetId = (int)$attributeSet['attribute_set_id'];
                            return $attributeSetId;
                        default:
                            return 4;
                    }
                //CAR AUDIO
                case 'AUTO-RADIOS':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'AUTO_RADIOS')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'ALTIFALANTES':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'AUTO_ALTIFALANTES')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'SISTEMAS NAVEGAÇÃO':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'AUTO_SISTEMAS NAVEGACAO')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                case 'AMPLIFICADORES':
                    $attributeSet = $this->attributeSetCollection->create()
                        ->addFieldToSelect('attribute_set_id')
                        ->addFieldToFilter('attribute_set_name', 'AUTO_AMPLIFICADORES')
                        ->getFirstItem()
                        ->toArray();
                    $attributeSetId = (int)$attributeSet['attribute_set_id'];
                    return $attributeSetId;
                default:
                    return 4;
            }
        }catch (\Exception $ex){
            /*
            $entityTypeCode = 'catalog_product';
            $entityType     = $this->eavTypeFactory->create()->loadByCode($entityTypeCode);
            $defaultSetId   = $entityType->getDefaultAttributeSetId();

            $attributeSet = $this->attributeSetFactory->create();
            $data = [
                'attribute_set_name'    => $familia,
                'entity_type_id'        => $entityType->getId(),
                'sort_order'            => 200,
            ];
            $attributeSet->setData($data);

            $newAttribute = $this->attributeSetManagement->create($entityTypeCode, $attributeSet, $defaultSetId);

            return $newAttribute->getAttributeSetId();
            */
            return 4;

        }

    }

    public function getSpecialAttributes($gama,$familia,$subfamilia, $description, $name)
    {
        $attributes = [];
        switch ($gama) {
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
                    case 'ENCASTRE - MAQ.L.ROUPA':
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
                    case 'ENCASTRE - MAQ.LOUÇA':
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
                        }
                        if (preg_match('/(A\+{1,3})/', $name, $matches2) == 1) {
                            $attribute2['code'] = 'eficiencia_energetica';
                            $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches2[1]));
                            array_push($attributes, $attribute2);
                            if (preg_match('/(A\+{1,3})/', $name, $matches2) == 1) {
                                $attribute2['code'] = 'eficiencia_energetica';
                                $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches2[1]));
                                array_push($attributes, $attribute2);
                            }
                        } elseif (preg_match('/Classe Energética: (\w)/', html_entity_decode(strip_tags($description)), $matches3) == 1) {
                            $attribute3['code'] = 'eficiencia_energetica';
                            $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches3[1]));
                            array_push($attributes, $attribute3);
                        }
                        if (preg_match('/(\d+)K/', $name, $matches4) == 1) {
                            $attribute4['code'] = 'capacidade_kg';
                            $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('capacidade_kg', $matches4[1] . " kg");
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
                    case 'CONGELADORES':
                        if (preg_match('/(\d+,*\d*x\d+,*\d*)/', $name, $matches1) == 1) {
                            $attribute1['code'] = 'medidas';
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('medidas', $matches1[1]);
                            array_push($attributes, $attribute1);
                        }
                        if (preg_match('/Cor: (\w+)/ui', strip_tags($description), $matches2) == 1) {
                            $attribute2['code'] = 'color';
                            $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($matches2[1]));
                            array_push($attributes, $attribute2);
                        }
                        if (preg_match('/(A\+{1,3})/', $name, $matches3) == 1) {
                            $attribute3['code'] = 'eficiencia_energetica';
                            $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches3[1]));
                            array_push($attributes, $attribute3);
                        } elseif (preg_match('/Classe Energética: (\w\S*\s*\S*)/', html_entity_decode(strip_tags($description)), $matches4) == 1) {
                            $eficiencia = $this->getClasseEnergetica($matches4[1]);
                            $attribute4['code'] = 'eficiencia_energetica';
                            $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', $eficiencia);
                            array_push($attributes, $attribute4);
                        }
                        return $attributes;
                    case 'ENCASTRE - FRIO':
                    case 'FRIGORIFICOS/COMBINADOS':
                        if (preg_match('/Largura:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches1) == 1) {
                            $attribute1['code'] = 'largura';
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('largura', $matches1[1] );
                            array_push($attributes, $attribute1);
                        }
                        if (preg_match('/Profundidade:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches2) == 1) {
                            $attribute2['code'] = 'profundidade';
                            $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('profundidade', $matches2[1]);
                            array_push($attributes, $attribute2);
                        }
                        if (preg_match('/Altura:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches3) == 1) {
                            $attribute3['code'] = 'altura';
                            $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('altura', $matches3[1]);
                            array_push($attributes, $attribute3);
                        }
                        if (preg_match('/Cor:\s*(\w+)/ui', strip_tags($description), $matches4) == 1) {
                            $attribute4['code'] = 'color';
                            $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($matches4[1]));
                            array_push($attributes, $attribute4);
                        }
                        if (preg_match('/(A\+{1,3})/', $name, $matches5) == 1) {
                            $attribute5['code'] = 'eficiencia_energetica';
                            $attribute5['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches5[1]));
                            array_push($attributes, $attribute5);
                        } elseif (preg_match('/Classe Energética:\s*(\w\W*\d*\s*\W*\d*\s\W*\d*).*Con/', strip_tags($description), $matches6) == 1) {
                            $eficiencia = $this->getClasseEnergetica($matches6[1]);
                            $attribute6['code'] = 'eficiencia_energetica';
                            $attribute6['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', $eficiencia);
                            array_push($attributes, $attribute6);
                        }
                        return $attributes;
                    case 'ENCASTRE - FORNOS':
                        if (preg_match('/(A\+{1,3})/', $name, $matches1) == 1) {
                            $attribute1['code'] = 'eficiencia_energetica';
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', trim($matches1[1]));
                            array_push($attributes, $attribute1);
                        } elseif (preg_match('/nergética:\s*(\w\W*\d*\s*\W*\d*\s\W*\d*).*Con/', strip_tags($description), $matches2) == 1) {
                            $eficiencia = $this->getClasseEnergetica($matches2[1]);
                            $attribute2['code'] = 'eficiencia_energetica';
                            $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('eficiencia_energetica', $eficiencia);
                            array_push($attributes, $attribute2);
                        }
                        if (preg_match('/Cor:\s*(\w+)/ui', strip_tags($description), $matches3) == 1) {
                            $attribute3['code'] = 'color';
                            $attribute3['value'] = $this->dataAttributeOptions->createOrGetId('color', trim($matches3[1]));
                            array_push($attributes, $attribute3);
                        }
                        if (preg_match('/Forno:\s*(\d+ Litros)/ui', strip_tags($description), $matches4) == 1) {
                            $attribute4['code'] = 'capacidade_forno';
                            $attribute4['value'] = $this->dataAttributeOptions->createOrGetId('capacidade_forno', trim($matches4[1]));
                            array_push($attributes, $attribute4);
                        }
                        if (preg_match('/Forno: (\w+)/ui', strip_tags($description), $matches5) == 1) {
                            $attribute5['code'] = 'tipo_forno';
                            $attribute5['value'] = $this->dataAttributeOptions->createOrGetId('tipo_forno', trim($matches5[1]));
                            array_push($attributes, $attribute5);
                        }
                        if (preg_match('/Largura:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches6) == 1) {
                            $attribute6['code'] = 'largura';
                            $attribute6['value'] = $this->dataAttributeOptions->createOrGetId('largura', $matches6[1] );
                            array_push($attributes, $attribute6);
                        }
                        if (preg_match('/Profundidade:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches7) == 1) {
                            $attribute7['code'] = 'profundidade';
                            $attribute7['value'] = $this->dataAttributeOptions->createOrGetId('profundidade', $matches7[1]);
                            array_push($attributes, $attribute7);
                        }
                        if (preg_match('/Altura:\s*(\d*,*\d*\s*cm)/', strip_tags($description), $matches8) == 1) {
                            $attribute8['code'] = 'altura';
                            $attribute8['value'] = $this->dataAttributeOptions->createOrGetId('altura', $matches8[1]);
                            array_push($attributes, $attribute8);
                        }
                        return $attributes;
                    case 'ENCASTRE - EXAUSTOR/EXTRATORES':
                        if (strcmp($subfamilia, 'EXAUST.DE CHAMINÉ') == 0){
                            $attribute1['code'] = 'tipo_exaustor';
                            $attribute1['value'] = $this->dataAttributeOptions->createOrGetId('tipo_exaustor', 'chaminé');
                            array_push($attributes, $attribute1);
                        }elseif (strcmp($subfamilia, 'EXAUST.TELESCÓPICOS') == 0){

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
                                            if (preg_match('/Arrefecimento: (\d+)./', $description, $matches) == 1) {
                                                $potencia = $this->getPotencia((int)$matches[1]);
                                                if ($potencia != null) {
                                                    $attribute2['code'] = 'potencia_ac_int';
                                                    $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('potencia_ac_int',
                                                        $potencia);
                                                    array_push($attributes, $attribute2);
                                                }

                                            }
                                            return $attributes;
                                        } elseif (preg_match('/UNID.EXT/', $name) == 1) {
                                            if (preg_match('/Arrefecimento: (\d+)./', $description, $matches) == 1) {
                                                $potencia = $this->getPotencia((int)$matches[1]);
                                                if ($potencia != null) {
                                                    $attribute2['code'] = 'potencia_ac_ext';
                                                    $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('potencia_ac_ext',
                                                        $potencia);
                                                    array_push($attributes, $attribute2);
                                                }

                                            }
                                            return $attributes;
                                        } else {
                                            if (preg_match('/Arrefecimento: (\d+)./', $description, $matches) == 1) {
                                                $potencia = $this->getPotencia((int)$matches[1]);
                                                if ($potencia != null) {
                                                    $attribute2['code'] = 'potencia_ac_conj';
                                                    $attribute2['value'] = $this->dataAttributeOptions->createOrGetId('potencia_ac_conj',
                                                        $potencia);
                                                    array_push($attributes, $attribute2);
                                                }

                                            }
                                            return $attributes;
                                        }
                                }
                        }
                }
            case 'IMAGEM E SOM':

        }
    }

    private function getClasseEnergetica($text){
        print_r("classe energetica text: ".$text."\n");
        return 'A';
    }
}