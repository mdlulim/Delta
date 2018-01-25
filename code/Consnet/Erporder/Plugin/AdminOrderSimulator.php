<?php
namespace Consnet\Erporder\Plugin;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Zend\Soap\Client;

class AdminOrderSimulator
{
    private $messageManager;
    private $_resources;
    private $_om;
    private $_module_path;
    private $_connection;
    private $_PMatnrs;
    private $address;

    //Customer DATA
    private $_magCustomerId;
    private $_magOrderId;

    private $_erpOrderId;
    private $_erpOrderCreated;

    private $_customerSession;
    protected $helper;

    public function __construct(
        MessageManager $messageManager,
        \Magento\Quote\Api\Data\AddressInterface $address){
        $this->address = $address;
        $this->messageManager = $messageManager;
        $this->om = \Magento\Framework\App\ObjectManager::getInstance();
        $this->customerSession = $this->om->get('Magento\Customer\Model\Session');
        $this->_resources = $this->om->get('Magento\Framework\App\ResourceConnection');
        $this->_connection = $this->_resources->getConnection();
        $this->module_path = dirname(__FILE__)."/";
        $this->helper = $this->om->get('Consnet\Api\Helper\Data');
    }

    public function afterBeforeSave($result){
        $quote = $result;
        if($result->getCustomerId() !== null && $result->getCustomerId() !== ""){
            $this->magOrderId = $result->getRealOrderId();
            
            
            $this->magCustomerId = $result->getCustomerId();
    
            $customer_model = $this->om->create('Magento\Customer\Model\Customer');
            $customer_model->setWebsiteId(1);
            $customer = $customer_model->load($this->magCustomerId);
            $customer_data = $customer->getData();
            
            $company_data = $this->getCompanyData($this->magCustomerId);//$customer_data['entity_id']);
            //$address = $this->getCompanyAddress($company_data, $this->magCustomerId);
            //$quote->setShippingAddress($address);

            $addressInfo=[
                'shipping_address' =>[
                       'firstname'    => $customer_data['firstname'], //address Details
                       'lastname'     => $customer_data['lastname'],
                               'street' => $company_data['street'],
                               'city' => $company_data['city'],
                       'country_id' => $company_data['country_id'],
                       'region' => $company_data['region'],
                       'postcode' => $company_data['postcode'],
                       'telephone' => $company_data['telephone'],
                       'fax' => '',
                       'save_in_address_book' => 0
                            ]
           ];
           //var_dump($addressInfo);die;
            //Set Address to quote
            $quote->getBillingAddress()->addData($addressInfo['shipping_address']);
            $quote->getShippingAddress()->addData($addressInfo['shipping_address']);
            
              if(in_array('STP_ID', array_keys($company_data))){
                    if(array_key_exists('STP_ID', $company_data)){
                    
                        if(preg_match("/[a-z]/i", $company_data['STP_ID'])){
                            $stp = $company_data['STP_ID'];
                        }else{
                            $stp = str_pad($company_data['STP_ID'], 10, '0', STR_PAD_LEFT);
                        }
                    //$stp = str_pad($company_data['STP_ID'], 10, '0', STR_PAD_LEFT);
                    
                    $orderItems = $quote->getAllVisibleItems();
                    
                    $PMatnrs = "";
                    foreach ($quote->getAllVisibleItems() as $item){
                        $PMatnrs .= $item->getSku().":".$item->getQty().";";
                    }
                      if($this->hasValue($PMatnrs)){
                        $zresults = $this->simulateSalesOrder($PMatnrs,
                                                              $company_data["PLANT"],
                                                              $stp,
                                                              $company_data["VKORG"],
                                                              $company_data["VTWEG"],
                                                              $company_data["SPART"]);
                        if($zresults !== null){                            
                            if(isset($zresults->ZRESULTS->item)) {	
                                $totalTax = 0;
                                $total = 0;		        
                                foreach($quote->getAllVisibleItems() as $item){
                                    $price_per_item =  null;
                                    $subtotal = array();
                                    if(is_array($zresults->ZRESULTS->item)){
                                        for($i = 0; $i < count($zresults->ZRESULTS->item); $i++) {
                                            if($item->getSku() === $zresults->ZRESULTS->item[$i]->MATERIAL) {	
                                                if($item->getPrice() !== $zresults->ZRESULTS->item[$i]->NET_VALUE1){
                                                    $price_per_item = $this->calculatePricePerItem($zresults->ZRESULTS->item[$i]->NET_VALUE1, $item->getQty());
                                                    $item->setPrice($price_per_item);		
                                                    $item->setCustomPrice($price_per_item);
                                                    $item->setOriginalCustomPrice($price_per_item);
                                                    $item->setRowTotal($zresults->ZRESULTS->item[$i]->NET_VALUE1);
                                                    $item->getProduct()->setIsSuperMode(true);

                                                    $totalTax = $totalTax + $zresults->ZRESULTS->item[$i]->TX_DOC_CUR; 
                                                    $total = $total + $zresults->ZRESULTS->item[$i]->NET_VALUE1;//$price_per_item;
                                                    //$quote->setTaxAmount($zresults->ZRESULTS->item[$i]->TX_DOC_CUR);
                                                    //$quote->setSubtotal($zresults->ZRESULTS->item[$i]->NET_VALUE1);
                                                    //$quote->setGrandTotal(($zresults->ZRESULTS->item[$i]->SUBTOTAL6) );
                                                    $this->messageManager->addSuccessMessage('You added '.$item->getName().
                                                    ' to your shopping cart.');
                                                }
                                            } 
                                            
                                            //if(isset($_SESSION['flowcontrol'])){
                                               
                                            //}else {
                                            //    $this->messageManager->addSuccessMessage('You added '.$item->getName().
                                            //    ' to your shopping cart.');
                                            //}
                                        }
                                    }
                                    else {
                                        if($item->getSku() === $zresults->ZRESULTS->item->MATERIAL) {	
                                            if($item->getPrice() !== $zresults->ZRESULTS->item->NET_VALUE1){
                                                $price_per_item = $this->calculatePricePerItem($zresults->ZRESULTS->item->NET_VALUE1, $item->getQty());										
                                                $item->setPrice($price_per_item);		
                                                $item->setCustomPrice($price_per_item);
                                                $item->setOriginalCustomPrice($price_per_item);
                                                $item->setRowTotal($zresults->ZRESULTS->item->NET_VALUE1);
                                                $item->getProduct()->setIsSuperMode(true);
                                                //$item->save();
                                                $totalTax = $totalTax + $zresults->ZRESULTS->item->TX_DOC_CUR;
                                                $total = $total + $zresults->ZRESULTS->item->NET_VALUE1;//$price_per_item;
                                                //$quote->setTaxAmount($zresults->ZRESULTS->item->TX_DOC_CUR);
                                                //$quote->setSubtotal($zresults->ZRESULTS->item->NET_VALUE1);
                                                //$quote->setGrandTotal(($zresults->ZRESULTS->item->SUBTOTAL6) );
                                                $this->messageManager->addSuccessMessage('You added '.$item->getName().
                                                ' to your shopping cart.');
                                                if(isset($_SESSION['flowcontrol'])){
                                                    
                                                }else {
                                                    //$this->messageManager->addSuccessMessage('You added '.$item->getName().
                                                    //' to your shopping cart.');
                                                }
                                            }
                                        }
                                    }
                                }           
                                //var_dump($totalTax);die();                     
                                $quote->setTaxAmount($totalTax);
                                $quote->setSubtotal($total);
                                //$quote->setBaseSubtotal($total);
                                //$quote->setBaseGrandTotal(($total + $totalTax));
                                $quote->setGrandTotal(($total + $totalTax));
                                $quote->setTotalsCollectedFlag(true)->collectTotals();
                                $quote->collectTotals();
                            }
                            if($this->hasValue($zresults->ZSTATUS->MESSAGE_V1)){                           
                                if($quote->hasItems()){
                                    $matnrs = explode(";", $zresults->ZSTATUS->MESSAGE_V1);//
                                    $products = '';
                                    foreach($quote->getAllVisibleItems() as $item){
                                        if(in_array($item->getSku(), $matnrs)){   //var_dump($matnrs);var_dump('Not QTY');die();                                             
                                            if ($products == '') {                                                    
                                                $products= $products.$item->getName();
                                            }else {
                                                $products= $products.$item->getName().', ';
                                            }                                                
                                            $quote->deleteItem($item);
                                            $quote->setTotalsCollectedFlag(false)->collectTotals();
                                            $quote->save();
                                            $this->messageManager->addError("Not enough stock for: ".$item->getName());
                                        }
                                    }
                                    if($products !== ''){
                                        //$this->messageManager->addError("Required quantity is not available For Product(s) ".$products);
                                    }                                        
                                }
                            }
                            if($this->hasValue($zresults->ZSTATUS->MESSAGE_V4)) {   
                                $product_repo = $this->om->create('\Magento\Catalog\Api\ProductRepositoryInterface');                             
                                $products = "";
                                $no_license = array(8,9,10);
                                foreach($quote->getAllVisibleItems() as $item){
                                    $category_ids = $product_repo->get($item->getSku())->getCategoryIds();
                                    foreach ($category_ids as $cat) {//var_dump($category_ids);var_dump('Not QTY');die();
                                        if(in_array($cat, $no_license)){
                                            if ($products == '') {                                                    
                                                $products= $products.$item->getName();
                                            }else {
                                                $products= $products.$item->getName().', ';
                                            }
                                            $quote->deleteItem($item);
                                            $quote->setTotalsCollectedFlag(false)->collectTotals();
                                            $quote->save();
                                            $this->messageManager->addError($zresults->ZSTATUS->MESSAGE_V4." ".$item->getName());
                                        }
                                    }                                            
                                }
                                if($products !== ''){
                                    //$this->messageManager->addError($zresults->ZSTATUS->MESSAGE_V4." ".$products);
                                }
                            }
                        }
                    }
                 }
            }   
        }
        
        /*if(isset($_SESSION['flowcontrol'])){
            if($_SESSION['flowcontrol'] !== $quote->getItemsCount()){
                return $quote;
            }else {
                $_SESSION['flowcontrol'] = $quote->getItemsCount();
                return $quote;
            }
        }else {
            $_SESSION['flowcontrol'] = $quote->getItemsCount();
            if($quote->getItemsCount() > 0){
                return $quote->collectTotals()->save();*/
                return $quote;//->save();
            /*}
            return $quote;
        } */      
    }
    
