<?php 

namespace Mlp\Cli\Helper\Expert;

use Mlp\Cli\Helper\CategoriesConstants as Cat;

class JpdiCategories  {

    public static function getJpdiCategories($gama,$familia,$subFamilia,$logger){
        switch ($gama){
            case 'Periféricos':
                $gama = Cat::INFORMATICA;
                switch ($familia) {
                    case 'Acessorios':
                        $familia = Cat::ACESSORIOS_INFORMATICA;
                        switch ($subFamilia) {
                            case 'Sistemas de POS':
                                $subFamilia = Cat::ACESSORIOS_POS;
                                return [$gama,$familia,$subFamilia];
                            case 'Projectores':
                                $subFamilia = Cat::ACESSORIOS_PROJECTORES;
                                return [$gama,$familia,$subFamilia];
                            case 'Suportes':
                                $gama = Cat::IMAGEM_E_SOM;
                                $familia = Cat::ACESSORIOS_IMAGEM_E_SOM;
                                $subFamilia = Cat::MOVEIS_SUPORTES;
                                return [$gama,$familia,$subFamilia];
                            case 'Portatil':
                                $subFamilia = Cat::ACESSORIOS_NOTEBOOKS;
                                return [$gama,$familia,$subFamilia];
                            case 'Impressoras':
                                $subFamilia = Cat::ACESSORIOS_IMPRESSORAS;
                                return [$gama,$familia,$subFamilia];
                            case 'Outros':
                                $subFamilia = Cat::OUTROS_ACESSORIOS_INFORMATICA;
                                return [$gama,$familia,$subFamilia];
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                    case 'HUBs USB e Leitores de Cartões':
                        $familia = Cat::ACESSORIOS_INFORMATICA;
                        $subFamilia = Cat::HUBS_USB_LEITORES_CARTOES;
                        return [$gama,$familia,$subFamilia];
                    case 'Armazenamento':
                        $familia = Cat::MEMORIAS;
                        switch ($subFamilia) {
                            case 'Cartões':
                                $subFamilia = Cat::CARTOES_MEMORIA;
                                return [$gama,$familia,$subFamilia];
                            case 'USB':
                                $subFamilia = Cat::PENS_USB;
                                return [$gama,$familia,$subFamilia];
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                    case 'Audio-Multimedia e Comunicação':
                        switch ($subFamilia) {
                            case 'Colunas':
                                $gama = Cat::IMAGEM_E_SOM;
                                $familia = Cat::COLUNAS;
                                $subFamilia = null;
                                return [$gama,$familia,$subFamilia];
                            case 'Microfones':
                                $familia = Cat::ACESSORIOS_INFORMATICA;
                                $subFamilia = Cat::MICROFONES;
                                return [$gama,$familia,$subFamilia];
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                    case 'Teclados e Ratos':
                        $familia = Cat::ACESSORIOS_INFORMATICA;
                        switch ($subFamilia) {
                            case 'Tapetes de rato':
                                $subFamilia = Cat::TAPETES_RATOS;
                                return [$gama,$familia,$subFamilia];
                            case 'Keyboard':
                            case 'Teclados':
                               $subFamilia = Cat::TECLADOS;
                               return [$gama,$familia,$subFamilia];
                            case 'Apresentadores':
                                $subFamilia = Cat::APRESENTADORES;
                                return [$gama,$familia,$subFamilia];
                            
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                    case 'Power Systems':
                        $gama = Cat::ELECTRICIDADE;
                        switch ($subFamilia) {
                            case 'Baterias':
                                $familia = Cat::PILHAS_BATERIAS;
                                $subFamilia = Cat::BATERIAS;
                                return [$gama,$familia,$subFamilia];
                            case 'UPS':
                                $familia = Cat::PILHAS_BATERIAS;
                                $subFamilia = Cat::UPS;
                                return [$gama,$familia,$subFamilia];
                            case 'Tomadas':
                                $familia = Cat::EXTENSOES_CABOS;
                                $subFamilia = Cat::EXTENSOES_MULTIPLAS;
                                return [$gama,$familia,$subFamilia];
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                    case 'Monitores':
                        $familia = Cat::MONITORES;
                        switch ($subFamilia) {
                            case 'Touch':
                                $subFamilia = Cat::MONITORES_TACTEIS;
                                return [$gama,$familia,$subFamilia];
                            case 'Hospitality TV':
                                $subFamilia = Cat::HOSPITALITY_TV;
                                return [$gama,$familia,$subFamilia];
                            default:
                                $subFamilia = Cat::MONITORES_PC;
                                return [$gama,$familia,$subFamilia];
                        }
                        
                        
                    case 'Scanners':
                        $familia = Cat::IMPRESSORAS;
                        $subFamilia = Cat::SCANNERS;
                        return [$gama,$familia,$subFamilia];
                    case 'Impressoras':
                        $familia = Cat::IMPRESSORAS;
                        switch ($subFamilia) {
                            case 'Grande Formato (LFP)':
                                $subFamilia = Cat::IMPRESSORAS_GRANDE_FORMATO;
                                return [$gama,$familia,$subFamilia];
                            case 'Térmicas':
                                $subFamilia = Cat::IMPRESSORAS_TERMICAS;
                                return [$gama,$familia,$subFamilia];
                            default:
                                $subFamilia = Cat::IMPRESSORAS_SIMPLES;
                                return [$gama,$familia,$subFamilia];
                        }
                        
                    case 'Multifunções':
                        switch ($subFamilia) {
                            case 'LED':
                            case 'Laser Cores':
                            case 'Laser Mono':
                            case 'Jacto Tinta A4':
                                $familia = Cat::IMPRESSORAS;
                                $subFamilia = Cat::IMPRESSORAS_MULTI_FUNC;
                                return [$gama,$familia,$subFamilia];
                            
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                        return [$gama,$familia,$subFamilia];
                    case 'Cabos e Adaptadores':
                        $familia = Cat::ACESSORIOS_INFORMATICA;
                        $subFamilia = Cat::REDES_CABOS;
                        return [$gama,$familia,$subFamilia];
                    case 'Projectores':
                        $gama = Cat::IMAGEM_E_SOM;
                        $familia = Cat::PROJECTORES;
                        switch ($subFamilia) {
                            case 'Curto Alcance':
                            case 'Instalação':
                            case 'Home Cinema':
                                $subFamilia = Cat::PROJECTORES_MESA;    
                                return [$gama,$familia,$subFamilia];
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }

                        $subFamilia = Cat::PROJECTORES_PORTATEIS;
                        return [$gama,$familia,$subFamilia];
                    case 'Audio-Multimedia e Comunicação':
                        switch ($subFamilia) {
                            case 'Auscultadores':
                                $gama = Cat::IMAGEM_E_SOM;
                                $familia = Cat::AUSCULTADORES;
                                $subFamilia = null;
                                return [$gama,$familia,$subFamilia];
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                    default:
                        print_r($familia.Cat::NOT_FUND);
                        $logger->info($familia.Cat::NOT_FUND);
                        return 0;
                }
            case 'Supplies':
                $gama = Cat::INFORMATICA;
                switch ($familia) {
                    case 'Laser':
                    case 'Jacto de Tinta':
                        $familia = Cat::IMPRESSORAS;
                        $subFamilia = Cat::CONSUMIVEIS_IMPRESSORAS;
                        return [$gama,$familia,$subFamilia];
                    
                    default:
                        print_r($familia.Cat::NOT_FUND);
                        $logger->info($familia.Cat::NOT_FUND);
                        return 0;
                        
                }
            case 'Networking':
                $gama = Cat::INFORMATICA;
                switch ($familia) {
                    case 'Switch':
                    case 'WLAN':
                    case 'Routers':
                    case 'Adaptors':
                    case 'Powerlines':
                    case 'Acessorios':
                        $familia = Cat::ACESSORIOS_INFORMATICA;
                        $subFamilia = Cat::REDES_CABOS;
                        return [$gama,$familia,$subFamilia];
                    
                    case 'Videovigilância':
                        $gama = Cat::SEGURANCA;
                        $familia = Cat::VIDEOVIGILANCIA;
                        $subFamilia = Cat::CAMARAS_IP;
                        return [$gama,$familia,$subFamilia];
                    default:
                        print_r($familia.Cat::NOT_FUND);
                        $logger->info($familia.Cat::NOT_FUND);
                        return 0;
                }
            case 'Mobilidade':
                $gama = Cat::INFORMATICA;
                switch ($familia) {
                    case 'Telefones':
                        $gama = Cat::COMUNICACOES;
                        switch ($subFamilia) {
                            case 'Telemóveis':
                                $familia = Cat::TELEMOVEIS_CARTOES;
                                $subFamilia = Cat::TELEMOVEIS;
                                return [$gama,$familia,$subFamilia];
                            case 'Fixos':
                                $familia = Cat::COMUNICACOES_FIXAS;
                                $subFamilia = Cat::TELEFONES_FIXOS;
                                return [$gama,$familia,$subFamilia];

                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                    case 'Acessorios':
                        switch ($subFamilia) {
                            case 'Tablet':
                                $familia = Cat::COMPUTADORES_E_TABLETS;
                                $subFamilia = Cat::ACESSORIOS_TABLETS;
                                return [$gama,$familia,$subFamilia];
                            case 'Smartphone':
                                $gama = Cat::COMUNICACOES;
                                $familia = Cat::ACESSORIOS_COMUNICACOES;
                                $subFamilia = Cat::ACESSORIOS_SMARTPHONES;
                                return [$gama,$familia,$subFamilia];
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                    case 'Wearables':
                        switch ($subFamilia) {
                            case 'SMARTWATCH':
                            case 'Smart Band':
                                $gama = Cat::COMUNICACOES;
                                $familia = Cat::SMARTWATCHES;
                                return [$gama,$familia,$subFamilia];
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                    default:
                        print_r($familia.Cat::NOT_FUND);
                        $logger->info($familia.Cat::NOT_FUND);
                        return 0;
                }
            case 'System Components':
                switch ($familia) {
                    case 'Discos':
                        $familia = Cat::MEMORIAS;
                        switch ($subFamilia) {
                            case 'Internos':
                                $subFamilia = Cat::DISCOS_HDD;
                                return [$gama,$familia,$subFamilia];
                            case 'SSD':
                                $subFamilia = Cat::DISCOS_SSD;
                                return [$gama,$familia,$subFamilia];
                            case 'Externo':
                                $subFamilia = Cat::DISCOS_EXTERNOS;
                                return [$gama,$familia,$subFamilia];
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                    
                    default:
                        print_r($familia.Cat::NOT_FUND);
                        $logger->info($familia.Cat::NOT_FUND);
                        return 0;
                }

            case 'Computer Systems':
                $gama = Cat::INFORMATICA;
                switch ($familia) {
                    case 'Desktops':
                        $familia = Cat::COMPUTADORES_E_TABLETS;
                        $subFamilia = Cat::DESKTOPS;
                        return [$gama,$familia,$subFamilia];
                    
                        
                    case 'Portáteis':
                        $familia = Cat::COMPUTADORES_E_TABLETS;
                        $subFamilia = Cat::PORTATEIS_NOTEBOOKS;
                        return [$gama,$familia,$subFamilia];
                    default:
                        print_r($subFamilia.Cat::NOT_FUND);
                        $logger->info($subFamilia.Cat::NOT_FUND);
                        return 0;
                }
            case 'Consumer Electronics':
                switch ($familia) {
                    case 'Camaras de Video':
                        $gama = Cat::IMAGEM_E_SOM;
                        $familia = Cat::CAMARAS_VIDEO;
                        $subFamilia = null;
                        return [$gama,$familia,$subFamilia];
                    case 'Consolas':
                        $familia = Cat::GAMING;
                        switch ($subFamilia) {
                            case 'Comandos de Jogos':
                                $subFamilia = Cat::ACESSORIOS_PC_GAMING;
                                return [$gama,$familia,$subFamilia];
                            
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                    case 'Audio - Video':
                        $gama = Cat::IMAGEM_E_SOM;
                        switch ($subFamilia) {
                            case 'Radios':
                                $familia = Cat::AUDIO_PORTATIL;
                                $subFamilia = Cat::RADIOS_PORTATEIS;
                                return [$gama,$familia,$subFamilia];
                            
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                    
                    default:
                        print_r($familia.Cat::NOT_FUND);
                        $logger->info($familia.Cat::NOT_FUND);
                        return 0;
                }
            case 'SMARTHOME':
                switch ($familia) {
                    case 'Acessorios':
                        $gama = Cat::ELECTRICIDADE;
                        $familia = Cat::SMART_HOME;
                        switch ($subFamilia) {
                            case 'Interruptores':        
                                $subFamilia = Cat::INTERRUPTORES_SMART;
                                return [$gama,$familia,$subFamilia];
                            case 'Campainhas':
                                $subFamilia = Cat::CAMPAINHAS_SMART;
                                return [$gama,$familia,$subFamilia];
                            case 'ASPIRADORES':
                                $gama = Cat::PEQUENOS_DOMESTICOS;
                                $familia = Cat::ASPIRADORES_ROBOT;
                                $subFamilia = null;
                                return [$gama,$familia,$subFamilia];
                            default:
                                print_r($subFamilia.Cat::NOT_FUND);
                                $logger->info($subFamilia.Cat::NOT_FUND);
                                return 0;
                        }
                    
                    default:
                        print_r($familia.Cat::NOT_FUND);
                        $logger->info($familia.Cat::NOT_FUND);
                        return 0;
                }
        }

    }
}