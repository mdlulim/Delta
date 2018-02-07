<?php
namespace Consnet\Api\Model;
//use Consnet\Api\Api\ConsnetInterface;
//use Consnet\Api\Model\createCompany;

class Api extends \Magento\Framework\Model\AbstractModel
{


  public function __construct() {
      



    
   }

    public function getClass($url) {

        $token  = $this->authApi();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getNamespace().$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true );
        $result = curl_exec($ch);
        $result = json_decode($result, 1);
        curl_close($ch);
 }


 protected function authApi() {
  $userData = array("username" => "admin", "password" => "Consnet01");
  $ch = curl_init( $this->getNamespace()."/index.php/rest/V1/integration/admin/token");
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));
  $this->token = curl_exec($ch);
}

public function getNamespace(){
  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
  $helper = $objectManager->create('Consnet\Api\Helper\Data');
  return  $helper->getGeneralConfig('namespace_text');
}
  



}
