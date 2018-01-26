<?php
/**
 * Consnet_ManageCustomer extension
 */
namespace Consnet\ManageCustomer\Controller\Adminhtml;

abstract class Customer extends \Magento\Backend\App\Action
{
    protected $_om;
    /**
     * Customer Factory
     * 
     * @var \Consnet\ManageCustomer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Core registry
     * 
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Result redirect factory
     * 
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $_resultRedirectFactory;

    /**
     * constructor
     * 
     * @param \Consnet\ManageCustomer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
       
    )
    {
        $this->om = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_resultRedirectFactory = $this->om->get('\Magento\Backend\Model\View\Result\RedirectFactory');
        $this->_customerFactory = $this->om->get('\Consnet\ManageCustomer\Model\CustomerFactory');
        $this->_coreRegistry= $this->om->get('\Magento\Framework\Registry');
        $this->_context= $this->om->get('\Magento\Backend\App\Action\Context');
      
        parent::__construct($this->_context);
    }

    /**
     * Init Customer
     *
     * @return \Consnet\ManageCustomer\Model\Customer
     */
    protected function _initCustomer()
    {
        $customerId  = (int) $this->getRequest()->getParam('entity_id');
        /** @var \Consnet\ManageCustomer\Model\Customer $customer */
        $customer    = $this->_customerFactory->create();
        if ($customerId) {
            $customer->load($customerId);
        }
        $this->_coreRegistry->register('customer_entity', $customer);
        return $customer;
    }
}
