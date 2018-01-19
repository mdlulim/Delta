<?php

namespace Consnet\Replication\Controller\Index;

use Magento\Framework\App\Action\Context;
use \Magento\User\Model\UserFactory;
use \Magento\User\Model\ResourceModel\User;

class Index extends \Magento\Framework\App\Action\Action {
    private $om;

    public function execute() {
        $this->om = \Magento\Framework\App\ObjectManager::getInstance();
        $this->loadproducts();
    }

    public function loadproducts(){
        
        $wsdlUrl = dirname(__FILE__) . "/zz_m_binding_v4.xml";
        $parameters = array(
            "IV_FROM_DATE" => null,
            "IV_TO_DATE" => null
        );

        $soapClient2 = new \Zend\Soap\Client($wsdlUrl, array("soap_version" => SOAP_1_2));

        // Set Login details
        $soapClient2->setHttpLogin('tmahihlaba');
        $soapClient2->setHttpPassword('COMTIA@7');

        $image = "";
        

        try {
            $result = $soapClient2->ZZ_BP_REPLICATION($parameters);//->EX_PRODUCTS;
            //var_dump($result->EX_PRODUCTS);//EX_CONTACT_PERSONS);//EX_PRODUCTS);EX_PRODUCTS
            $count = 0;
            foreach ($result->EX_PRODUCTS->item as $item) {
                //echo "<img src='/images/".str_replace(' ', '%20', $item->MATNR.".jpg")."'/><br/>";
                //echo 'Thato';
                if($count > 10){
                    $this->create_product($item->MATNR, 1, $item->MAKTX, $this->get_sbu($item->KONDM));
                }$count++;
            }
                //$image = $item->MAKTX;
        } catch (\Exception $ex) {
            echo $image.$ex->getMessage();

            /*<EX_PRODUCTS>
            <item>
            <MATNR>1010</MATNR>
            <MAKTX>1LGLASR1X12 COKE</MAKTX>
            <KONDM>Y1</KONDM>
            <VTEXT>Sparkling</VTEXT>*/
        }

    }

    public function create_product($material_number, $price, $description, $sbu){
        
        try {
            $_product = $this->om->create('Magento\Catalog\Model\Product');
            $_product->setName($description);
            $_product->setTypeId('simple');
            $_product->setAttributeSetId(4);
            $_product->setSku($material_number);
            //$_product->setWebsiteIds(array(1));
            $_product->setVisibility(4);
            $_product->setPrice($price);
            $_product->setCategoryIds($sbu);
            //str_replace(' ', '%20', $description.".png");
            //echo 'media/catalog/'.str_replace(' ', '%20', $description.".png");
            /*$_product->addImageToMediaGallery(
                '/media/import/'.str_replace(' ', '%20', $material_number.".jpg"),
                //'media/catalog/'.str_replace(' ', '%20', $description.".png"),//$this->module_path.'images/'.$description.'.png',
                array(
                        'image',
                        'small_image',
                        'thumbnail'
                ), false, false);*/
            $_product->setStatus(1);
            
            $_product->setStockData(array(
                'use_config_manage_stock' => 0, //'Use config settings' checkbox
                'manage_stock' => 1, //manage stock
                'min_sale_qty' => 1, //Minimum Qty Allowed in Shopping Cart
                'max_sale_qty' => 2, //Maximum Qty Allowed in Shopping Cart
                'is_in_stock' => 1, //Stock Availability
                'qty' => 99999 //qty
                )
            );
            $_product->save();

        } catch (Exception $e) {
            /*$_product = $om->create('Magento\Catalog\Model\Product');
            $_product->setName($description);
            $_product->setTypeId('simple');
            $_product->setAttributeSetId(4);
            $_product->setSku($material_number);
            $_product->setWebsiteIds(array(1));
            $_product->setVisibility(4);
            $_product->setPrice($price);
            $_product->setCategoryIds($sbu);
            $_product->setStatus(1);
            
            $_product->setStockData(array(
                'use_config_manage_stock' => 0, //'Use config settings' checkbox
                'manage_stock' => 1, //manage stock
                'min_sale_qty' => 1, //Minimum Qty Allowed in Shopping Cart
                'max_sale_qty' => 2, //Maximum Qty Allowed in Shopping Cart
                'is_in_stock' => 1, //Stock Availability
                'qty' => 9999999 //qty
                )
            );

            $_product->save();*/
        }
        
    }

    public function get_sbu($material_group){
        switch ($material_group){
            case 'Y1': //Y1 Sparkling
             return array(8);
             break;
            case 'Y2': //Y2 Lagers
             return array(9);
             break;
            case 'Y3': //Y3 Sorghum
             return array(10);
             break;
            case 'Y4': //Y4 Maltings
             return array(11);
             break;
            case 'Y5': //Y5 ALL
             return array(4,8,9,10,11);
             break;
        }
    }
}
