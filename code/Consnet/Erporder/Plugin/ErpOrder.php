<?php
namespace Consnet\Erporder\Plugin;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Zend\Soap\Client;
use SoapFault;

class ErpOrder
{
    private $messageManager;
    private $_resources;
    private $_om;
    private $_module_path;
    private $_connection;
    private $_PMatnrs;

    //Customer DATA
    private $_magCustomerId;
    private $_magOrderId;

    private $_erpOrderId;
    private $_erpOrderCreated;

    private $_customerSession;

    protected $url ;
    protected $responseFactory ;

    protected $MageOrder;
    protected $helper;

    public function __construct(MessageManager $messageManager,
        \Magento\Framework\UrlInterFace $url,
        \Magento\Framework\App\ResponseFactory $responseFactory 
    ){
        $this->messageManager = $messageManager;
        $this->om = \Magento\Framework\App\ObjectManager::getInstance();
        $this->customerSession = $this->om->get('Magento\Customer\Model\Session');
        $this->_resources = $this->om->get('Magento\Framework\App\ResourceConnection'); 
        $this->_connection = $this->_resources->getConnection();
        $this->module_path = dirname(__FILE__)."/";
        $this->url = $url ;
        $this->responseFactory = $responseFactory  ;

        $this->MageOrder = $this->om->get('Consnet\Erporder\Helper\MageOrder');
        $this->helper = $this->om->get('Consnet\Api\Helper\Data');
    }

    public function beforePlace($order){

        //$order = $result;
        //var_dump($order->getRealOrderId());
        
        $order_number = substr($order->getRealOrderId(), 0,9);
        $current  = $this->om->create('\Magento\Sales\Api\Data\OrderInterface')->loadByIncrementId($order_number);
        if($current->getId()){
            //edit 
            $edited_order = $this->om->create('\Magento\Sales\Model\Order')->load($current->getId());
            
            /*$edited_order->setItems(null);
            
            foreach($edited_order->getAllItems() as $item) {
                $item->isDeleted(true);
            }*/
            //$edited_order->setItems($order->getAllItems());

            /*foreach($zresults->ZTT_ORDER_TOTALS->item as $orderitm){
                $product = $this->productRepository->get($orderitm->MATNR) ;                     
                $item = $this->itemFactory->create();                                                            
                $item->setProductId($product->getId());
                $item->setRowTotal($orderitm->NETWR);
                $item->setPrice($orderitm->NETPR);
                $item->setOriginalPrice($orderitm->NETPR);
                $item->setProductType($product->getProductType());
                $item->setQtyOrdered($orderitm->KWMENG);
                $item->setSku($orderitm->MATNR);
                $newOrder->addItem($item);
                $item = null ;
                $product = null ;
                $totalTax = $totalTax + $orderitm->MWSBP ;
                $i++;
            }            
            foreach($edited_order->getItems() as $item){
                $item->isDeleted(true);
                $edited_order->setItems($item);
                $edited_order->save();
            }
            foreach ($order->getAllItems() as $item) {
                foreach ($edited_order->getAllItems() as $existing_item) {
                    if($item->getSku() == $existing_item->getSku()){
                        $existing_item->setQtyOrdered($item->getQtyOrdered());
                    }else{
                        $edited_order->addItem($item);
                    }                    
                }
            }
            */   
            
            //getItemById

            foreach($edited_order->getAllItems() as $item) {
                $item->isDeleted(true);
                //$edited_order->removeItem($item->getId());
            }
            $edited_order->save();
            //$newitems = array();

            foreach ($order->getAllItems() as $item) {
                $edited_order->addItem($item);
            }
            
            //foreach ($edited_order->getAllItems() as $existing_item) {        
            //    foreach ($order->getAllItems() as $item) {

                    //if($item->getSku() == $existing_item->getSku()){
                        //array_push($newitems, $existing_item);
                        //$existing_item->setQtyOrdered($item->getQtyOrdered());
                    //}else{
                        //array_push($newitems, $existing_item);
                        //$edited_order->addItem($item);
                    //}                    
            //    }
            //}

            $edited_order->save();
            $_SESSION['CURRENT_ORDER'] = $edited_order->getId();
            return $edited_order;
        }
        //var_dump('Current Order Null');
        return null;
    }

