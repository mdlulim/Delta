<?php


namespace Consnet\CustomerFilter\Plugin\Magento\Customer\Ui\Component;

class DataProvider
{

    public function afterGetData($result) {
       
        $Arra = array(1, 2, 3);
        $tmp['items'] = array ();
        $data['items'] = $result;
        foreach($data['items'] as $line){
            if(in_array($line['entity_id'],$Arra)){
              array_push($tmp['items'],$line);
            }
        }
        $data = $tmp;
        return $data;
    }
}
