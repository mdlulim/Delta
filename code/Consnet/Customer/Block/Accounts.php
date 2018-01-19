<?php
namespace Consnet\Customer\Block;

class Accounts extends \Magento\Framework\View\Element\Template
{


  protected $registry ;
  protected $companyFactory;
  private $CustomerFactory;

  public function __construct(
      \Magento\Framework\View\Element\Template\Context $context,
      \Magento\Customer\Model\Session $customerSession,
      \Magento\Customer\Model\Url $customerUrl,
      \Magento\Customer\Model\CustomerFactory $CustomerFactory ,
      \Magento\Company\Model\CompanyFactory $companyFactory,
      array $data = [] ,
      \Magento\Framework\Registry $registry
  ) {
      parent::__construct($context, $data);
      $this->_isScopePrivate = false;
      $this->_customerUrl = $customerUrl;
      $this->_customerSession = $customerSession;
      $this->CustomerFactory  = $CustomerFactory;
      $this->companyManagement  = $companyManagement ;
      $this->registry = $registry;
  }



    public function getHelloWorldTxt()
    {
        return 'Hello world!';
    }

    public  function getRegValue()
    {

      if (isset($_SESSION['modal_on'])) {

         return $_SESSION['modal_on'];
       }else{

         return false;
       }




    }

    public  function getLinkedAccounts()
    {

      //$this->reg->registry('modal_on' true);
      //$this->reg->registry('user' $customer->getId());
      $out = array();
      foreach($_SESSION['accounts'] as $accounts )
      {
        $company  = $this->companyFactory->create()->load($accounts['company_id']);
        
        $item['id'] = $company->getId();
        $item['name'] = $company->getCompanyName();
        $item['kunnr'] = $company->getData('STP_ID');

        array_push($out,$item);
      }

      var_dump($out);
      die();
        return $out ;
    }

    
}
