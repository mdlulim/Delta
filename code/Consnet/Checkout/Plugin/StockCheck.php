<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Consnet\Checkout\Plugin;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Zend\Soap\Client;

class StockCheck
{
    private $messageManager;

    public function __construct(MessageManager $messageManager){
        /*$om = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $om->get('Magento\Customer\Model\Session');*/

        /*$customer_model = $om->create('Magento\Customer\Model\Customer');//->
        $customer_model->setWebsiteId(1);
        $customer = $customer_model->load($customerSession->getId());
        $customerData = $customer->getData();*/

        /*$companymanagement = $om->create('Magento\Company\Model\CompanyManagement');
        $stp_id = $companymanagement->getByCustomerId($customerSession->getId())->getData("STP_ID");
        $soldtoparty_number = $stp_id;//$customerData['stp_id'];*/
        $this->messageManager = $messageManager;
    }

    /**
     * Add product to shopping cart (quote)
     *
     * @param int|Product $productInfo
     * @param \Magento\Framework\DataObject|int|array $requestInfo
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
     public function beforeAddOrderItem($orderItem, $qtyFlag = null){
       $product = $this->checkECCStock($orderItem);
        //var_dump($product);
        $orderItem->setQtyInvoiced($product->Qty);
        return null;
     }

    public function checkECCStock($item){
        $wsdlUrl = dirname(__FILE__)."/zsalesorder_services_bindings.xml";

        //Creat SOAP client instance
        $soapClient  = new Client($wsdlUrl,array("soap_version" => SOAP_1_2));

        //Set Login details
        $soapClient->setHttpLogin('tmahihlaba');
        $soapClient->setHttpPassword('COMTIA@7');
        
        //Set Parameters
        $parameters = array(
        "PMatnrs" => $item->getSku().":".$item->getQtyToInvoice().";",
        "PPlant" => "D123"
        );

        //Call Funtion (passing in parameters)
        try{
            $result = $soapClient->ZstockCheck($parameters);
            $product = $result->EResults;
            return $product;
           $this->messageManager->addSuccessMessage($product->Matnr . ' ' .
            $product->Qty);
        }
        catch (SoapFault $e){
            $this->messageManager->addSuccessMessage("Soap call error: ".$e->getMessage());
        }   
    }
}
