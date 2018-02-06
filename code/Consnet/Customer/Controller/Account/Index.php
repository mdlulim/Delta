<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Consnet\Customer\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;

class Index extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    protected  $objectManager ;
    private $_connection;
    private $_resource;
    protected  $customerFactory;
    protected $customerExtensionFactory;

    protected  $companyInterfaceFactory ;

   protected  $NAMESPACE_ID  ;

    protected $_cacheTypeList;
    protected $_cacheFrontendPool;

    protected $_indexerFactory;
    /**
     * @var \Magento\Indexer\Model\Indexer\CollectionFactory
     */
    protected $_indexerCollectionFactory;

    protected $session ;

    protected $customerAccountManagement;

    protected $cart ; 

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;
    
        /**
         * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
         */
        private $cookieMetadataManager;
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\Data\CustomerExtensionFactory $customerExtensionFactory,
        \Magento\Company\Api\Data\CompanyInterfaceFactory $companyInterfaceFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory,
        AccountManagementInterface $customerAccountManagement,
        Session $customerSession,
        \Magento\Checkout\Model\Cart  $cart,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->customerFactory  = $customerFactory;
        $this->customerExtensionFactory = $customerExtensionFactory;
        $this->companyInterfaceFactory  =  $companyInterfaceFactory;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_indexerFactory = $indexerFactory;
        $this->_indexerCollectionFactory = $indexerCollectionFactory;
        $this->session = $customerSession;
        $this->cart  = $cart;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->_resources = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->_connection = $this->_resources->getConnection();
    }

    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated 100.1.0
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }

    

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {   
        $this->NAMESPACE_ID =  'deltademo';
        $id = $this->getRequest()->getParam('id');
        $switch_acc = $this->getRequest()->getParam('switch');
        if($id){

          $_SESSION['modal_on'] = false;
          $_SESSION['company'] = $id ;

          $customer = $this->objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface')->getById($_SESSION['user']);
          $company =  $this->companyInterfaceFactory->create()->load($id);
         
          $customer->setData( "group_id" , $company->getCustomerGroupId());
            
          $customerExt = $customer->getExtensionAttributes();
          $repo = $this->objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface');

              $customerExt = $this->objectManager->create('\Magento\Customer\Api\Data\CustomerExtension');
              $customerCompanyExt = $this->objectManager->create('\Magento\Company\Api\Data\CompanyCustomerInterface');
              $customerCompanyExt->setCustomerId($id);
              $customerCompanyExt->setCompanyId($company['entity_id']);
              $customerCompanyExt->setJobTitle("Contact Person");
              $customerCompanyExt->setStatus(1);
              $customerCompanyExt->setTelephone("000-000-0000");
              $customerExt->setCompanyAttributes($customerCompanyExt);
              $customer->setExtensionAttributes($customerExt);


              $repo->save($customer);



          $_SESSION['kunnr'] = $company->getData('STP_ID');
          $_SESSION['VKORG'] = $company->getData('VKORG');
          $_SESSION['ZTERM'] = $company->getData('ZTERM');

          //getCompanyDefaultRole
          $defaultRole = $this->objectManager->create('\Magento\Company\Model\RoleManagement');
          $defRole = $defaultRole->getCompanyDefaultRole($id);
  

          $role = [
            "role" => [
                "id" => $defRole->getId(),
                "role_name" => 'Test Role',
                "permissions" => [
                    ["resource_id" => "Magento_Company::index", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::all", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::place_order", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::payment_account", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::view_orders", "permission" => "allow"],
                    ["resource_id" => "Magento_Sales::view_orders_sub", "permission" => "allow"],
                    ["resource_id" => "Magento_Company::view", "permission" => "allow"],
                    ["resource_id" => "Magento_Company::view_account", "permission" => "allow"],
                    ["resource_id" => "Magento_Company::view_address", "permission" => "allow"],
                    ["resource_id" => "Magento_Company::payment_information", "permission" => "allow"],
                    
                    
                ],
                "company_id" => $id
            ]
          ];
        $url =  $this->NAMESPACE_ID . "/index.php/rest/V1/company/role/".$defRole->getId();

        $role = $this->crearObject($url, 'PUT', $role, 'X');

      

         $UserRole = $this->objectManager->create('\Magento\Company\Model\UserRoleManagement');
         //$UserRole->assignRoles($customer->getId(),[$defRole]);
         $UserRole->assignUserDefaultRole($customer->getId(),$id);
         $compMan = $this->objectManager->create('\Magento\Company\Model\CompanyManagement');
         $compMan->assignCustomer($id,$customer->getId());

         $allItems =   $this->cart->getQuote()->getAllVisibleItems();
         foreach($allItems as $item){
                $itemId =  $item->getItemId();
                 $this->cart->removeItem($itemId)->save();

         }

         $this->cart->truncate();
         $this->cart->save();
         $this->session->logout();
                
                $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
                foreach ($types as $type) {
                    $this->_cacheTypeList->cleanType($type);
                }
                foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                    $cacheFrontend->getBackend()->clean();
                }
               


                $message = __('You have are now signing in as account : '.$company->getData('STP_ID').' ,Please type your email and password to sign in');
                $this->messageManager->addSuccess($message);
        
                
                 $resultRedirect = $this->resultRedirectFactory->create();
                 $resultRedirect->setPath('customer/account/');
                return $resultRedirect;
       }

        if($switch_acc){
          $_SESSION['modal_on'] = true;
          //$_SESSION['company'] = $postParam ;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Account'));





        return $resultPage;
    

    }

    public function crearObject($url, $operation, $data, $log) {
        $_result = null;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $operation);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($this->authApi())));

        $_result = curl_exec($ch);
        $_result = json_decode($_result, 1);


        if ($log == 'X'){
            var_dump($data);
            var_dump($_result);
        }
        
        return $_result['id'];
    }
    protected function authApi() {
        $userData = array("username" => "admin", "password" => "Consnet01");
        $ch = curl_init("http://10.2.10.93/" . $this->NAMESPACE_ID . "/index.php/rest/V1/integration/admin/token");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

        return  curl_exec($ch);
    }
}
