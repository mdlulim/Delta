<?php
namespace Consnet\Erporder\Helper;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Zend\Soap\Client;

class MageOrder
{
    private $messageManager;
    protected $orderManagement;
    protected $productRepository;
    protected $orderFactory;
    protected $itemFactory;
    protected $message ;
    protected $helper;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Sales\Model\Order\ItemFactory $itemFactory,
        //\Magento\Framework\Message\ManagerInterface $message,
        MessageManager $messageManager){
        $this->messageManager = $messageManager;
        $this->orderManagement = $orderManagement;
        $this->productRepository = $productRepository;
        $this->orderFactory = $orderFactory;
        $this->itemFactory = $itemFactory;
        //$this->message = $message;
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $this->helper = $om->get('Consnet\Api\Helper\Data');
    }

    /**
     * Create Order On Your Store
     * 
     * @param array $orderData
     * @return array
     * 
    */
    public function updateOrder($order, $orderitems, $deliverydate, $customerId, $control_flow) {
        $delivery_date = null;//05122017
        
        if(substr_count($deliverydate, '-') > 0){
            //2018-01-24
            $date = date_create($deliverydate);
            $delivery_date = date_format($date, 'dmY');
            
        }elseif(!checkdate(substr($deliverydate, 0,2), //DAY
                     substr($deliverydate, 2,4), //MONTH
                     substr($deliverydate, 4))) //YEAR
        {
            $delivery_date = $deliverydate;
        }else {
            $date = date_create($deliverydate);
            //date_modify($date, '+1 day');
            $delivery_date = date_format($date, 'dmY');
        }

        $newOrder = $this->orderFactory->create()->load($order->getId());

        //print_r('order id '.$newOrder->getId()."qwerty");

        //$newOrder->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
        
        $items = array();
        
        $i = 0;
        
        //$customerId = 
        $company_data = $this->getCompanyData($customerId);//$newOrder->getCustomerId()
        
        if(in_array('STP_ID', array_keys($company_data))){
            //print_r('in 1 if');
            if(array_key_exists('STP_ID', $company_data)){   
                //print_r('in 2 if');         
                if(preg_match("/[a-z]/i", $company_data['STP_ID']))
                {   
                    //print_r('in 3 if');  
                    $stp = $company_data['STP_ID'];
                }else{
                    //print_r('in 3 else');  
                    $stp = str_pad($company_data['STP_ID'], 10, '0', STR_PAD_LEFT);
                  
                }
                //print_r($stp.'stp  <');
                $PMatnrs = "";
                foreach ($orderitems as $item){
                    $PMatnrs .= $item[0].":".$item[1].";";
                }
                //print_r('CHECK >>> '.$PMatnrs);
                if($this->hasValue($PMatnrs)){
                    $zresults = $this->change_ecc_salesorder($PMatnrs,
                                                             $this->get_ecc_order_number($newOrder->getRealOrderId()),
                                                             $company_data['PLANT'],
                                                             $delivery_date
                                                             );

                    $totalTax = 0;
                    
                    $newOrder->setItems(null);
                    foreach($newOrder->getItems() as $item){
                      $item->isDeleted(true);
                    }
                  
                    if(isset($zresults->ZRESULT) !== ""){
                        //print_r($zresults->ZRESULT);
                        if($control_flow == 0){
                            $this->messageManager->addSuccessMessage("Order Has Been Updated.");
                        }
                        

                       //print_r($zresults->ZRESULT);
                       if(is_array($zresults->ZTT_ORDER_TOTALS->item)){
                            foreach($zresults->ZTT_ORDER_TOTALS->item as $orderitm){
                                $product = $this->productRepository->get($orderitm->MATNR) ;                     
                                $item = $this->itemFactory->create();                                                            
                                $item->setProductId($product->getId());
                                $item->setDescription($product->getDescription());
                                $item->setName($product->getName());
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
                            $newOrder->setTaxAmount($totalTax);
                            $newOrder->setSubtotal($zresults->TOTAL);
                            $newOrder->setGrandTotal(($zresults->TOTAL + $totalTax) );
                            $newOrder->setData("DELIVERY_DATE", $delivery_date);
                            $newOrder->save();                                   
                       }else{
                                $line = $zresults->ZTT_ORDER_TOTALS->item ;
                                $product = $this->productRepository->get($line->MATNR) ;                     
                                $item = $this->itemFactory->create();                                                            
                                $item->setProductId($product->getId()); 
                                $item->setDescription($product->getDescription());
                                $item->setName($product->getName()); 
                                $item->setPrice($line->NETPR);
                                $item->setRowTotal($line->NETWR);
                                $item->setProductType($product->getProductType());
                                $item->setQtyOrdered($line->KWMENG);
                                $item->setSku($line->MATNR);
                                $newOrder->addItem($item);
                                $item = null ;
                                $product = null ;
                                //$totalTax = $line->KWMENG * $line->MWSBP ;
                                $i++; 
                                
                                $newOrder->setTaxAmount($line->MWSBP);
                                $newOrder->setSubtotal($zresults->TOTAL);
                                $newOrder->setGrandTotal(($zresults->TOTAL + $line->MWSBP) );
                                $newOrder->setData("DELIVERY_DATE", $delivery_date);
                                $newOrder->save();
                            }
                    }
                    else{
                            /*foreach($orderitems as $orderitm){
                                //ZCHANGE_SALES_ORDER                                   
                                $product = $this->productRepository->get($orderitm[0]);
                                                        
                                $item = $this->itemFactory->create();
                                                                
                                $item->setProductId($product->getId());
                                $item->setBasePrice($product->getPrice());
                                $item->setRowTotal($product->getFinalPrice());
                                $item->setOriginalPrice($product->getPrice());
                                $item->setProductType($product->getProductType());
                                $item->setQtyOrdered($orderitm[1]);
                                $item->setSku($orderitm[0]);
                                $newOrder->addItem($item);
                                $item = null ;
                                $product = null ;
                                $i++;           
                            }
                            //$newOrder->setItems($items);
                            $newOrder->setData("DELIVERY_DATE", $delivery_date);
                            $newOrder->save();*/   
                            if($control_flow == 0){
                                $this->message->addErrorMessage($zresults->ZRESULT); 
                            }                 
                        }
                    }
            }
        }        
        return $newOrder->getId();//$result;
    }

    public function cancel_order($ordernumber){
        $order = $this->orderFactory->create()->load($ordernumber);
        $order->cancel();
        $order->setStatus(\Magento\Sales\Model\Order::STATE_CLOSED);//STATE_COMPLETE);        
        $order->save();
        $this->messageManager->addSuccessMessage("Order Canceled");
        return $order->getId();
    }

    public function get_ecc_order_number($magentoId){
        //Custom ERP Order Number
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $resources = $om->get('Magento\Framework\App\ResourceConnection');
        $connection = $resources->getConnection();
        
        $query = "SELECT `erpOrderId` FROM `erp_magento` WHERE `magOrderId` = '".$magentoId."'";
        return  $connection->fetchRow($query)['erpOrderId'];
    }

    protected function getorderdata($order){
        return [
                    'currency_id'  => 'USD',
                    'email'        => $order->getShippingAddress()->getData('email'), //buyer email id
                    'shipping_address' =>[
                        'firstname'    => $order->getShippingAddress()->getData('firstname'), //address Details
                        'lastname'     => $order->getShippingAddress()->getData('lastname'),
                                'street' => $order->getShippingAddress()->getData('street'),
                                'city' => $order->getShippingAddress()->getData('city'),
                        'country_id' => $order->getShippingAddress()->getData('country_id'),
                        'region' => $order->getShippingAddress()->getData('region'),
                        'postcode' => $order->getShippingAddress()->getData('postcode'),
                        'telephone' => $order->getShippingAddress()->getData('telephone'),
                        'fax' => $order->getShippingAddress()->getData('fax'),
                        'save_in_address_book' => 1
                                ]
                ];
    }

    public function getCompanyData($customer_number){
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $companymanagement = $om->create('Magento\Company\Model\CompanyManagement');
        return $companymanagement->getByCustomerId($customer_number)->getData();
    }

    public function change_ecc_salesorder($PMatnrs, $eccDocNum , $plant, $delivery_date ){
        //$wsdlUrl = dirname(__FILE__)."/zchange_sales_order_v1_binding.xml";
        //$WURL = "http://deltadev01.delta.co.zw/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/rfc/sap/zchange_sales_order/220/zchange_sales_order_v1/zchange_sales_order_v1_binding?sap-client=220";
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
                             "P_PO" => '',
                             "P_SHIP" => '',
                             "P_SPART" => '',
                             "P_VKORG" => '',
                             "P_VTWEG" => '',
                             "P_MAGENTO_ORDER" => '',
                             "P_MAGENTO_USERNAME" => '',
                             "P_AUART" => '',
                             "P_DELIVERY_DATE" => $delivery_date
                            );

        //Call Funtion (passing in parameters)
        
        try{

            $result = $soapClient->ZCHANGE_SALES_ORDER($parameters);
            //print_r($result);
            return $result;
		         
        }
        catch (SoapFault $e){
            $this->messageManager->addErrorMessage("Soap call error".$e->getMessage());
            return null;
        }        
    }

    private function hasValue($string){
        if(!isset($string) || $string == null)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    
    public function get_ecc_order_status($order_number){
        $wsdlUrl = dirname(__FILE__)."/zsalesorder_function_group_binding.xml";		
        
        $WURL = $this->helper->getGeneralConfig('sales_order_text');
        
		//Set SOAP Options
        $options = array(
                            "soap_version" => SOAP_1_2 /*,
                            "exception" => 0*/      							
						);		
                      
        //Creat SOAP client instance
        $soapClient  = new Client($WURL, $options);

        //Set Login details
        $soapClient->setHttpLogin($this->helper->getGeneralConfig('user_name'));
        $soapClient->setHttpPassword($this->helper->getGeneralConfig('password'));

        //Set Parameters
        $parameters = array(
                            "E_STATUS_INFO" => array(
                            "item" => array (
                                "DOC_NUMBER" => "",
                                "DOC_DATE" => "",
                                "PURCH_NO" => "",
                                "PRC_STAT_H" => "",
                                "DLV_STAT_H" => "",
                                "REQ_DATE_H" => "",
                                "DLV_BLOCK" => "",
                                "ITM_NUMBER" => "",
                                "MATERIAL" => "",
                                "SHORT_TEXT" => "",
                                "REQ_DATE" => "",
                                "REQ_QTY" => "",
                                "CUM_CF_QTY" => "",
                                "SALES_UNIT" => "",
                                "NET_VALUE" => "",
                                "CURRENCY" => "",
                                "NET_PRICE" => "",
                                "COND_P_UNT" => "",
                                "COND_UNIT" => "",
                                "DLV_STAT_I" => "",
                                "DELIV_NUMB" => "",
                                "DELIV_ITEM" => "",
                                "DELIV_DATE" => "",
                                "DLV_QTY" => "",
                                "REF_QTY" => "",
                                "S_UNIT_ISO" => "",
                                "CD_UNT_ISO" => "",
                                "CURR_ISO" => "",
                                "MATERIAL_EXTERNAL" => "",
                                "MATERIAL_GUID" => "",
                                "MATERIAL_VERSION" => "",
                                "PO_ITM_NO" => "",
                                "CREATION_DATE" => "",
                                "CREATION_TIME" => "",
                                "S_UNIT_DLV" => "",
                                "DLV_UNIT_ISO" => "",
                                "REA_FOR_RE" => "",
                                "PURCH_NO_C" => "",
                            )),
                         "P_DOC_NO" => $this->get_ecc_order_number($order_number)
                           );

        //Call Funtion (passing in parameters)        
        try{
            $result = null;
            
            //$a = new Client($wsdlUrl, $options);
            //var_dump($a->ping());

            $result = $soapClient->ZGET_ORDER_STATUS($parameters);
            if ($result->E_RETURN->MESSAGE == "") {
                if (is_array($result->E_STATUS_INFO->item)) {
                    foreach ($result->E_STATUS_INFO->item as $item) {
                        switch ($item->PRC_STAT_H) {
                            case "A":
                            return 'pending';
                            //pending
                            break;                        
                            case "B":
                            return 'processing';
                            //processing
                            break;                        
                            case "C":
                            if ($item->REA_FOR_RE == 'Z7') {
                                return "canceled";
                            }
                            return 'closed';
                            //complete
                            break;
                        }                    	
                    }     
                }else {
                    switch ($result->E_STATUS_INFO->item->PRC_STAT_H) {
                        case "A":
                        return 'pending';
                        //pending
                        break;                        
                        case "B":
                        return 'processing';
                        //processing
                        break;                        
                        case "C":
                        if ($result->E_STATUS_INFO->item->REA_FOR_RE == 'Z7') {
                            return "canceled";
                        }
                        return 'closed';
                        //complete
                        break;
                    } 
                }  
            }else {
                //No Data
            }
            return 0;              	         
        }
        catch (Exception $e){
            //$this->messageManager->addErrorMessage("Required quantity is not available");
            return 0; 
        }
    }
}