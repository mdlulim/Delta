<?php
namespace Consnet\Api\Model;


class createCompany
{


   /**
    * @var \Magento\Customer\Model\CustomerFactory
    */
   protected $company;





  public function createCompnay($data)
    {
        // Instantiate object (this is the most important part)
        $customer   = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);

        // Preparing data for new customer
        $customer->setEmail("email@domain.com");
        $customer->setFirstname("First Name");
        $customer->setLastname("Last name");
        $customer->setPassword("password");

        // Save data
        $customer->save();
        $customer->sendNewAccountEmail();
    }
}
