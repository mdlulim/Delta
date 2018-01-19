<?php
namespace Consnet\Erporder\Plugin;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Zend\Soap\Client; 

class FrontendOrderSimulator
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

    public function __construct(MessageManager $messageManager){
        $this->messageManager = $messageManager;
        $this->om = \Magento\Framework\App\ObjectManager::getInstance();
        $this->customerSession = $this->om->get('Magento\Customer\Model\Session');
        $this->_resources = $this->om->get('Magento\Framework\App\ResourceConnection');
        $this->_connection = $this->_resources->getConnection();
        $this->module_path = dirname(__FILE__)."/";
    }

    public function UpdateQuote($result){
        $this->magOrderId = $result->getRealOrderId();
        $quote = $result;
        $this->magCustomerId = $result->getCustomerId();

        $customer_model = $this->om->create('Magento\Customer\Model\Customer');
        $customer_model->setWebsiteId(1);
        $customer = $customer_model->load($this->magCustomerId);
        $customer_data = $customer->getData();
		          		
		$status = $this->checkECCStatus();
        $allowedResponses = array(403, 404);
        if(!in_array($status, $allowedResponses)){
          $this->messageManager->addSuccessMessage("Soap call result: ".$status);
          return $quote;
        }

        if(in_array('Id', array_keys($customer_data))){
           if(array_key_exists('Id', $customer_data)){
        	  	  
        	$customerId = str_pad($customerdata['Id'], 10, '0', STR_PAD_LEFT);
            $company_data = $this->getCompanyData($customerId);
            
            $orderItems = $quote->getAllVisibleItems();
            
        	$PMatnrs = "";
            foreach ($orderItems as $item){
                $PMatnrs .= $item->getSku().":".$item->getQty().";";
            }
        	  if($this->hasValue($PMatnrs)){
                $zresults = $this->simulateSalesOrder($PMatnrs, 
        		            		                  $customer_data['stp_id'],
        		                  		              $company_data["VKORG"],
        		                        		      $company_data["VTWEG"],
        		                              		  $company_data["SPART"]);

                    if($this->hasValue($zresults->Zstatus->MessageV1)){
                            if($quote->hasItems()){
                               foreach($quote->getAllVisibleItems() as $item){
                                   if ($zresults->Zstatus->MessageV1 === $item->getSku()) {
                                       $this->messageManager->addError($zresults->Zstatus->Message);
                                       $quote->removeItem($item->getId())->save();
                                       return $quote;
                                   }
                               }
                           }
                    }elseif($zresults !== null) {
							foreach($quote->getAllVisibleItems() as $item){
								$price_per_item =  null;
			        			if(is_array($zresults->Zresults->item)){
			        				for($i = 0; $i < count($zresults->Zresults->item); $i++) {
				        				if($item->getSku() === $zresults->Zresults->item[$i]->Material) {	
											if($item->getPrice() !== $zresults->Zresults->item[$i]->NetValue1){
												$price_per_item = $this->calculatePricePerItem($zresults->Zresults->item[$i]->NetValue1, $item->getQty());
												$item->setPrice($price_per_item);		
												$item->setCustomPrice($price_per_item);
												$item->setOriginalCustomPrice($price_per_item);
									      	    $item->getProduct()->setIsSuperMode(true);
											}
										}
									}
			        			}
			        			else {
			        				if($item->getSku() === $zresults->Zresults->item->Material) {	
										if($item->getPrice() !== $zresults->Zresults->item->NetValue1){
											$price_per_item = $this->calculatePricePerItem($zresults->Zresults->item->NetValue1, $item->getQty());										
											$item->setPrice($price_per_item);		
											$item->setCustomPrice($price_per_item);
											$item->setOriginalCustomPrice($price_per_item);
									        $item->getProduct()->setIsSuperMode(true);
											$item->save();
										}
									}
			        			}
							}
						   $quote->setTotalsCollectedFlag(true)->collectTotals();
						}
					}
	     	  }
        }   
        return $quote;
    }

    private function checkECCStatus(){
        $wsdlUrl = $this->module_path."wsdl/zsalesorder_srvs_binding.xml";
        $options = array(
                            "soap_version" => SOAP_1_2                                  
                        );
        $soapClient  = new Client($wsdlUrl, $options);
          
        return $this->urlExists("deltadev01.delta.co.zw");
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

    public function simulateSalesOrder($PMatnrs, $soldtoparty_number, $Vkorg, $Vtweg, $Spart){
        $wsdlUrl = $this->module_path."wsdl/zsalesorder_srvs_binding.xml";
		
		  //Set SOAP Options
        $options = array(
							"soap_version" => SOAP_1_2       							
						);		
		
        //Creat SOAP client instance
        $soapClient  = new Client($wsdlUrl, $options);

        //Set Login details
        $soapClient->setHttpLogin('tmahihlaba');
        $soapClient->setHttpPassword('COMTIA@7');

        //Set Parameters
        $parameters = array(
                            "PAuart" => "ZOR",
                            "PMatnrs" => $PMatnrs,
                            "PPlant" => "0",
                            "PShip" => $soldtoparty_number,
                            "PSold" => $soldtoparty_number,
                            "PSpart" => $Spart,
                            "PVkorg" => $Vkorg,
                            "PVtweg" => $Vtweg
                            );

        //Call Funtion (passing in parameters)
        try{
		         $result = $soapClient->ZsimulateSalesOrder($parameters);
                 //var_dump($result);
                 return $result;
        }
        catch (SoapFault $e){
            $this->erpOrderId = 0;
            $this->erpOrderCreated = 0;
            $this->messageManager->addSuccessMessage("Soap call error".$e->getMessage());
            return null;
        }
        catch (Exception $e){
            $this->erpOrderId = 0;
            $this->erpOrderCreated = 0;
            $this->messageManager->addSuccessMessage("Soap call error".$e->getMessage());
            return null;
        }   
    }
    
    public function urlExists($url){  
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
