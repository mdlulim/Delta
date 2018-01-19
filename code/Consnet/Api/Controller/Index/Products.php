<?php

namespace Consnet\Api\Controller\Index;

use Magento\Framework\App\Action\Context;
use \Magento\User\Model\UserFactory;
use \Magento\User\Model\ResourceModel\User;

class Product extends \Magento\Framework\App\Action\Action {

	public function execute() {
        $this->loadproducts();
    }

	public function loadproducts(){
        
        $wsdlUrl = dirname(__FILE__) . "/z_bp_rep_v3.xml";
        $parameters = array(
            "IV_FROM_DATE" => null,
            "IV_TO_DATE" => null
        );

        $soapClient2 = new \Zend\Soap\Client($wsdlUrl, array("soap_version" => SOAP_1_2));

        // Set Login details
        $soapClient2->setHttpLogin('tmahihlaba');
        $soapClient2->setHttpPassword('COMTIA@7');


        try {
            $result = $soapClient2->ZZ_BP_REPLICATION($parameters);
            var_dump($result);
        } catch (\Exception $ex) {
            /*<EX_PRODUCTS>
            <item>
            <MATNR>1010</MATNR>
            <MAKTX>1LGLASR1X12 COKE</MAKTX>
            <KONDM>Y1</KONDM>
            <VTEXT>Sparkling</VTEXT>*/
        }

    }

    public function create_product($material_number, $price, $description, $sbu){
    	$om = \Magento\Framework\App\ObjectManager::getInstance();
        $_product = $om->create('Magento\Catalog\Model\Product');
        $_product->setName($description);
        $_product->setTypeId('simple');
        $_product->setAttributeSetId(4);
        $_product->setSku($material_number);
        $_product->setWebsiteIds(array(1));
        $_product->setVisibility(4);
        $_product->setPrice($price);
        $_product->setCategoryIds(array(3));
        //$_product->addImageToMediaGallery(
        //    "media/import/".$description.".png",//$this->module_path.'images/'.$description.'.png',
        //    array(
        //            'image',
        //            'small_image',
        //            'thumbnail'
        //    ), false, false);
        //$_product->setImage("media/import/".$description.".png");//$this->module_path.'images/'.$description.'.png');
        //$_product->setSmallImage("media/import/".$description.".png");//$this->module_path.'images/'.$description.'.png');
        //$_product->setThumbnail("media/import/".$description.".png");//$this->module_path.'images/'.$description.'.png');
        $_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        
        $_product->setStockData(array(
            'use_config_manage_stock' => 0, //'Use config settings' checkbox
            'manage_stock' => 1, //manage stock
            'min_sale_qty' => 1, //Minimum Qty Allowed in Shopping Cart
            'max_sale_qty' => 2, //Maximum Qty Allowed in Shopping Cart
            'is_in_stock' => 1, //Stock Availability
            'qty' => 100 //qty
            )
        );

        //Set tier price
        $tierPrices[] = array(
                        'website_id'  => 0,
                        'cust_group'  => 5,
                        'price_qty'   => 10,
                        'price'       => 7
                        );

        //Set the tier price to product
        //$_product->setTierPrice($tierPrices);

        $_product->save();
    }

    public function get_sbu($material_group){
        switch ($material_group){
            case 'Y1': //Y1 Sparkling
             return 8;
             break;
            case 'Y2': //Y2 Lagers
             return 9;
             break;
            case 'Y3': //Y3 Sorghum
             return 10;
             break;
            case 'Y4': //Y4 Maltings
             return 11;
             break;
            case 'Y5': //Y5 ALL
             return 4;
             break;
        }
    }
}

?>