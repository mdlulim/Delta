<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Consnet\Contact\Block;

use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 */



class ContactForm extends \Magento\Contact\Block\ContactForm   //Template
{
    private $_connection;
    private $_resource;
    private $_om;
    private $CustomerFactory;
    protected $companyManagement;
    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context,
                                \Magento\Customer\Model\CustomerFactory $CustomerFactory ,
                                \Magento\Company\Api\CompanyManagementInterface $companyManagement,
                                array $data = [])
    {

        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->CustomerFactory  = $CustomerFactory;
        $this->companyManagement = $companyManagement;
    }

    /**
     * Returns action url for contact form
     *
     * @return string
     */
    public function getFormAction()
    {

        return $this->getUrl('contact/index/post', ['_secure' => true]);
    }
    protected function getConnection()
    {

        $this->_om = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_resources = $this->_om->get('Magento\Framework\App\ResourceConnection');
        $this->_connection = $this->_resources->getConnection();

    }

    public function getAboutTypes()
    {
        $result = array();
        $this->getConnection();
        $about = $this->_connection->fetchAll('SELECT * FROM contact_about');
        $decr1 = $this->_connection->fetchAll('SELECT * FROM contact_about_decr_1');

        $result[] = $about;
        $result[] = $decr1;
        return $result;


    }
    public function getHeaderText(){
        return 'Send us a note';
    }

    public function getTickets(){

        $customer = $this->CustomerFactory->create()->load($_SESSION['user']);
        $company  = $this->companyManagement->getByCustomerId($customer->getId());

        
       

       $crm_data  =  [] ;
       $this->getConnection();
       $tickets = $this->_connection->fetchAll("SELECT * FROM crm_service_requests WHERE customer ='".$company->getData('STP_ID')."'");


            // /$wsdlUrl = dirname(__FILE__)."/wsdl/zzget_serv_req_details_v1_binding.xml";
             $wsdlUrl = $this->getHelper()->getGeneralConfig('crm_service_text');
            
            //Start Remove
            $soapClient  = new Client($wsdlUrl,array("soap_version" => SOAP_1_2));

            //Set Login details
            $soapClient->setHttpLogin($this->getHelper()->getGeneralConfig('user_name'));
            $soapClient->setHttpPassword($this->getHelper()->getGeneralConfig('password'));
            

            foreach ($tickets as $value) {
                
            
                //Set Parameters
            $parameters = array(
                            "RequestId" => $value['request_id']///"0000000003"//
                            );        
            
            $result      = $soapClient->ZzgetServReqDetails($parameters);
  

             $orderHeader  = $result->OrderHeader;
             $OrderText    =  $result->OrderText;
             //$textLines    =  $OrderText->Lines;
             $status       = $result->OrderStatus ;
             //array_push($crm, $orderHeader );
             //array_push($crm, $OrderText );
             $TicketObject = new \stdClass() ;
             $TicketObject->header = $orderHeader ;
             $TicketObject->text   = $OrderText ;
             $TicketObject->status = $status  ;

            // return $TicketObject;
             array_push($crm_data,$TicketObject);

             //foreach ($status->item as $value) {
             //    $value->Txt30;
            
                  
            // }
            
             
            }

            return $crm_data;

    }

    public function getHelper(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('Consnet\Api\Helper\Data');
        return  $helper;
    }
}
