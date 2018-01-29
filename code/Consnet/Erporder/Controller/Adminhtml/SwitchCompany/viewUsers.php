<?php
  namespace Consnet\ErpOrder\Controller\Adminhtml\SwitchCompany;

  class viewUsers extends \Magento\Backend\App\Action
  {
    /**
    * @var \Magento\Framework\View\Result\PageFactory
    */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
          parent::__construct($context);
          $this->resultPageFactory = $resultPageFactory;
    } 

    /**
     * Load the page defined in view/adminhtml/layout/exampleadminnewpage_helloworld_index.xml
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {   
       $company_id =  $this->getRequest()->getParam('id');
       

       $_SESSION['company_id'] = $company_id;
       $_SESSION['from'] = 'company';

       $resultRedirect = $this->resultRedirectFactory->create();
       $resultRedirect->setPath('customer/index/');
      return $resultRedirect;
    }
  }
?>
