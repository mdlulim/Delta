<?php

namespace Consnet\Api\Controller\Index;

use Magento\Framework\App\Action\Context;
use \Magento\User\Model\UserFactory;
use \Magento\User\Model\ResourceModel\User;
use Zend\Soap\Client;

class Orders extends \Magento\Framework\App\Action\Action
{

    const COMPANY_EMAIL = 'stores1@delta.co.zw';
    const CUSTOMER_EMAIL = 'users1@delta.co.zw';
    //const NAMESPACE_ID = 'deltaqa01'; 
    protected $_resultPageFactory;
    protected $_logger;
    protected $storeManager;
    protected $customerFactory;
    protected $address;
    protected $customerRepository;
    protected $companyRepository;
    protected $companyFactory;
    protected $objectManager;
    protected $cust;
    protected $cont;
    protected $bf;
    protected $addr;
    protected $companyManagement;
    protected $companyCustomerInterface;
    protected $token;
    protected $userFactory;

    protected $products;

    //constants
    protected $userResourceModel;
    private $_connection;
    private $_resource;

    protected $next_batch_min  ;

    protected $username ;
    protected $password ;
    protected $size;
    protected $init_repl ;
    protected $NAMESPACE_ID;
        
    protected $soapClient2 ;
    //protected $appState ;

    protected $helper;

    /**
     * @param \Magento\Company\Api\Data\CompanyInterfaceFactory $companyFactory
     */
    public function __construct(
        Context $context,
        UserFactory $userFactory,
        User $userResourceModel,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Company\Api\Data\CompanyInterfaceFactory $companyFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Company\Api\Data\CompanyCustomerInterface $companyCustomerInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $address,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        \Magento\Company\Model\CompanyRepository $companyRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $appState
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->_logger = $logger;
        $this->address = $address;
        $this->customerRepository = $customerRepository;
        $this->companyRepository = $companyRepository;
        $this->objectManager = $objectManager;
        $this->companyFactory = $companyFactory;
        $this->companyCustomerInterface = $companyCustomerInterface;
        $this->_resources = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->_connection = $this->_resources->getConnection();
        $this->userFactory = $userFactory;
        $this->userResourceModel = $userResourceModel;
                
        $this->helper = $this->objectManager->create('Consnet\Api\Helper\Data');
        $WURL = $this->helper->getGeneralConfig('replication_text');

        $this->init_repl = $this->helper->getGeneralConfig('sales_order_text');
        $this->username  = $this->helper->getGeneralConfig('user_name');
        $this->password  = $this->helper->getGeneralConfig('password');
        
        $this->NAMESPACE_ID = $this->helper->getGeneralConfig('namespace_text');

        $this->authApi();

        parent::__construct($context);
    }

    protected function authApi()
    {
        $userData = array("username" => "admin", "password" => "Consnet01");
        $ch = curl_init("http://".$this->$NAMESPACE_ID. "/index.php/rest/V1/integration/admin/token");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

        $this->token = curl_exec($ch);
        $this->getOrders();
    }

    public function execute()
    {

        /*$resultPage = $this->_resultPageFactory->create();
        return $resultPage;*/
    }

    public function crearObject($url, $operation, $data, $log)
    {
        $_result = null ;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $operation);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($this->token)));
        while($_result == null)
        {
            $_result = curl_exec($ch);
            $_result = json_decode($_result, 1);
        }
        return $_result;
    }

    protected function getOrders(){
        $ch = curl_init("http://" . $this->NAMESPACE_ID . "/index.php/rest/V1/orders?searchCriteria"); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($this->token))); 
        $result = curl_exec($ch); 
        $result = json_decode($result, 1); 
        //var_dump($result['items'][0]);
        $this->updateOrders($result['items']);
        //echo '<pre>';
        //var_dump($result['items']);
        //print_r($result);
    }

    protected function updateOrders($orders){
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $orderFactory = $om->create('\Magento\Sales\Model\OrderFactory');
        
        foreach ($orders as $order) {
            
            $ex_order = null;
            $order_id = null;
            $order_id = $order['increment_id'];
            $ecc_status = $this->get_ecc_order_status($order_id);
            $ex_order = $om->create('\Magento\Sales\Model\Order')->load($order['entity_id']);
            if(is_int($ecc_status) && $ecc_status == 0){
                //Create Order In ECC
                $erp_order = $om->create('\Consnet\Erporder\Plugin\ErpOrder');
                $erp_order->afterPlace($ex_order);

                $ecc_status = $this->get_ecc_order_status($ex_order->getRealOrderId());
                $ex_order = $om->create('\Magento\Sales\Model\Order')->load($ex_order->getRealOrderId());
            }
            $ex_order = $om->create('\Magento\Sales\Model\Order')->load($order['entity_id']);
            $ex_order->setStatus($ecc_status);
            $ex_order->setState($ecc_status);
            $ex_order->save();
        }
        echo ' Done Now';
    }

    protected function get_ecc_order_number($magentoId){
        //Custom ERP Order Number
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $resources = $om->get('Magento\Framework\App\ResourceConnection');
        $connection = $resources->getConnection();
        
        $query = "SELECT `erpOrderId` FROM `erp_magento` WHERE `magOrderId` = '".$magentoId."'";
        $ecc_id = $connection->fetchRow($query)['erpOrderId'];
        return  $ecc_id;
    }

    protected function get_ecc_order_status($order_number){       
        $WURL = $this->helper->getGeneralConfig('sales_order_text');
        
		//Set SOAP Options
        $options = array(
                            "soap_version" => SOAP_1_2,
                            "cache_wsdl" => WSDL_CACHE_NONE       							
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
                            return 'complete';
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
                        return 'complete';
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
            return 0; 
        }
    }

    protected function setOrderStatus($order_id, $status){
        $order_data = "{ 
                            'entity': 
                            { 
                                'id':".$order_id.", 
                                'status': ".$status." 
                            } 
                        }";
        
        $ch = curl_init("http://" . $this->NAMESPACE_ID . "/index.php/rest/V1/orders");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($this->token)));
        $result = curl_exec($ch); 
        $result = json_decode($result, 1); 
        return $result;
    }
}
