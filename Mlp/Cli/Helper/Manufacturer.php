<?php


namespace Mlp\Cli\Helper;


class Manufacturer
{

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