    public function afterPlace($result){
        $this->magOrderId = $result->getRealOrderId();
        unset($_SESSION['items']);
        
        if(isset($_SESSION['DELIVERY_DATE'])){
            $date  = date_create($_SESSION['DELIVERY_DATE']);
            $delivery_date   = date_format($date,'dmY');
        }else {
            $delivery_date = $result->getData("DELIVERY_DATE");
        }

        if(isset($_SESSION['CURRENT_ORDER'])){
           // return $this->om->create('\Magento\Sales\Api\Data\OrderInterface')->load($_SESSION['CURRENT_ORDER']);
                $edited_order = $this->om->create('\Magento\Sales\Model\Order')->load($_SESSION['CURRENT_ORDER']);
                $this->magCustomerId = $edited_order->getCustomerId();
                $customer_model = $this->om->create('Magento\Customer\Model\Customer');
                $customer_model->setWebsiteId(1);
                $customer = $customer_model->load($this->magCustomerId);
                $customer_data = $customer->getData();            
                $company_data = $this->getCompanyData($customer_data['entity_id']);

                $PMatnrs = "";
                foreach ($edited_order->getAllItems() as $item){
                    $PMatnrs .= $item->getSku().":".$item->getQtyToInvoice().";";
                }
                $zresults = $this->change_ecc_salesorder($PMatnrs,
                                                         $this->get_ecc_order_number($edited_order->getRealOrderId()),
                                                         $company_data['PLANT'],
                                                         $delivery_date
                                                         );
                
                $this->update_magento_order($edited_order->getId(), $customer_data['entity_id']);
                //return $edited_order;
                $LOCATION  = $this->url->getUrl('sales/order/view/order_id/'.$edited_order->getId());
                unset($_SESSION['CURRENT_ORDER']);
                $this->responseFactory->create()->setRedirect($LOCATION)->sendResponse();
                exit;
                //return null;
        }
        else{
            $order = $result;            
            $order->setData("DELIVERY_DATE", $delivery_date);        
            $this->magCustomerId = $result->getCustomerId();
            if($this->magCustomerId !== null && $this->magCustomerId !== '' && isset($this->magCustomerId)){
                $customer_model = $this->om->create('Magento\Customer\Model\Customer');
                $customer_model->setWebsiteId(1);
                $customer = $customer_model->load($this->magCustomerId);
                $customer_data = $customer->getData();
                //var_dump($customer_data);  
                $company_data = $this->getCompanyData($this->magCustomerId);//$customer_data['entity_id']);
                if(preg_match("/[a-z]/i", $company_data['STP_ID'])){
                    $stp = $company_data['STP_ID'];
                }else{
                    $stp = str_pad($company_data['STP_ID'], 10, '0', STR_PAD_LEFT);
                }

                $orderItems = $order->getAllVisibleItems();
                
                $zresults = $this->createSalesOrderECC($order->getData("DELIVERY_DATE"),
                                        $orderItems, 
                                        $stp,
                                        $company_data["PLANT"],
                                        $company_data["VKORG"],
                                        $company_data["VTWEG"],
                                        $company_data["SPART"],
                                        $order->getId(),
                                        $customer_data['email'],//$customer->getEmail(),
                                        $order->getPayment()->getPoNumber());
                $this->messageManager->addSuccessMessage("Thank you for placing the order, our agents will verify the order.");
                $totalTax = 0.0;
                
                if($zresults != NULL){
                    if($zresults->ZRESULT !== 'FAILED'){
                        if(is_array($zresults->ZTT_ORDER_TOTALS->item)){
                            if(is_array($zresults->ZTT_ORDER_TOTALS->item)){
                                foreach($zresults->ZTT_ORDER_TOTALS->item as $orderitm){
                                    $totalTax = $totalTax + $orderitm->MWSBP ;
                                    //$i++;
                                }
                                $order->setTaxAmount($totalTax);
                                $order->setSubtotal($zresults->TOTAL);
                                $order->setGrandTotal(($zresults->TOTAL + $totalTax));
                                $order->save();
                            }
                        }else{
                            $line = $zresults->ZTT_ORDER_TOTALS->item ;               
                            $order->setTaxAmount($zresults->ZTT_ORDER_TOTALS->item->MWSBP);
                            $order->setSubtotal($zresults->TOTAL);
                            $order->setGrandTotal(($zresults->TOTAL + $line->MWSBP) );
                            $order->save();
                        }
                        
                        $order->setData("DELIVERY_DATE", $delivery_date);
                        $order->save();
                        $order->setData("STP_ID", $stp);
                        $order->save();                    
                        $order->setData('ECC_ORDER', $zresults->ZRESULT);//7777);//$this->erpOrderId);//$zresults->ZRESULT);
                        $order->save();
                        $order->setData('erp_order', $zresults->ZRESULT);//6666);//$this->erpOrderId);
                        $order->save();
                        return NULL;$this->om->create('\Magento\Sales\Model\Order')->load($order_id);
                    }elseif ($zresults->ZRESULT == 'FAILED') {
                        $this->messageManager->addErrorMessage("Could Not Create Order");
                        return null;
                    } 
                }else{
                    return null;
                }
            }
        }
    }

