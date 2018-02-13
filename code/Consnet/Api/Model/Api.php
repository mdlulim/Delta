<?php
namespace Consnet\Api\Model;
//use Consnet\Api\Api\ConsnetInterface;
//use Consnet\Api\Model\createCompany;

class Api extends \Magento\Framework\Model\AbstractModel
{
 

  protected $admin_user ;
  protected $admin_pass;

  public function __construct() {
    

   }

    public function getClass($url) {

        $token  = $this->authApi();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getHelper()->getGeneralConfig('namespace_text').$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true );
        $result = curl_exec($ch);
        $result = json_decode($result, 1);
        curl_close($ch);
 }


 protected function authApi() {

  $userData = array("username" => $this->getHelper()->getGeneralConfig('admin_user') , "password" => $this->getHelper()->getGeneralConfig('admin_password') );
  $ch = curl_init(  $this->getHelper()->getGeneralConfig('namespace_text')."/index.php/rest/V1/integration/admin/token");
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));
  $this->token = curl_exec($ch);
}

public function getHelper(){
  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
  $helper = $objectManager->create('Consnet\Api\Helper\Data');
  return  $helper;
}
  



}
