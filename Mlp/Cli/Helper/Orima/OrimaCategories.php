<?php 

namespace Mlp\Cli\Helper\Orima;

use Mlp\Cli\Helper\CategoriesConstants as Cat;

class OrimaCategories {

    public static function getCategoriesOrima($gama, $familia, $subFamilia,$logger,$sku)
    {
        switch ($gama) {
            case 'ACESSORIOS':
                switch ($familia) {
                    case 'ACESSORIOS':
                        $gama = Cat::ELECTRICIDADE;
                        $familia = Cat::OUTROS_ACESSORIOS;
                        $subFamilia = null;
                        return [$gama,$familia,$subFamilia];
                    default:
                        return [$gama,$familia,$subFamilia];
                }
            case 'QUEIMA':
                $gama = Cat::GRANDES_DOMESTICOS;
                switch ($familia) {
                    case 'FOGAREIROS':
                        $gama = Cat::PEQUENOS_DOMESTICOS;
                        $familia = Cat::APARELHOS_DE_COZINHA;
                        $subFamilia = Cat::FOGAREIROS;
                        return ([$gama, $familia, $subFamilia]);
                    case 'FOGOES':
                        $familia = Cat::FOGOES;
                        switch ($subFamilia) {
                            case 'FOGÃO MONOBLOCO 50X60':
                            case 'FOGAO MONOBLOCO 53,5X56,5':
                            case 'FOGOES BAIXOS 50X55':
                            case 'FOGOES MAXI FORNO':
                            case 'FOGOES MONOBLOCO 50/55CM':
                            case 'FOGOES MONOBLOCO 60CM':
                            case 'FOGOES PORTA GARRAFA':
                                $subFamilia = Cat::FOGÕES_C_GÁS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'FOGOES VITROCERAMICOS':
                                $subFamilia = Cat::FOGOES_ELECTRICOS;
                                return ([$gama, $familia, $subFamilia]);
                            default:
                                print_r("SubFamilia not found".$subFamilia."\n");
                                return ([$gama, $familia, $subFamilia]);
                        }
                    default:
                        return ([$gama, $familia, $subFamilia]);
                }

            case 'MAQUINAS ROUPA':
                $gama = Cat::GRANDES_DOMESTICOS;
                switch ($familia) {
                    case 'MAQUINAS LAVAR ROUPA':
                        $familia = Cat::MAQ_ROUPA;
                        switch ($subFamilia) {
                            case 'MAQUINAS LAVAR ROUPA CARGA SUPERIOR':
                                $subFamilia = Cat::MAQ_LAVAR_ROUPA_CARGA_SUPERIOR;
                                return ([$gama, $familia, $subFamilia]);
                            default:
                                $subFamilia = Cat::MAQ_LAVAR_ROUPA_CARGA_FRONTAL;
                                return ([$gama, $familia, $subFamilia]);
                        }
                    case 'MAQUINAS LAVAR SECAR ROUPA':
                        $familia = Cat::MAQ_ROUPA;
                        $subFamilia = Cat::MAQ_LAVAR_SECAR_ROUPA;
                        return ([$gama, $familia, $subFamilia]);
                    case 'SECADORES ROUPA BOMBA CALOR':
                        $familia = Cat::MAQ_ROUPA;
                        $subFamilia = Cat::MAQ_SECAR_ROUPA_BC;
                        return ([$gama, $familia, $subFamilia]);
                    case 'SECADORES ROUPA CONDENSAÇAO':
                        $familia = Cat::MAQ_ROUPA;
                        $subFamilia = Cat::MAQ_SECAR_ROUPA_COND;
                        return ([$gama, $familia, $subFamilia]);
                    case 'SECADORES ROUPA VENTILAÇAO':
                        $familia = Cat::MAQ_ROUPA;
                        $subFamilia = Cat::MAQ_SECAR_ROUPA_VENT;
                        return ([$gama, $familia, $subFamilia]);
                    default:
                        return ([$gama, $familia, $subFamilia]);
                }

            case 'ENCASTRE':
                $gama = Cat::ENCASTRE;

                switch ($familia) {
                    case 'CHAMINES':
                    case 'EXAUSTORES':
                        $familia = Cat::EXAUSTORES;
                        $subFamilia = null;
                        return ([$gama, $familia, $subFamilia]);

                    case 'COMBINADOS ENCASTRE':
                        $familia = Cat::FRIO_ENC;
                        $subFamilia = Cat::COMBINADOS_ENC;
                        return ([$gama, $familia, $subFamilia]);

                    case 'CONGELADORES VERTICAIS ENCASTRE':
                        $familia = Cat::FRIO_ENC;
                        $subFamilia = Cat::CONGELADORES_ENC;
                        return ([$gama, $familia, $subFamilia]);

                    case 'MAQUINAS DE CAFE ENCASTRE':
                        $subFamilia = Cat::MAQ_CAFE_ENC;
                        return ([$gama, $familia, $subFamilia]);

                    case 'FRIGORIFICOS 1 PORTA ENCASTRE':
                    case 'FRIGORIFICOS 2 PORTAS ENCASTRE':
                        $familia = Cat::FRIO_ENC;
                        $subFamilia = Cat::FRIGORIFICOS_ENC;
                        return ([$gama, $familia, $subFamilia]);
                    case 'FORNOS':
                        $familia = Cat::FORNOS;
                        $subFamilia = null;
                        return ([$gama, $familia, $subFamilia]);
                    case 'MAQUINAS LAVAR LOUÇA ENCASTRE':
                        $familia = Cat::MAQ_DE_LOUCA_ENC;
                        $subFamilia = null;
                        return ([$gama, $familia, $subFamilia]);

                    case 'MAQUINAS LAVAR ROUPA ENCASTRE':
                        $familia = Cat::MAQ_ROUPA_ENC;
                        $subFamilia = Cat::MAQ_LAVAR_ROUPA_ENC;
                        return ([$gama, $familia, $subFamilia]);

                    case 'MAQUINAS LAVAR SECAR ENCASTRE':
                        $familia = Cat::MAQ_LAVAR_SECAR_ROUPA_ENC;
                        $subFamilia = Cat::MAQ_LAVAR_SECAR_ROUPA_ENC;
                        return ([$gama, $familia, $subFamilia]);
                    case 'MICRO ONDAS ENCASTRE':
                        $familia = Cat::MICROONDAS_ENC;
                        $subFamilia = null;
                        return ([$gama, $familia, $subFamilia]);

                    case 'PLACAS A GAS':
                    case 'PLACAS CRISTAL GAS':
                    case 'PLACAS DOMINO':
                    case 'PLACAS DOMINO ELECTRICAS':
                    case 'PLACAS MISTAS':
                    case 'PLACAS VITROCERAMICAS':
                    case 'PLACAS INDUÇAO':
                        $familia = Cat::PLACAS;
                        $subFamilia = null;
                        return ([$gama, $familia, $subFamilia]);
                    case 'TAMPOS':
                        $subFamilia = Cat::ACESSORIOS_ENC;
                        return ([$gama, $familia, $subFamilia]);

                    default:
                        return ([$gama, $familia, $subFamilia]);
                }

            case 'FRIO':
                $gama = Cat::GRANDES_DOMESTICOS;
                switch ($familia) {                    
                    case 'COMBINADOS':
                        $familia = Cat::FRIO;
                        $result = preg_match("/NF/", $subFamilia);
                        if ($result == 1) {
                            $subFamilia = Cat::COMBINADOS_NO_FROST;
                        } elseif ($result == 0) {
                            $subFamilia = Cat::COMBINADOS_CONVENCIONAIS;
                        }
                        return ([$gama, Cat::FRIGORIFICOS_COMBINADOS, $subFamilia]);
                    case 'FRIGORIFICOS 1 PORTA':
                        $familia = Cat::FRIO;
                        $subFamilia = Cat::FRIGORIF_1_PORTA;
                        return ([$gama, $familia, $subFamilia]);

                    case 'FRIGORIFICOS 2 PORTAS':
                        $familia = Cat::FRIO;
                        $result = preg_match("/NF/", $subFamilia);
                        if ($result == 1) {
                            $subFamilia = Cat::FRIGORIF_2P_NO_FROST;
                        } elseif ($result == 0) {
                            $subFamilia = Cat::FRIGORIF_2_PORTAS;
                        }
                        return ([$gama, $familia, $subFamilia]);

                    case 'FRIGORIFICOS SIDE BY SIDE':
                        $familia = Cat::FRIO;
                        $subFamilia = Cat::FRIGORIF_AMERICANOS;
                        return ([$gama, $familia, $subFamilia]);
                    case 'CONGELADORES HORIZONTAIS':
                        $familia = Cat::FRIO;
                        $subFamilia = Cat::CONGELADORES_HORIZONTAIS;
                        return ([$gama, $familia, $subFamilia]);
                    case 'CONGELADORES VERTICAIS':
                        $familia = Cat::FRIO;
                        $subFamilia = Cat::CONGELADORES_VERTICAIS;
                        return ([$gama, $familia, $subFamilia]);
                    case 'FRIGORIFICOS MINI-BAR':
                        $familia = Cat::FRIO;
                        $subFamilia = Cat::FRIGOBAR;
                        return ([$gama, $familia, $subFamilia]);
                    default:
                        return ([$gama, $familia, $subFamilia]);
                }

            case 'AGUAS QUENTES':
                $gama = Cat::GRANDES_DOMESTICOS;
                switch ($familia) {
                    case 'TERMOACUMULADORES':
                        $familia = Cat::TERMOACUMULADORES;
                        switch ($subFamilia) {
                            case 'TERMOACUMULADORES':
                            case 'TERMOACUMULADORES HORIZONTAIS':
                                $subFamilia = Cat::TERMOACUMULADORES_ELECTRICOS;
                                return ([$gama, $familia, $subFamilia]);
                            default:
                                return ([$gama, $familia, $subFamilia]);
                        }

                    case 'ESQUENTADORES':
                        $familia = Cat::ESQUENTADORES_CALDEIRAS;
                        switch ($subFamilia) {
                            case 'ESQUENTADORES ELETRICOS':
                                $subFamilia = Cat::ESQUENTADORES_ELECTRICOS;
                                return ([$gama, $familia, $subFamilia]);
                            case  'ESQUENTADORES ESTANQUES':
                            case 'ESQUENTADORES IGNIÇAO MANUAL':
                            case 'ESQUENTADORES INTELIGENTES':
                            case 'ESQUENTADORES VENTILADOS':
                                $subFamilia = Cat::ESQUENTADORES_C_GAS;
                                return ([$gama, $familia, $subFamilia]);
                            default:
                                print_r("Esquentador not found category.php 556");
                                return ([$gama, $familia, $subFamilia]);
                        }
                    default:
                        return ([$gama, $familia, $subFamilia]);
                }

            case 'MAQUINAS LOUÇA':
                $gama = Cat::GRANDES_DOMESTICOS;
                $familia = Cat::MAQ_DE_LOUCA;
                switch ($subFamilia) {
                    case 'MAQUINAS LAVAR LOUÇA 45CM':
                        $subFamilia = Cat::MLL_DE_45;
                        return ([$gama, $familia, $subFamilia]);
                    case 'MAQUINAS LAVAR LOUÇA BRANCAS':
                    case 'MAQUINAS LAVAR LOUÇA OUTRAS CORES':
                    case 'MAQUINAS LAVAR LOUÇA INOX':
                        $subFamilia = Cat::MLL_DE_60;
                        return ([$gama, $familia, $subFamilia]);
                    case 'MAQUINAS LAVAR LOUÇA COMPACTAS':
                        $subFamilia = Cat::MLL_COMPACTAS;
                        return ([$gama, $familia, $subFamilia]);
                    default:
                        return ([$gama, $familia, $subFamilia]);
                }

            case 'SOM & IMAGEM':
                $gama = Cat::IMAGEM_E_SOM;
                switch ($familia) {
                    case 'LED´S':
                        $familia = Cat::TELEVISAO;
                        $subFamilia = null;
                        $logger->info(Cat::VERIFICAR_CATEGORIAS);
                        return ([$gama, $familia, $subFamilia]);
                    case 'SOM & VIDEO':
                        switch ($subFamilia) {
                            case 'BARRA DE SOM':
                                $familia = Cat::EQUIPAMENTOS_AUDIO;
                                $subFamilia = Cat::BARRAS_SOM;
                                return ([$gama, $familia, $subFamilia]);
                            case 'HI-FI':
                                $familia = Cat::EQUIPAMENTOS_AUDIO;
                                $subFamilia = Cat::APARELHAGENS_MICROS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'LEITOR BLU RAY':
                                $familia = Cat::DVD_BLURAY_TDT;
                                $subFamilia = null;
                                return ([$gama, $familia, $subFamilia]);
                            default:
                                return ([$gama, $familia, $subFamilia]);
                        }
                    case 'SUPORTES':
                        $familia = Cat::ACESSORIOS_IMAGEM_E_SOM;
                        $subFamilia = Cat::MOVEIS_SUPORTES;
                        return ([$gama, $familia, $subFamilia]);
                    default:
                        $familia = null;
                        $subFamilia = null;
                        return ([$gama, $familia, $subFamilia]);
                }

            case 'PEQUENOS DOMESTICOS':
                $gama = Cat::PEQUENOS_DOMESTICOS;
                switch ($familia) {
                    case 'BELEZA & HIGIENE':
                        $familia = Cat::ASSEIO_PESSOAL;
                        switch ($subFamilia) {
                            case 'APARADORES DE BARBA E BIGODE':
                            case 'APARADORES DE CABELO':
                                $subFamilia = Cat::APARADORES;
                                return ([$gama, $familia, $subFamilia]);
                            case 'DEPILADORAS':
                                $subFamilia = Cat::DEPILADORAS;
                                return ([$gama, $familia, $subFamilia]);
                            default:
                                return ([$gama, $familia, $subFamilia]);
                        }
                    case 'CASA':
                        $familia = Cat::APARELHOS_DE_LIMPEZA;
                        switch ($subFamilia) {
                            case 'ASPIRADORES MISTOS':
                            case 'ASPIRADORES COM SACO':
                                $subFamilia = Cat::ASPIRADOR_COM_SACO;
                                return ([$gama, $familia, $subFamilia]);
                            case 'ASPIRADORES MINI':
                                $subFamilia = Cat::MINI_ASPIRADORES;
                                return ([$gama, $familia, $subFamilia]);
                            case 'ASPIRADORES ROBOT':
                                $subFamilia = Cat::ASPIRADORES_ROBOT;
                                return ([$gama, $familia, $subFamilia]);
                            case 'ASPIRADORES SEM SACO':
                                $subFamilia = Cat::ASPIRADOR_SEM_SACO;
                                return ([$gama, $familia, $subFamilia]);
                            case 'ASPIRADORES VERTICAIS':
                                $subFamilia = Cat::ASPIRADOR_VERTICAL;
                                return ([$gama, $familia, $subFamilia]);
                            case 'COFRES':
                                $familia = Cat::COFRES;
                                $subFamilia = null;
                                return ([$gama, $familia, $subFamilia]);
                            case 'SACOS PARA ASPIRADOR':
                                $subFamilia = Cat::SACOS_ASPIRADOR;
                                return ([$gama, $familia, $subFamilia]);
                            default:
                                $subFamilia = null;
                                return ([$gama, $familia, $subFamilia]);
                        }

                    case 'COZINHA':
                        $familia = Cat::APARELHOS_DE_COZINHA;
                        switch ($subFamilia) {
                            case 'ARTIGOS MANUAIS':
                                $familia = Cat::ARTIGOS_DE_MENAGE;
                                $subFamilia = Cat::PEQ_APARELHOS_COZINHA;
                                return ([$gama, $familia, $subFamilia]);
                            case 'BALANÇAS COZINHA':
                                $subFamilia = Cat::BALANÇAS_DE_COZINHA;
                                return ([$gama, $familia, $subFamilia]);
                            case 'BATEDEIRAS':
                                $subFamilia = Cat::BATEDEIRAS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'CAÇAROLAS':
                                $familia = Cat::ARTIGOS_DE_MENAGE;
                                $subFamilia = Cat::CACAROLAS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'CAIXA HERMETICA PLASTICO':
                            case 'CAIXA HERMETICA VIDRO':
                                $familia = Cat::ARTIGOS_DE_MENAGE;
                                $subFamilia = Cat::CAIXA_HERMETICA;
                                return ([$gama, $familia, $subFamilia]);
                            case 'CENTRIFUGADORAS':
                                $subFamilia = Cat::CENTRIFUGADORAS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'ESPREMEDORES DE CITRINOS':
                                $subFamilia = Cat::ESPREMEDORES;
                                return ([$gama, $familia, $subFamilia]);
                            case 'FACAS ELECTRICAS':
                                $subFamilia = Cat::ABRE_LATAS_FACAS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'FIAMBREIRAS':
                                $subFamilia = Cat::FIAMBREIRAS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'FORNOS ELETRICOS DE BANCADA':
                                $subFamilia = Cat::FORNOS_DE_BANCADA;
                                return ([$gama, $familia, $subFamilia]);
                            case 'FRIGIDEIRAS':
                                $familia = Cat::ARTIGOS_DE_MENAGE;
                                $subFamilia = Cat::FRIGIDEIRAS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'FRITADEIRAS':
                                $subFamilia = Cat::FRITADEIRAS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'GRELHADORES':
                            case 'GRELHADORES DE PLACAS':
                                $subFamilia = Cat::GRELHADORES;
                                return ([$gama, $familia, $subFamilia]);
                            case 'LIQUIDIFICADORES':
                                $subFamilia = Cat::LIQUIDIFICADORAS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'PICADORAS':
                            case 'QUEIMADORES LEITE CREME':
                            case 'MAQUINAS DE MOER CARNE':
                            case 'SELADORES DE SACOS':
                            case 'COZEDURA A VAPOR':
                                $subFamilia = Cat::MAQ_DE_COZINHA;
                                return ([$gama, $familia, $subFamilia]);
                            case 'PANELAS DE PRESSAO':
                                $familia = Cat::ARTIGOS_DE_MENAGE;
                                $subFamilia = Cat::PANELAS_DE_PRESSAO;
                                return ([$gama, $familia, $subFamilia]);
                            case 'ROBOTS DE COZINHA':
                                $subFamilia = Cat::ROBOTS_DE_COZINHA;
                                return ([$gama, $familia, $subFamilia]);
                            case 'TACHOS':
                                $familia = Cat::ARTIGOS_DE_MENAGE;
                                $subFamilia = Cat::TACHOS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'TREM DE COZINHA':
                                $familia = Cat::ARTIGOS_DE_MENAGE;
                                $subFamilia = Cat::TRENS_COZINHA;
                                return ([$gama, $familia, $subFamilia]);
                            case 'VARINHAS':
                                $subFamilia = Cat::VARINHAS_MAGICAS;
                                return ([$gama, $familia, $subFamilia]);
                            default:
                                print_r("subfamilia not found".$subFamilia);
                                return([$gama,$familia,'']);
                        }

                    case 'CUIDADOS PESSOAIS':
                        $familia = Cat::ASSEIO_PESSOAL;
                        switch ($subFamilia) {
                            case 'BALANÇAS WC':
                                $subFamilia = Cat::BALANÇAS_DE_WC;
                                return ([$gama, $familia, $subFamilia]);
                            case 'MODELADORES DE CABELO':
                                $subFamilia = Cat::MODELADORES;
                                return ([$gama, $familia, $subFamilia]);
                            case 'SECADORES DE CABELO':
                                $subFamilia = Cat::SECADORES_DE_CABELO;
                                return ([$gama, $familia, $subFamilia]);
                        }

                    case 'MICRO ONDAS':
                        $gama = Cat::GRANDES_DOMESTICOS;
                        $familia = Cat::MICROONDAS;
                        switch ($subFamilia) {
                            case 'MICRO ONDAS C/GRILL 21 A 29L':
                            case 'MICRO ONDAS C/GRILL = > 30L':
                            case 'MICRO ONDAS C/GRILL ATÉ 20L':
                            case 'MICRO ONDAS C/GRILL ATÉ 20L':
                                $subFamilia = Cat::MO_COM_GRILL;
                                return ([$gama, $familia, $subFamilia]);
                            case 'MICRO ONDAS S/GRILL 21 A 29L':
                            case 'MICRO ONDAS S/GRILL ATÉ 20L':
                                $subFamilia = Cat::MO_SEM_GRILL;
                                return ([$gama, $familia, $subFamilia]);
                            default:
                                print_r("subFamilia not found".$subFamilia);
                                return([$gama,$familia,'']);
                        }

                    case 'PEQUENO-ALMOÇO':
                        $familia = Cat::APARELHOS_DE_COZINHA;
                        switch ($subFamilia) {
                            case 'CAFETEIRAS':
                                $subFamilia = Cat::CAFETEIRAS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'JARROS ELECTRICOS':
                                $subFamilia = Cat::JARROS_E_FERV_PURIF_ÁGUA;
                                return ([$gama, $familia, $subFamilia]);
                            case 'JARROS TERMICOS':
                                $familia = Cat::ARTIGOS_DE_MENAGE;
                                $subFamilia = Cat::PEQ_APARELHOS_COZINHA;
                                return ([$gama, $familia, $subFamilia]);

                            case 'MAQUINAS DE CAFE':
                                $subFamilia = Cat::MAQ_CAFE;
                                return ([$gama, $familia, $subFamilia]);
                            case 'MOINHOS DE CAFE':
                                $subFamilia = Cat::MOINHOS_DE_CAFE;
                                return ([$gama, $familia, $subFamilia]);
                            case 'SANDWICHEIRAS':
                                $subFamilia = Cat::SANDWICHEIRAS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'TERMOS':
                                $familia = Cat::ARTIGOS_DE_MENAGE;
                                $subFamilia = Cat::TERMOS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'TORRADEIRAS':
                                $subFamilia = Cat::TORRADEIRAS;
                                return ([$gama, $familia, $subFamilia]);
                        }

                    case 'ROUPA':
                        $familia = Cat::CUIDADO_DE_ROUPA;
                        switch ($subFamilia) {
                            case 'FERROS COM CALDEIRA':
                                $subFamilia = Cat::FERROS_CALDEIRA;
                                return ([$gama, $familia, $subFamilia]);

                            case 'FERROS DE ENGOMAR A SECO';
                                $subFamilia = Cat::FERROS_A_SECO;
                                return ([$gama, $familia, $subFamilia]);
                            case 'FERROS DE ENGOMAR A VAPOR':
                                $subFamilia = Cat::FERROS_A_VAPOR;
                                return ([$gama, $familia, $subFamilia]);
                            case 'FERROS DE ENGOMAR DE VIAGEM':
                                $subFamilia = Cat::FERRO_VIAGEM;
                                return ([$gama, $familia, $subFamilia]);
                            case 'TABUAS DE ENGOMAR':
                                $subFamilia = Cat::TABUAS_PASSAR_FERRO;
                                return ([$gama, $familia, $subFamilia]);

                        }


                }
                return 'PEQUENOS DOMÉSTICOS';

            case 'INDUSTRIAL':
                $gama = Cat::INDUSTRIAL;
                $familia = Cat::FRIO_INDUSTRIAL;
                switch ($subFamilia) {
                    case 'ARREFECEDORES HORIZONTAIS':
                        $subFamilia = Cat::ARREFECEDORES_HORIZONTAIS_INDUSTRIAIS;
                        return ([$gama, $familia, $subFamilia]);
                    case 'CONGELADORES HORIZONTAIS INDUSTRIAIS':
                        $subFamilia = Cat::CONGELADORES_HORIZONTAIS_INDUSTRIAIS;
                        return ([$gama, $familia, $subFamilia]);
                    case 'CONGELADORES ILHA':
                        $subFamilia = Cat::CONGELADORES_ILHA_INDUSTRIAIS;
                        return ([$gama, $familia, $subFamilia]);
                    case 'VITRINES CONGELADORAS':
                        $subFamilia = Cat::CONGELADORES_VERTICAIS_INDUSTRIAIS;
                        return ([$gama, $familia, $subFamilia]);
                    case 'VITRINES ARREFECEDORAS':
                    case 'VITRINES ARREFECEDORAS ENCASTRE':
                        $subFamilia = Cat::ARREFECEDORES_VERTICAIS_INDUSTRIAIS;
                        return ([$gama, $familia, $subFamilia]);
                    case 'ELECTROCUTORES DE INSETOS':
                        $familia = Cat::ELECTROCUTORES_INSECTOS;
                        $subFamilia = null;
                        return ([$gama, $familia, $subFamilia]);
                    case 'FOGOES GAMA INDUSTRIAL':
                    case 'TREMPES':
                        $familia = Cat::FOGOES_INDUSTRIAIS;
                        return ([$gama, $familia, $subFamilia]);
                    case 'VARINHAS GAMA HOTELEIRA':
                        $familia = Cat::EQUIPAMENTOS_COZINHA_INDUSTRIAIS;
                        $subFamilia = Cat::VARINHAS_INDUSTRIAIS;
                        return ([$gama, $familia, $subFamilia]);
                    default:
                        return ([$gama, $familia, $subFamilia]);
                }

            case 'CLIMATIZAÇAO':
                $gama = Cat::CLIMATIZACAO;
                switch ($familia) {
                    case 'AR CONDICIONADO':
                        $familia = Cat::AR_CONDICIONADO;
                        switch ($subFamilia) {
                            case 'AR CONDICIONADO UNIDADES EXTERIORES':
                            case 'AR CONDICIONADO UNIDADES INTERIORES':
                            case 'AR CONDICIONADO INVERTER':
                                $subFamilia = Cat::AC_FIXO;
                                return ([$gama, $familia, $subFamilia]);
                            case 'AR CONDICIONADO PORTATIL':
                                $subFamilia = Cat::AC_PORTATIL;
                                return ([$gama, $familia, $subFamilia]);
                            default:
                                return ([$gama, $familia, $subFamilia]);
                        }
                    case 'AMBIENTE - PORTATIL, PELLETS E LENHA':
                        $familia = Cat::AQUECIMENTO;
                        switch ($subFamilia) {
                            case 'AQUECEDORES WC PAREDE':
                                $subFamilia = Cat::AQUECEDORES_WC_PAREDE;
                                return ([$gama, $familia, $subFamilia]);
                            case 'ESCALFETAS':
                            case 'BRASEIRAS':
                            case 'AQUECEDORES SILICAS':
                                $subFamilia = Cat::ELECTRICO;
                                return ([$gama, $familia, $subFamilia]);

                            case 'VENTOINHAS MESA':
                            case 'VENTOINHAS PE':
                            case 'COLUNAS DE AR':
                                $familia = Cat::VENTILACAO;
                                $subFamilia = Cat::VENTOINHAS;
                                return ([$gama, $familia, $subFamilia]);

                            case 'TERMOVENTILADORES':
                            case 'CONVECTORES':
                                $subFamilia = Cat::CONVECTORES_TERMOVENT;
                                return ([$gama, $familia, $subFamilia]);
                            case 'EMISSORES TERMICOS':
                            case 'IRRADIADORES DE MICA':
                                $subFamilia = Cat::EMISSORES_TERMICOS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'IRRADIADORES A OLEO':
                                $subFamilia = Cat::RADIADORES_A_OLEO;
                                return ([$gama, $familia, $subFamilia]);
                            case 'TOALHEIRO':
                                $subFamilia = Cat::TOALHEIROS;
                                return ([$gama, $familia, $subFamilia]);

                            case 'DESUMIDIFICADORES':
                                $familia = Cat::TRATAMENTO_DE_AR;
                                $subFamilia = Cat::DESUMIDIFICADORES;
                                return ([$gama, $familia, $subFamilia]);

                            case 'FOGOES LENHA LINHA ESMALTADA/INOX':
                            case 'FOGOES LENHA TRADICIONAIS':
                                $subFamilia = Cat::FOGOES_LENHA;
                                return ([$gama, $familia, $subFamilia]);
                            case 'SALAMANDRA A LENHA FUNDIÇÃO':
                            case 'SALAMANDRAS LENHA C/ VENTILAÇAO':
                            case 'SALAMANDRAS LENHA REDONDAS':
                            case 'SALAMANDRAS LENHA S/ VENTILAÇAO':
                            case 'SALAMANDRAS PELLETS AR QUENTE':
                                $subFamilia = Cat::SALAMANDRAS;
                                return ([$gama, $familia, $subFamilia]);
                            case 'RECUPERADORES AR QUENTE':
                                $subFamilia = Cat::RECUPERADORES;
                                return ([$gama, $familia, $subFamilia]);
                            default:
                                $subFamilia = null;
                                return ([$gama, $familia, $subFamilia]);

                        }
                    default:
                        $familia = null;
                        $subFamilia = null;
                        return ([$gama,$familia,$subFamilia]);
                }

            default:
                return [$gama,$familia,$subFamilia];
        }
    }
}