    public function update_magento_order($order_number, $customerId){
        $order = $this->om->create('\Magento\Sales\Model\Order')->load($order_number);
        $Matnrs = array();
        $count = 0;
        foreach ($order->getAllVisibleItems() as $item){
            $Matnrs = array($item->getSku(), $item->getQtyToInvoice());
            $count++;
        }

        $DBdate = $order->getData('DELIVERY_DATE');
        $time = strtotime(substr($DBdate, 0,2).'/'.substr($DBdate, 2,2).'/'.substr($DBdate, 3));
        $delivery_date = date('Y-m-d', $time);

        $result = $this->MageOrder->updateOrder($order, $Matnrs, $delivery_date, $customerId, 0);
        $result = $this->MageOrder->updateOrder($order, $Matnrs, $delivery_date, $customerId, 1);
        //$result = 
    } 

    public function change_ecc_salesorder($PMatnrs, $eccDocNum , $plant, $delivery_date ){
        //$wsdlUrl = dirname(__FILE__)."/zchange_sales_order_v1_binding.xml";
        //$WURL = "http://deltadev01.delta.co.zw/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/rfc/sap/zchange_sales_order/220/zchange_sales_order_v1/zchange_sales_order_v1_binding?sap-client=220";
        //$WURL = "http://deltadev01.delta.co.zw/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/rfc/sap/zmage_sales_function_group/220/zmage_sales_function_group/zmage_sales_function_group?sap-client=220";
        //$WURL = "http://deltadev01.delta.co.zw/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/rfc/sap/zmagento_sales_modules/220/zmagento_sales_modules/zmagento_sales_modules?sap-client=220";
        $WURL = $this->helper->getGeneralConfig('sales_order_text');
        
		//Set SOAP Options
        $options = array(
							"soap_version" => SOAP_1_2       							
						);		
		
        //Creat SOAP client instance
        $soapClient  = new Client($WURL, $options);

        //Set Login details
        $soapClient->setHttpLogin($this->helper->getGeneralConfig('user_name'));
        $soapClient->setHttpPassword($this->helper->getGeneralConfig('password'));

        //print_r($plant);

        //Set Parameters
        $parameters = array( "P_MATNRS" => $PMatnrs,
                             "P_DOC_NO" => $eccDocNum,
                             "P_PLANT" => $plant,
                             "P_PO" => null,
                             "P_SHIP" => null,
                             "P_SPART" => null,
                             "P_VKORG" => null,
                             "P_VTWEG" => null,
                             "P_MAGENTO_ORDER" => null,
                             "P_MAGENTO_USERNAME" => null,
                             "P_AUART" => null,
                             "P_DELIVERY_DATE" => $delivery_date
                            );

        //Call Funtion (passing in parameters)
        
        try{

            $result = $soapClient->ZCHANGE_SALES_ORDER($parameters);
            $this->messageManager->addSuccessMessage("Order Has Been Updated.");
            //print_r($result);
            return $result;
		         
        }
        catch (SoapFault $e){
            $this->messageManager->addErrorMessage("Soap call error".$e->getMessage());
            return null;
        }        
    }
    
