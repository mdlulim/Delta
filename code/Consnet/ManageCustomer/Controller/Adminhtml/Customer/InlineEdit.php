<?php
/**
 * Consnet_ManageCustomer extension
 */
namespace Consnet\ManageCustomer\Controller\Adminhtml\Customer;

abstract class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * JSON Factory
     * 
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_jsonFactory;

    /**
     * Customer Factory
     * 
     * @var \Consnet\ManageCustomer\Model\CustomerFactory
     */
    protected $_customrFactory;

    /**
     * constructor
     * 
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Mageplaza\ManageCustomer\Model\CustomerFactory $customerFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Consnet\ManageCustomer\Model\CustomerFactory $customerFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->_jsonFactory = $jsonFactory;
        $this->_customerFactory = $customerFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->_jsonFactory->create();
        $error = false;
        $messages = [];
        $customerItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($customerItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        foreach (array_keys($customerItems) as $customerId) {
            /** @var \Consnet\ManageCustomer\Model\Customer $customer */
            $customer = $this->_customerFactory->create()->load($customerId);
            try {
                $customerData = $customerItems[$customerId];//todo: handle dates
                $customer->addData($customerData);
                $customer->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithCustomerId($customer, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithCustomerId($customer, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithCustomerId(
                    $customer,
                    __('Something went wrong while saving the Customer.')
                );
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Customer id to error message
     *
     * @param \Mageplaza\ManageCustomer\Model\Customer $customer
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithCustomerId(\Consnet\ManageCustomer\Model\Customer $customer, $errorText)
    {
        return '[Customer ID: ' . $customer->getId() . '] ' . $errorText;
    }
}
