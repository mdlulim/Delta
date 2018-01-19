<?php
namespace Consnet\Customer\Block;

class SwitchAccount extends \Magento\Framework\View\Element\Template
{


  protected $registry ;

  public function __construct(
      \Magento\Framework\View\Element\Template\Context $context,
      \Magento\Customer\Model\Session $customerSession,
      \Magento\Customer\Model\Url $customerUrl,
      array $data = [] ,
      \Magento\Framework\Registry $registry
  ) {
      parent::__construct($context, $data);
      $this->_isScopePrivate = false;
      $this->_customerUrl = $customerUrl;
      $this->_customerSession = $customerSession;
      $this->registry = $registry;
  }



    public function getHelloWorldTxt()
    {
        return 'Hello world!';
    }

}
