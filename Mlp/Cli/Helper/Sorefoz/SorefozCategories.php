<?php 

namespace Mlp\Cli\Helper\Sorefoz;

use Mlp\Cli\Helper\CategoriesConstants as Cat;

class SorefozCategories {

    public function getCategoriesSorefoz($gama,$familia,$subFamilia,$logger,$sku)
    {
        switch ($gama) {
            case 'ACESSÓRIOS E BATERIAS':
                switch ($familia) {
                    case 'ACESSÓRIOS E BATERIAS':
                        switch ($subFamilia) {
                            case 'ACESSÓRIOS':
                                $gama = Cat::ELECTRICIDADE;
                                $familia = Cat::OUTROS_ACESSORIOS;
                                $subFamilia = null;
                                $logger->info(Cat::VERIFICAR_SUBFAMILIA.$sku);
                                return [$gama,$familia,$subFamilia];
                            case 'BATERIAS':
                                $gama = Cat::ELECTRICIDADE;
                                $familia = Cat::PILHAS_BATERIAS;
                                $subFamilia = Cat::BATERIAS;
                                $logger->info(Cat::VERIFICAR_SUBFAMILIA.$sku);
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    default:
                        return [$gama,$familia,$subFamilia];
                }
            case 'TELEFONES E TELEMÓVEIS':
            case 'SERVIÇOS TV/INTERNET/OUTROS':
                $gama = Cat::COMUNICACOES;
                return [$gama,$familia,$subFamilia];
            case 'GRANDES DOMÉSTICOS':
                switch ($familia) {
                    case 'ENCASTRE - FORNOS':
                        switch ($subFamilia) {
                            case 'INDEPENDENTES - ELÉCTRICOS':
                            case 'PIROLITICOS':
                            case 'INDEPENDENTES C/GÁS':
                            case 'POLIVALENTES':
                                $familia = Cat::ENCASTRE;
                                $subfamilia = Cat::FORNOS;
                                return [$gama, $familia, $subfamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    case 'ENCASTRE - MESAS':
                        switch ($subFamilia) {
                            case 'CONVENCIONAIS C/GÁS':
                            case 'DE INDUÇÃO':
                            case 'VITROCERÂMICAS C/GÁS':
                            case 'DOMINÓS C/GÁS':
                            case 'VITROCERÂMICAS - ELÉCTRICAS':
                            case 'DOMINÓS - ELÉCTRICOS':                                
                            case 'CONVENCIONAIS - ELÉCTRICAS':
                                $familia = Cat::ENCASTRE;
                                $subFamilia = Cat::PLACAS;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    case 'ENCASTRE - EXAUSTOR/EXTRATORES':
                        switch($subFamilia){
                            case 'EXAUST.DE CHAMINÉ':
                            case 'EXAUST.TELESCÓPICOS':
                            case 'EXAUST.CONVENCIONAIS':
                            case 'EXTRACTORES':
                                $familia = Cat::ENCASTRE;
                                $subFamilia = Cat::EXAUSTORES;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    case 'ENCASTRE - FRIO':
                        $familia = Cat::ENCASTRE;
                        return [$gama,$familia,$subFamilia];
                    case 'ENCASTRE - MAQ.LOUÇA':
                        switch ($subFamilia) {
                            case 'MAQ.LAVAR LOUÇA 60 Cm':
                            case 'MAQ.LAVAR LOUÇA 45 Cm':
                                $familia = Cat::ENCASTRE;
                                $subFamilia = Cat::MAQUINAS_DE_LOUCA_ENCASTRE;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    case 'ENCASTRE - MAQ.L.ROUPA':
                    case 'ENCASTRE - MICROONDAS':
                        $familia = Cat::ENCASTRE;
                        $subFamilia = Cat::MICROONDAS_ENCASTRE;
                        return [$gama,$familia,$subFamilia];
                    case 'ENCASTRE - OUTRAS':
                        $familia = Cat::ENCASTRE;
                        return [$gama,$familia,$subFamilia];
                    default:
                        return [$gama,$familia,$subFamilia];
                }        
            case 'IMAGEM E SOM':
                switch ($subFamilia) {
                    case 'TV LED 46"':
                        $subFamilia = Cat::TV_LED_M46;
                        return [$gama,$familia,$subFamilia];
                    case 'TV LED 27"':
                        $subFamilia = Cat::TV_LED_28;
                        return [$gama,$familia,$subFamilia];
                    case 'TV LED 42"':
                        $subFamilia = Cat::TV_LED_M42;
                        return [$gama,$familia,$subFamilia];
                    default:
                        return [$gama,$familia,$subFamilia];
                    
                }
            case 'INFORMÁTICA':
                switch ($subFamilia) {
                    case 'DE SECRETÁRIA':
                        $subFamilia = Cat::DESKTOPS;
                        return [$gama,$familia,$subFamilia];
                    default:
                        return [$gama,$familia,$subFamilia];
                }
            case 'CLIMATIZAÇÃO':
                switch ($subFamilia) {
                    case 'AR COND.INVERTER':
                    case 'AR COND.MULTI-SPLIT':
                         $subFamilia = Cat::AC_FIXO;
                         return [$gama,$familia,$subFamilia];
                    default:
                        return [$gama,$familia,$subFamilia];
                }
            case 'PEQUENOS DOMÉSTICOS':
                switch ($familia) {
                    case 'APARELHOS DE COZINHA':
                        switch ($subFamilia) {
                            case 'FORNOS':
                                $subFamilia = Cat::FORNOS_DE_BANCADA;
                                return [$gama,$familia,$subFamilia];
                            default:
                                return [$gama,$familia,$subFamilia];
                        }
                    default:
                        return [$gama,$familia,$subFamilia];
                }
            default:
                return [$gama,$familia,$subFamilia];
        }
    }

    public function setCategories($gama, $familia, $subfamilia, $name,$logger,$sku)
    {
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
        if (preg_match('/^CLIMATIZADOR/', $name) == 1) {
            $categories['gama'] = 'CLIMATIZAÇÃO';
            $categories['familia'] = 'AR CONDICIONADO';
            $categories['subfamilia'] = 'CLIMATIZADORES';
            return $categories;
        }
        $categories = $this->getCategoriesSorefoz($gama,$familia,$subfamilia,$logger,$sku);
        //$categories['gama'] = $this -> setGamaSorefoz($gama);
        //$categories['familia'] = $this -> setFamiliaSorefoz($familia);
        //$categories['subfamilia'] = $this -> setSubFamiliaSorefoz($familia,$subfamilia);
        return $categories;
    }
}