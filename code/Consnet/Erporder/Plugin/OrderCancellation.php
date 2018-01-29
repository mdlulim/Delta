<?php
namespace Consnet\Erporder\Plugin;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Zend\Soap\Client;

class OrderCancellation
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

	function afterCancel($order){
        //function beforeCancel($id){
        //if($id){
            //Cancel In ERP
            //Consnet\SapConnection\Helper\ERP            
            //$orderFactory = $this->om->get('\Magento\Sales\Model\OrderFactory');
            //var_dump($id);
            //$order = $orderFactory->create()->load($id);
            //echo($order->getRealOrderId());
            
            
            
            
            //var_dump(get_class_methods($order));
            if(method_exists($order, 'getRealOrderId')){
                $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);//STATE_COMPLETE);
                $ecc_doc = $this->get_ecc_order_number($order->getRealOrderId());
                $this->reject_ecc_salesorder($ecc_doc);
                $order->save();
            }
            
        //}
        return null;
    }

    public function reject_ecc_salesorder($ecc_doc){
        //$wsdlUrl = dirname(__FILE__)."/zsalesorder_function_group_binding.xml";
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $om->get('Consnet\Api\Helper\Data');
        $WURL = $helper->getGeneralConfig('sales_order_text');
        
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
                        "P_DOC_NO" => $ecc_doc
                        );

        //Call Funtion (passing in parameters)
        try{

            $result = $soapClient->ZCANCEL_SALES_ORDER($parameters);
            return $result;
            
        }
        catch (SoapFault $e){
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
   