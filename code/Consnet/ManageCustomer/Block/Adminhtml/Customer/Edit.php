<?php
/**
 * Consnet_ManageCustomer extension
 */
namespace Consnet\ManageCustomer\Block\Adminhtml\Customer;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     * 
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * constructor
     * 
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize Post edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Consnet_ManageCustomer';
        $this->_controller = 'adminhtml_customer';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Customer'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete Customer'));
    }
    /**
     * Retrieve text for header element depending on loaded Post
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \Consnet\ManageCustomer\Model\Post $post */
        $customer = $this->_coreRegistry->registry('customer_entity');
        if ($customer->getId()) {
            return __("Edit Customer '%1'", $this->escapeHtml($customer->getName()));
        }
        return __('New Customer');
    }
}