    public function availableUrl($host, $port=80, $timeout=10) { 
        $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
        $result = $fp!=false;
  		return $result;
	 }    
    
    
     public function getCompanyData($customer_number){
        $companymanagement = $this->om->create('Magento\Company\Model\CompanyManagement');
        return $companymanagement->getByCustomerId($customer_number)->getData();
    }

    public function writeToDB(){
        $this->_resources = $this->om->get('Magento\Framework\App\ResourceConnection');
        $connection= $this->_resources->getConnection();

        $erp_magento_table = $this->_resources->getTableName('erp_magento');
        $sql = "INSERT INTO " . $erp_magento_table . "(magOrderId, magCustomerId, erpOrderId, erpOrderCreated) 
                VALUES 
                ('$this->magOrderId', '$this->magCustomerId', '$this->erpOrderId', '$this->erpOrderCreated')".
                "ON DUPLICATE KEY UPDATE    
                magCustomerId='$this->magCustomerId', erpOrderId='$this->erpOrderId', erpOrderCreated='$this->erpOrderCreated'";
        $connection->query($sql);
    }

    public function createSalesOrderECC($DELIVERY_DATE, $orderItems, $soldtoparty_number, $plant, $Vkorg,
    												 $Vtweg, $Spart, $orderId, $customerEmail, $po_number){
        //$wsdlUrl = $this->module_path."wsdl/zsalesorder_service_group_binding.xml";
        //$WURL = "http://deltadev01.delta.co.zw/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/rfc/sap/zmage_sales_function_group/220/zmage_sales_function_group/zmage_sales_function_group?sap-client=220";
        $WURL = $this->helper->getGeneralConfig('sales_order_text');
        
		//Set SOAP Options
        $options = array(
							"soap_version" => SOAP_1_2       							
						);		
		
        //Creat SOAP client instance
        $soapClient  = new Client($WURL, $options);

        //Set Login details
        $soapClient->setHttpLogin($this->helper->getGeneralConfig('user_name'));
        $soapClient->setHttpPassword($this->helper->getGeneralConfig('password'));

        $this->PMatnrs = "";
        foreach ($orderItems as $item){
            $this->PMatnrs .= $item->getSku().":".$item->getQtyToInvoice().";";
        }
                
        //Set Parameters
        $parameters = array(
                            "P_AUART" => "ZOR",
                            "P_MATNRS" => $this->PMatnrs,
                            "P_PLANT" => $plant,//"0",//"P060",
                            "P_SHIP" => $soldtoparty_number,
                            "P_SOLD" => $soldtoparty_number,
                            "P_SPART" => $Spart,
                            "P_VKORG" => $Vkorg,
                            "P_VTWEG" => $Vtweg,
                            "P_MAGENTO_ORDER" => $this->magOrderId,//$orderId,//$this->magOrderId, 
                            "P_MAGENTO_USERNAME" => $customerEmail,
                            "P_PO" => $po_number,
                            "P_DELIVERY_DATE" => $DELIVERY_DATE//"10112017"//
                            );// 
        
        //Call Funtion (passing in parameters)
        try{
            $result = $soapClient->ZCREATE_SALES_ORDER($parameters);
            $this->erpOrderId = ($result->ZRESULT == 'FAILED' ? 0 : 1);
            $this->erpOrderCreated = 1;
            $this->writeToDB();
            return $result;
        }
        catch (SoapFault $e){
            $this->erpOrderId = 0;
            $this->erpOrderCreated = 0;
            $this->writeToDB();
            //$this->messageManager->addSuccessMessage("Soap call error".$e->getMessage());
            return null;
        }   
    }

    public function get_ecc_order_number($magentoId){
        //Custom ERP Order Number
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $resources = $om->get('Magento\Framework\App\ResourceConnection');
        $connection = $resources->getConnection();
        
        $query = "SELECT `erpOrderId` FROM `erp_magento` WHERE `magOrderId` = '".$magentoId."'";
        return  $connection->fetchRow($query)['erpOrderId'];
    }
}
?>