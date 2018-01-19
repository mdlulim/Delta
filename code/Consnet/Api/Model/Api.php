<?php
namespace Consnet\Api\Model;
//use Consnet\Api\Api\ConsnetInterface;
//use Consnet\Api\Model\createCompany;

class Api //extends createCompany //implements ConsnetInterface
{
protected $_logger;
protected $storeManager;
protected $customerFactory;
protected $address ;
protected $customerRepository ;

  public function __construct(
       \Psr\Log\LoggerInterface $logger,
       \Magento\Framework\App\Action\Context $context,
       \Magento\Store\Model\StoreManagerInterface $storeManager,
       \Magento\Customer\Model\CustomerFactory $customerFactory,
       \Magento\Customer\Model\AddressFactory $address,
       \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
   ) {
       $this->storeManager     = $storeManager;
       $this->customerFactory  = $customerFactory;
       $this->_logger = $logger;
       $this->address = $address;
       $this->customerRepository  = $customerRepository ;
       //parent::__construct($context);
   }

    public function getDataFromErp() {
      // Get Website ID
      //Edit it according to your requirement
  $this->_logger->debug('CREATE CUSTOER HAS RAN MLA');
  $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();
  $customer   = $this->customerFactory->create();
  $customer->setWebsiteId($websiteId)->loadByEmail('email@domain.com');
  if ($customer->getId()) {
 // cusromer already exist

   // update data
   $customer->setFirstname("Emmanuel");
   $customer->setLastname("Zondo");



 }else{

   $customer->setEmail("email@domain.com");
   $customer->setFirstname("First Name");
   $customer->setLastname("Last name");
   $customer->setPassword("password");
   $customer->setData('kunnr','55501');

 }

   // Save data
   $customer->save();
   //$customer_id = $customer->getId();
   $customer->sendNewAccountEmail();



  return $this;
   }


    public function createCompany($ERPCustomers){


    }


    public function createCustomer($ERPContactPerson ,$ERPCustomers){



       //}



    }


    public function createComanyCustomerMAP($ERPCompCustMAP)
    {

    }






}