    public function calculatePricePerItem($price, $qty) {
		if($qty > 1) {
			  return $price / $qty;  		
		}
		return $price;
    }
    
    public function getCompanyData($customer_number){
        $companymanagement = $this->om->create('Magento\Company\Model\CompanyManagement');
        return $companymanagement->getByCustomerId($customer_number)->getData();
    }

    private function getCompanyAddress($companydata, $customerId){   
        $this->address->setRegion($companydata['region']);         
        $this->address->setCountryId($companydata['country_id']);
        $this->address->setStreet($companydata['street']); 
        $this->address->setCompany($companydata['company_name']); 
        $this->address->setTelephone($companydata['telephone']); 
        $this->address->setCity($companydata['city']);
        $this->address->setCustomerId($customerId); 
        $this->address->setIsDefaultBilling(1);
        $this->address->setIsDefaultShipping(1);
        $this->address->setSaveInAddressBook(1);
        $this->address->save();
        return $this->address;
    }

    public function simulateSalesOrder($PMatnrs,$plant ,$soldtoparty_number, $Vkorg, $Vtweg, $Spart){
        //$wsdlUrl = $this->module_path."wsdl/zsalesorder_service_group_binding.xml";
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

        //Set Parameters
        $parameters = array(
                            "P_AUART" => "ZOR",
                            "P_MATNRS" => $PMatnrs,
                            "P_PLANT" => $plant,//"0",
                            "P_SHIP" => $soldtoparty_number,
                            "P_SOLD" => $soldtoparty_number,
                            "P_SPART" => $Spart,
                            "P_VKORG" => $Vkorg,
                            "P_VTWEG" => $Vtweg
                            );

        //Call Funtion (passing in parameters)
        
        try{

            $result = $soapClient->ZSIMULATE_SALES_ORDER($parameters);
            return $result;
		         
        }
        catch (SoapFault $e){
            $this->erpOrderId = 0;
            $this->erpOrderCreated = 0;
            $this->messageManager->addErrorMessage("Soap call error".$e->getMessage());
            return null;
        }
        
    }
    
    private function urlExists($url){ 
	    $ch = curl_init($url);  
	    curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
	    $data = curl_exec($ch);  
	    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
	    curl_close($ch);
	    return $httpcode;
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
}
?>
