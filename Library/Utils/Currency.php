<?php

namespace DigitalGrease\Library\Utils;

class Currency
{
    public static function byTimezone(string $timezone): array
    {
        switch ($timezone) {
            
            // Canada | Canadian Dollar
            case 'America/Toronto':
            case 'America/Winnipeg':
            case 'America/Edmonton':
            case 'America/Vancouver':
            case 'America/Halifax':
            case 'America/St_Johns':
                $currency = '{"symbol": "$", "code": "CAD"}';
                break;
            
            // EUR
            // Austria
            case 'Europe/Vienna':
            // Belgium
            case 'Europe/Brussels':
            // Croatia
            case 'Europe/Zagreb':
            // Cyprus
            case 'Asia/Nicosia':
            // Estonia.
            case 'Europe/Tallinn':
            // Finland
            case 'Europe/Helsinki':
            case 'Europe/Mariehamn':
            // France
            // French Guiana (France)
            // Guadeloupe (France)
            // Martinique (France)
            // Mayotte (France)
            // Réunion (France)
            // Saint Barthélemy (France)
            // Saint Martin (France)
            // Saint Pierre and Miquelon (France)
            case 'Europe/Paris':
            // Germany
            case 'Europe/Berlin':
            // Greece
            case 'Europe/Athens':
            // Ireland
            case 'Europe/Dublin':
            // Italy
            case 'Europe/Rome':
            // Latvia
            case 'Europe/Riga':
            // Lithuania
            case 'Europe/Vilnius':
            // Luxembourg
            case 'Europe/Luxembourg':
            // Malta
            case 'Europe/Malta':
            // Netherlands
            case 'Europe/Amsterdam':
            // Portugal
            case 'Europe/Lisbon':
            // Slovakia
            case 'Europe/Bratislava':
            // Slovenia
            case 'Europe/Ljubljana':
            // Spain
            case 'Europe/Madrid':
            // Andorra
            case 'Europe/Andorra':
            // Kosovo
            case 'Europe/Belgrade':
            // Monaco
            case 'Europe/Monaco':
            // Montenegro
            case 'Europe/Podgorica':
            // San Marino
            case 'Europe/San_Marino':
            // Vatican City
            case 'Europe/Vatican':
            // Azores (Portugal)
            case 'Atlantic/Azores':
            // Madeira (Portugal)
            case 'Atlantic/Madeira':
            // Canary Islands (Spain)
            case 'Atlantic/Canary':
                $currency = '{"symbol": "€", "code": "EUR"}';
                break;
            
            // GBP
            // United Kingdom (including England, Scotland, Wales, and Northern Ireland)
            case 'Europe/London':
            // Guernsey (Crown Dependency of the UK)
            case 'Europe/Guernsey':
            // Jersey (Crown Dependency of the UK)
            case 'Europe/Jersey':
            // Isle of Man (Crown Dependency of the UK)
            case 'Europe/Isle_of_Man':
            // South Georgia and the South Sandwich Islands (overseas territory of the UK)
            case 'Atlantic/South_Georgia':
            // British Indian Ocean Territory (overseas territory of the UK)
            case 'Indian/Chagos':
            // Tristan da Cunha (overseas territory of the UK)
            case 'Atlantic/St_Helena':
            // Gibraltar
            case 'Europe/Gibraltar':
                $currency = '{"symbol": "£", "code": "GBP"}';
                break;
            
            // NZD
            // New Zealand
            // Cook Islands (self-governing territory in free association with New Zealand)
            // Niue (self-governing territory in free association with New Zealand)
            case 'Pacific/Auckland':
            case 'Pacific/Chatham':
            // Tokelau (territory of New Zealand)
                // Uses the Samao timezone Pacific/Apia but Samoa has a different currency.
                $currency = '{"symbol": "$", "code": "NZD"}';
                break;
            
            // AUD
            // Australia
            // Christmas Island (territory of Australia)
            // Cocos (Keeling) Islands (territory of Australia)
            // Norfolk Island (territory of Australia)
            // Ashmore and Cartier Islands (territory of Australia)
            // Coral Sea Islands (territory of Australia)
            case 'Australia/Adelaide':
            case 'Australia/Brisbane':
            case 'Australia/Broken_Hill':
            case 'Australia/Darwin':
            case 'Australia/Eucla':
            case 'Australia/Hobart':
            case 'Australia/Lindeman':
            case 'Australia/Lord_Howe':
            case 'Australia/Melbourne':
            case 'Australia/Perth':
            case 'Australia/Sydney':
                $currency = '{"symbol": "$", "code": "AUD"}';
                break;
            
            // SGD
            case 'Asia/Singapore':
                $currency = '{"symbol": "$", "code": "SGD"}';
                break;
            
            // USD for all other timezones.
            default:
                $currency = '{"symbol": "$", "code": "USD"}';
        }
        
        return ['currency' => $currency];
    }
}
