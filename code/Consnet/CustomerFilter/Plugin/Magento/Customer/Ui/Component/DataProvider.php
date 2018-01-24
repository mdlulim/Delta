<?php


namespace Consnet\CustomerFilter\Plugin\Magento\Customer\Ui\Component;

class DataProvider
{

    public function afterGetData($result) {
        $data['items'] = $result;
        $Arra = array(1, 2, 3);
        $tmp['items'] = array ();
        
        foreach($data['items'] as $line){
            if(in_array($line['entity_id'],$Arra)){
              array_push($tmp['items'],$line);
            }
        }
        var_dump($tmp);die();
        $data = $tmp;
        return $data;
    }
}
