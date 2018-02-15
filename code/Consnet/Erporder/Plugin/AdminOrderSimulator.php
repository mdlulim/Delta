<?php
namespace Consnet\Erporder\Plugin;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Zend\Soap\Client;
use SoapFault;

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
            
            $company_data = $this->getCompanyData($this->magCustomerId);

            $addressInfo= $this->setAddressInformation($customer_data, $company_data);
           
            $quote->getBillingAddress()->addData($addressInfo['shipping_address']);
            $quote->getShippingAddress()->addData($addressInfo['shipping_address']);
            
              if(in_array('STP_ID', array_keys($company_data))){
                    if(array_key_exists('STP_ID', $company_data)){
                    
                        if(preg_match("/[a-z]/i", $company_data['STP_ID'])){
                            $stp = $company_data['STP_ID'];
                        }else{
                            $stp = str_pad($company_data['STP_ID'], 10, '0', STR_PAD_LEFT);
                        }
                    
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
                                                    $total = $total + $zresults->ZRESULTS->item[$i]->NET_VALUE1;
                                                    
                                                    $this->showItemMessage($item->getName(), $item->getSku(), 
                                                    $item->getQty(), 'success');
                                                }
                                            }
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
                                                
                                                $totalTax = $totalTax + $zresults->ZRESULTS->item->TX_DOC_CUR;
                                                $total = $total + $zresults->ZRESULTS->item->NET_VALUE1;
                                                
                                                $this->showItemMessage($item->getName(), $item->getSku(), 
                                                $item->getQty(), 'success');
                                            }
                                        }
                                    }
                                }                              
                                $quote->setTaxAmount($totalTax);
                                $quote->setSubtotal($total);
                                $quote->setGrandTotal(($total + $totalTax));
                                if(method_exists($quote->setTotalsCollectedFlag(true), 'collectTotals')){
                                    $quote->setTotalsCollectedFlag(true)->collectTotals();
                                    $quote->collectTotals();
                                }                                
                            }
                            if($this->hasValue($zresults->ZSTATUS->MESSAGE_V1)){                           
                                $this->stockCheck($quote, $zresults);
                            }
                            if($this->hasValue($zresults->ZSTATUS->MESSAGE_V4)) {   
                                $this->licenseCheck($quote);
                            }
                            if($this->hasValue($zresults->ZSTATUS->MESSAGE_V2)){
                                $this->notAllowedCheck($quote);
                            }
                        }else{
                            //return $this->OfflineECCPricing($quote);
                            $totalTax = 0;
                            $total = 0;		        
                            foreach($quote->getAllVisibleItems() as $item){
                                $price_per_item =  null;
                                $subtotal = array();
                                $price_per_item = 0;
                                $item->setPrice($price_per_item);		
                                $item->setCustomPrice($price_per_item);
                                $item->setOriginalCustomPrice($price_per_item);
                                $item->setRowTotal(0);
                                $item->getProduct()->setIsSuperMode(true);
                    
                                $totalTax = 0; 
                                $total = 0;
                                
                                $this->showItemMessage($item->getName(), $item->getSku(), 
                                $item->getQty(), 'success');            
                            }
                            $quote->setTaxAmount($totalTax);
                            $quote->setSubtotal($total);
                            $quote->setGrandTotal(($total + $totalTax));
                            if(method_exists($quote->setTotalsCollectedFlag(true), 'collectTotals')){
                                $quote->setTotalsCollectedFlag(true)->collectTotals();
                                $quote->collectTotals();
                            }
                    
                            return $quote;
                        }
                    }
                 }
            }   
        }return $quote;   
    }

    public function stockCheck($quote, $zresults){
        if($quote->hasItems()){
            $matnrs = explode(";", $zresults->ZSTATUS->MESSAGE_V1);
            $count = count($matnrs);
            if(count($matnrs) > 0){
                $count = count($matnrs) - 1;
            }
            
            foreach($quote->getAllVisibleItems() as $item){
                if(in_array($item->getSku(), $matnrs)){                                               
                    $quote->deleteItem($item);
                    $quote->setTotalsCollectedFlag(false)->collectTotals();
                    $quote->save();
                    $this->showItemMessage($item->getName(), $item->getSku(), 
                    $item->getQty(), 'stock');                                        
                }
            }                                      
        }
    }

    public function notAllowedCheck($quote, $zresults){
        if($quote->hasItems()){
            $matnr = $zresults->ZSTATUS->MESSAGE_V2;
            
            foreach($quote->getAllVisibleItems() as $item){
                if($item->getSku() == $matnr){                                               
                    $quote->deleteItem($item);
                    $quote->setTotalsCollectedFlag(false)->collectTotals();
                    $quote->save();
                    $this->showItemMessage($item->getName(), $item->getSku(), 
                    $item->getQty(), 'notAllowed');                                        
                }
            }                                      
        }
    }

    public function licenseCheck($quote){
        $product_repo = $this->om->create('\Magento\Catalog\Api\ProductRepositoryInterface');
        $no_license = array(8,9,10);
        foreach($quote->getAllVisibleItems() as $item){
            $category_ids = $product_repo->get($item->getSku())->getCategoryIds();
            foreach ($category_ids as $cat) {
                if(in_array($cat, $no_license)){
                    $quote->deleteItem($item);
                    $quote->setTotalsCollectedFlag(false)->collectTotals();
                    $quote->save();
                    $this->showItemMessage($item->getName(), $item->getSku(), 
                    $item->getQty(), 'licence');
                }
            }                                            
        }
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
        }catch (SoapFault $e){
            $this->erpOrderId = 0;
            $this->erpOrderCreated = 0;
            //$this->messageManager->addErrorMessage("Soap call error".$e->getMessage());
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
    
    public function hasValue($string){
        if(!isset($string) || $string == null){
            return false;
        }
        else{
            return true;
        }
    }
    
    public function showItemMessage($itemName, $itemSku, $itemQty, $messageType){
        if(isset($_SESSION['items'])){
            //foreach ($_SESSION['items'] as $sessionItem) {
                if($this->checkItemAddPreviously($itemSku)){    
                    $sessionItem = $this->getSessionItem($itemSku);
                    if($sessionItem['itemQty'] !== $itemQty){
                        $this->addMessage($itemName, $messageType);
                    }
                }else{
                    array_push($_SESSION['items'], array('itemSku' => $itemSku,
                    'itemName' => $itemName, 
                    'itemQty' => $itemQty));
                    $this->addMessage($itemName, $messageType);
                }
            //}
        }else {
            $_SESSION['items'] = array(
                array('itemSku' => $itemSku,
                      'itemName' => $itemName, 
                      'itemQty' => $itemQty)
            );
            $this->addMessage($itemName, $messageType);
        }
    }

    public function checkItemAddPreviously($itemSku){
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $state =  $om->get('Magento\Framework\App\State');

        if('adminhtml' != $state->getAreaCode()){
            foreach ($_SESSION['items'] as $sessionItem) {
                if($itemSku == $sessionItem['itemSku']){
                    return 1;
                }
            } 
        }         
        return 0;
    }

    public function addMessage($itemName, $messageType){
        if($messageType == 'stock'){
            $this->messageManager->addError("Stock for ".$itemName." is not available");
        }elseif($messageType == 'success'){
            $this->messageManager->addSuccessMessage('You added '.$itemName.
            ' to your shopping cart.');
        }elseif($messageType == 'licence'){
            $this->messageManager->addError("Youre Liquor Licence Is Expired For Product: ".$itemName);
        }elseif($messageType == 'notAllowed'){
            $this->messageManager->addError("Youre Not Allowed To Add Product: ".$itemName);
        }
    }

    public function getSessionItem($itemSku){
        foreach ($_SESSION['items'] as $sessionItem) {
            if($itemSku == $sessionItem['itemSku']){
                return $sessionItem;
            }
        }
    }

    public function setAddressInformation($customer_data, $company_data){
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
       return $addressInfo;
    }

    public function OfflineECCPricing($quote){
        $totalTax = 0;
        $total = 0;		        
        foreach($quote->getAllVisibleItems() as $item){
            $price_per_item =  null;
            $subtotal = array();
            $price_per_item = 0;
            $item->setPrice($price_per_item);		
            $item->setCustomPrice($price_per_item);
            $item->setOriginalCustomPrice($price_per_item);
            $item->setRowTotal(0);
            $item->getProduct()->setIsSuperMode(true);

            $totalTax = 0; 
            $total = 0;
            
            $this->showItemMessage($item->getName(), $item->getSku(), 
            $item->getQty(), 'success');            
        }
        $quote->setTaxAmount($totalTax);
        $quote->setSubtotal($total);
        $quote->setGrandTotal(($total + $totalTax));
        if(method_exists($quote->setTotalsCollectedFlag(true), 'collectTotals')){
            $quote->setTotalsCollectedFlag(true)->collectTotals();
            $quote->collectTotals();
        }

        return $quote;
    }
}
?>
