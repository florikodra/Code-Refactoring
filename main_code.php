<?php

define("BIN_URL", "https://lookup.binlist.net/");
define("RATE_URL", "https://api.exchangeratesapi.io/latest");

$file_contents = explode(PHP_EOL, file_get_contents('input.txt'));
$result="";

$file_arrays = array_map( function( $file_content ) {
    return json_decode($file_content, true);
}, $file_contents );


foreach($file_arrays as $file_array){

    $commission_amount = $file_array['amount'];
    $bin_result = json_decode(file_get_contents(BIN_URL.$file_array['bin']));
    if($bin_result){
        $rate_result = @json_decode(file_get_contents(RATE_URL), true)['rates'][$file_array['currency']];

        if ($file_array['currency'] != 'EUR' || $rate_result > 0) {
            $commission_amount =$commission_amount/$rate_result;
        }
        
        if(check_is_eu($bin_result->country->alpha2)){
            $commission_amount = $commission_amount*0.01;
        }
        else{
            $commission_amount = $commission_amount*0.02;
        }
        echo round($commission_amount,2)."<br>";
    }
}


function check_is_eu($country_code){
    $result = false;
    $europe = array('AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','FR','GR','HR','HU','IE','IT','LT','LU','LV','MT','NL','PO','PT','RO','SE','SI','SK');
    if(array_search($country_code, $europe)){
        $result = true;
    }
    return $result;
}  

 
?>