<?php
namespace Consnet\SapConnection\Helper;

use Zend\Soap\Client;

class ERP
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const RFC_USER = 'tmahihlaba';
    const RFC_PASSWORD = 'COMTIA@7';
    
    private $_resources;
    private $_om;
    private $_module_path;
    private $_connection;

    public function __construct(){
        $this->om = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_resources = $this->om->get('Magento\Framework\App\ResourceConnection'); 
        $this->_connection = $this->_resources->getConnection();
        $this->module_path = dirname(__FILE__)."/";
    }

	function ecc_call($wsdlUrl, $function, $options = null){
		//Set SOAP Options
        $options = array(
            "soap_version" => SOAP_1_2       							
        );		

        //Creat SOAP client instance
        $soapClient  = new Client($this->module_path.$wsdlUrl, $options);

        //Set Login details
        $soapClient->setHttpLogin(RFC_USER);
        $soapClient->setHttpPassword(RFC_PASSWORD);

        //Call Funtion (passing in parameters)
        try{
            $result = $soapClient->call($function)($parameters);
        return $result;
        }
        catch (SoapFault $e){
            return $e->getMessage();
        }
        catch (Exception $e){
            return $e->getMessage();
        } 
	}
}
   