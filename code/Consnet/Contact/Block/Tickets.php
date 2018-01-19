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



class Tickets extends \Magento\Framework\View\Element\Template  //Template
{
    private $_connection;
    private $_resource;
    private $_om;
    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context,
                                array $data = [])
    {

        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
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
       /*  $result = array();
        $this->getConnection();
        $about = $this->_connection->fetchAll('SELECT * FROM contact_about');
        $decr1 = $this->_connection->fetchAll('SELECT * FROM contact_about_decr_1');

        $result[] = $about;
        $result[] = $decr1; */
        return [];// $result;


    }
    public function getHeaderText(){
        return 'Send us a note';
    }

    public function getTickets(){

     $crm_data  =  [] ;
       $this->getConnection();
       $tickets = $this->_connection->fetchAll('SELECT * FROM crm_service_requests');





            $wsdlUrl = dirname(__FILE__)."/wsdl/zzget_serv_req_details_v1_binding.xml";
            $soapClient  = new \Zend\Soap\Client($wsdlUrl,array("soap_version" => SOAP_1_2));

            //Set Login details
            $soapClient->setHttpLogin('magentorfc');
            $soapClient->setHttpPassword('Consnet01');


            foreach ($tickets as $value) {


                //Set Parameters
            $parameters = array(
                            "RequestId" => $value['request_id']///"0000000003"//
                            );

            $result      = $soapClient->ZzgetServReqDetails($parameters);


             $orderHeader  = $result->OrderHeader;
             $OrderText    =  $result->OrderText;
             $status       = $result->OrderStatus ;

             $TicketObject = new \stdClass() ;
             $TicketObject->header = $orderHeader ;
             $TicketObject->text   = $OrderText ;
             $TicketObject->status = $status  ;

             array_push($crm_data,$TicketObject);

        
          }

            return $crm_data;

    }
}
