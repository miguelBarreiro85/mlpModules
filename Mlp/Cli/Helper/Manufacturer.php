<?php


namespace Mlp\Cli\Helper;


class Manufacturer
{


    public static function getExpertManufacturer($manufacturer){

        return $manufacturer;
    }
    
    public static function getSorefozManufacturer($manufacturer)  {
        switch ($manufacturer) {
            case 'TOSHIBA  - INFORMATICA':
                return 'TOSHIBA';
            case 'SONY  - MAGNÉTICOS':
            case 'SONY  - PILHAS':
                return 'SONY';
            case 'SAMSUNG  - LINHA CASTANHA':
            case 'SAMSUNG  - INFORMATICA':
            case 'SAMSUNG  - LINHA BRANCA':
            case 'SAMSUNG  - AR CONDICIONADO':
                return 'SAMSUNG';
            case 'LG  - LINHA CASTANHA':
            case 'LG  - INFORMATICA':
            case 'LG  - LINHA BRANCA':
            case 'LG  - AR CONDICIONADO';
                return 'LG';
            case 'BROTHER  INFORMÁTICA':
                return 'BROTHER';
            case 'PHILIPS D.A.P.':
                return 'PHILIPS';
            case 'HOTPOINT / ARISTON':
            case 'HOTPOINT / ARISTON PAE':
                return 'HOTPOINT';
            case 'WHIRLPOOL  - PROFISSIONAL':
            case 'WHIRLPOOL  - AR CONDICIONADO':
                return 'WHIRLPOOL';
            case 'FAGOR  - CONFORT':
            case 'FAGOR  P.A.E.':
                return 'FAGOR';
            case 'EDESA  - CONFORT':
                return 'EDESA';
            case 'BOSCH  P.A.E.':
                return 'BOSCH';
            case 'SIEMENS  P.A.E.':
                return 'SIEMENS';
            case 'BRIEL  - CAFÉ':
                return 'BRIEL';
            case 'AEG  D.A.P.':
            case 'AEG  - CLIMATIZAÇÃO':
            case 'AEG  - USO PESSOAL':
                return 'AEG';
            case 'ZANUSSI  D.A.P.':
                return 'ZANUSSI';
            case 'ELECTROLUX  D.A.P.':
                return 'ELECTROLUX';
            case 'BRAUN  - CASA E COZINHA':
                return 'BRAUN';
            case 'NOS  - ZON/OPTIMUS':
                return 'NOS';
            default:
                return $manufacturer;
        }
    }
    public static function getOrimaManufacturer($manufacturer) {
        switch($manufacturer){
            case 'LG | LINHA CASTANHA':
            case 'LG | LINHA BRANCA':
            case 'LG | LINHA CONFORTO':
                return 'LG';
            case 'BOSCH | PEQUENOS DOMESTICOS':
            case 'BOSCH | LINHA BRANCA':
                return 'BOSCH';
            case 'SAMSUNG | LINHA BRANCA':
            case 'SAMSUNG | LINHA CASTANHA':
                return 'SAMSUNG';
            case 'SIEMENS | LINHA BRANCA':
            case 'SIEMENS | PEQUENOS DOMESTICOS':
                return 'SIEMENS';
            case 'AEG | PEQUENOS DOMESTICOS':
                return 'AEG';
            default:
                return $manufacturer;
        }
    }
}
