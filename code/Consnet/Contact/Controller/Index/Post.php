<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Consnet\Contact\Controller\Index;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Zend\Soap\Client;

class Post extends \Magento\Contact\Controller\Index
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;
    private $module_path;

    /**
     * Post user question
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        
        if (!$post) {
            $this->_redirect('*/*/');
            return;
        }
     
        


    
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($post);
            
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerSession = $objectManager->create('Magento\Customer\Model\Customer')->load($_SESSION['user']);


            $contact = $customerSession->getData('CONTACT_ID');
          
		    $text_contact_us = 'From Magento Contact us'; 
            $_title = $post['text_about'].' > '.$post['text_desc'];          
            
            $code1 =   substr($post['s_decr1'],4,4) ;
            $codegroup1 = substr($post['s_decr1'],0,4);       
            $code2 =   substr($post['s_about'],4,4) ;
            $codegroup2 = substr($post['s_about'],0,4);
          
            $parameters1['PCodea'] = '';
            $parameters1['PCodeb'] = '';
    		    $parameters1['PCodegroupa'] = '';
    		    $parameters1['PCodegroupb'] = '';
         
            $parameters1 = array( "PCodea" => $code1 ,
           "PCodegroupa1" => $codegroup1 ,
           "PCodeb" => $code2 , 
           "PCodegroupb" => $codegroup2 ,
           "PDescription" => $post['comment'].'%XX50000764',//%0000001005', 
           "PContactPerson" => '',//$contact , 
           "PTextFrom" => 'Magento Contact us',
           "PPartner" => $post['stp_id']
            ); 
          
          //var_dump(//$parameters1);
            
            $this->module_path = "app/code/Consnet/Contact/";
            $wsdlUrl = $this->module_path."wsdl/QA_zcreate_serv_reqv2_bind.xml";
            
				//Start Remove
				   $soapClient  = new Client($wsdlUrl,array("soap_version" => SOAP_1_2));

        		//Set Login details
        		$soapClient->setHttpLogin('magentorfc');
        		$soapClient->setHttpPassword('Consnet02');
        		
        		
        	  $result = $soapClient->Zmagentoservreqcreatev1($parameters1);
              $_reference = $result->TId  ;

             if($_reference !=''){


            $this->saveRequestLocaly($_reference ,  $_title , $post['stp_id'] );

            

            

            $_SESSION['Type'] = 'success' ;
            $_SESSION['Message'] = 'Thanks for contacting us , your Reference number is  : '.$_reference;
           // $this->messageManager->addSuccess(
           //     __('Thanks for contacting us with your query , your Reference number is : '.$_reference )
           // );
            $_reference = '';
            $this->getDataPersistor()->clear('contact_us');
            $this->_redirect('contact/');
            return;
            }
        } catch (SoapFault $e) {
        

            $_SESSION['Type'] = 'error' ;
            $_SESSION['Message'] = 'We can\'t process your request right now. Sorry, that\'s all we know.'.$e->getMessage() ;

            //$this->messageManager->addError(
            //    __('We can\'t process your request right now. Sorry, that\'s all we know.'.$e->getMessage())
            //);
            $this->getDataPersistor()->set('contact_us', $post);
            $this->_redirect('contact/');
            return;
        }
    }
    
    public function get_ecc_data($wsdlUrl, $function, $parameters ){
        //Creat SOAP client instance
        $soapClient  = new Client($wsdlUrl,array("soap_version" => SOAP_1_2));

        //Set Login details
        $soapClient->setHttpLogin('magentorfc');
        $soapClient->setHttpPassword('Consnet01');

        //Set Parameters
       

        //Call Funtion (Can pass in parameters)
        return $soapClient->__call($function, $parameters)->TId;
    }   
    

    /**
     * Get Data Persistor
     *
     * @return DataPersistorInterface
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()
                ->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }

    public function saveRequestLocaly($_request , $title , $customer){
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $resources = $om->get('Magento\Framework\App\ResourceConnection');
        $connection = $resources->getConnection();

        $sql = 'INSERT INTO `crm_service_requests`( `customer`, `request_id`, `request_title` , `createDate` ) VALUES ("'.$_SESSION['kunnr'].'","'.$_request.'","'.$title.'","'.date("d.m.Y").'")';
        $connection->query($sql);
        $sql = '';

    }
}
