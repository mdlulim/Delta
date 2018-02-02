<?php
namespace Consnet\Api\Model;
//use Consnet\Api\Api\ConsnetInterface;
//use Consnet\Api\Model\createCompany;

class Api extends \Magento\Framework\Model\AbstractModel
{


  public function __construct() {
      

   }

    public function getClass() {
    
      $om = \Magento\Framework\App\ObjectManager::getInstance();
      $clas  = $om->create('\Consnet\Erporder\Controller\Adminhtml\SwitchCompany\startReplication');
      $clas->execute();
      echo 'done';
 

 }

  



}
