<?php
/**
 * Consnet extension
 */
namespace Consnet\ManageCustomer\Block\Adminhtml;

class Customer extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_customer';
        $this->_blockGroup = 'Consnet_ManageCustomer';
        $this->_headerText = __('Customers');
        $this->_addButtonLabel = __('Add New Customer');
        parent::_construct();
    }
}
