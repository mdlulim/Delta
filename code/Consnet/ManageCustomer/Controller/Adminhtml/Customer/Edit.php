<?php
use \Magento\Backend\Model\View\Result\RedirectFactory;
/**
 * Mageplaza_HelloWorld extension
 */
namespace Consnet\ManageCustomer\Controller\Adminhtml\Customer;

class Edit extends \Consnet\ManageCustomer\Controller\Adminhtml\Customer
{
    protected $_om;
    /**
     * Backend session
     * 
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * Page factory
     * 
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    protected $_customerFactory;

    /**
     * Result JSON factory
     * 
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * constructor
     * 
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Consnet\ManageCustomer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
     
    )
    {
        $this->om = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_resultPageFactory = $this->om->get('\Magento\Backend\Model\View\Result\RedirectFactory');
        $this->_customerFactory = $this->om->get('\Consnet\ManageCustomer\Model\CustomerFactory');
        $this->_resultPageFactory= $this->om->get('\Magento\Framework\View\Result\PageFactory');
        $this->_registry= $this->om->get('\Magento\Framework\Registry');
        $this->_context= $this->om->get('\Magento\Backend\App\Action\Context');

        parent::__construct($this->_customerFactory, $this->_registry, $this->_resultPageFactory, $this->_context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Consnet_ManageCustomer::customer');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        /** @var \Mageplaza\HelloWorld\Model\Customer $customer */
        $customer = $this->_initCustomer();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        
        
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Consnet_ManageCustomer::customer');
        $resultPage->getConfig()->getTitle()->set(__('Customers'));
        if ($id) {
            $customer->load($id);
            if (!$customer->getId()) {
                $this->messageManager->addError(__('This Customer no longer exists.'));
                $resultRedirect = $this->_resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'consnet_managecustomer/*/edit',
                    [
                        'entity_id' => $customer->getId(),
                        '_current' => true
                    ]
                );
                return $resultRedirect;
            }
        }
        $this->_backendSession = $this->om->get('\Magento\Backend\Model\Session');
        $title = $customer->getId() ? $customer->getName() : __('New Customer');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $data = $this->_backendSession->getData('consnet_managecustomer_customer_data', true);
        if (!empty($data)) {
            $customer->setData($data);
        }
        return $resultPage;
    }
}
