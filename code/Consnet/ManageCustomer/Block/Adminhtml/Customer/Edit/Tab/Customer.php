<?php
/**
 * ManageCustomer extension
 */
namespace Consnet\ManageCustomer\Block\Adminhtml\Customer\Edit\Tab;

class Customer extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Wysiwyg config
     * 
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * Country options
     * 
     * @var \Magento\Config\Model\Config\Source\Locale\Country
     */
    protected $_countryOptions;

    /**
     * Country options
     * 
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_booleanOptions;

    /**
     * Sample Multiselect options
     * 
     * @var \Mageplaza\ManageCustomer\Model\Post\Source\SampleMultiselect
     */
    protected $_sampleMultiselectOptions;

    /**
     * constructor
     * 
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Config\Model\Config\Source\Locale\Country $countryOptions
     * @param \Magento\Config\Model\Config\Source\Yesno $booleanOptions
     * @param \Mageplaza\ManageCustomer\Model\Post\Source\SampleMultiselect $sampleMultiselectOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Config\Model\Config\Source\Locale\Country $countryOptions,
        \Magento\Config\Model\Config\Source\Yesno $booleanOptions,
        \Consnet\ManageCustomer\Model\Customer\Source\SampleMultiselect $sampleMultiselectOptions,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        $this->_wysiwygConfig            = $wysiwygConfig;
        $this->_countryOptions           = $countryOptions;
        $this->_booleanOptions           = $booleanOptions;
        $this->_sampleMultiselectOptions = $sampleMultiselectOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\ManageCustomer\Model\Post $post */
        $customer = $this->_coreRegistry->registry('customer_entity');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('customer_');
        $form->setFieldNameSuffix('customer');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Customer Information'),
                'class'  => 'fieldset-wide'
            ]
        );
        $fieldset->addType('image', 'Consnet\ManageCustomer\Block\Adminhtml\Customer\Helper\Image');
        $fieldset->addType('file', 'Consnet\ManageCustomer\Block\Adminhtml\Customer\Helper\File');
        if ($customer->getId()) {
            $fieldset->addField(
                'entity_id',
                'hidden',
                ['name' => 'entity_id']
            );
        }
        
         $fieldset->addField(
            'website_id',
            'select',
            [
                'name'  => 'website_id',
                'label' => __('Associate to Website'),
                'title' => __('Associate to Website'),
                'value' => 'Main Website',
            ]
        );
        $fieldset->addField(
            'group_id',
            'select',
            [
                'name'  => 'group_id',
                'label' => __('Group'),
                'title' => __('Group'),
                'value' => '1',
            ]
        );
        $fieldset->addField(
            'prefix',
            'text',
            [
                'name'  => 'prefix',
                'label' => __('Prefix'),
                'title' => __('Prefix'),
            ]
        );
        $fieldset->addField(
            'firstname',
            'text',
            [
                'name'  => 'firstname',
                'label' => __('First Name'),
                'title' => __('First Name'),
            ]
        );
         $fieldset->addField(
            'middlename',
            'text',
            [
                'name'  => 'middlename',
                'label' => __('Middle Name/Initial'),
                'title' => __('Middle Name/Initial'),
            ]
        );
        $fieldset->addField(
            'lastname',
            'text',
            [
                'name'  => 'lastname',
                'label' => __('Last Name'),
                'title' => __('Last Name'),
            ]
        );
       
       
       $fieldset->addField(
            'suffix',
            'text',
            [
                'name'  => 'suffix',
                'label' => __('Suffix'),
                'title' => __('Suffix'),
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                'name'  => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
            ]
        );
        /* $fieldset->addField(
            'Kunnr',
            'text',
            [
                'name'  => 'Kunnr',
                'label' => __('Customer Number'),
                'title' => __('Customer Number'),
            ]
        );
         $fieldset->addField(
            'Pernr',
            'text',
            [
                'name'  => 'Pernr',
                'label' => __('Contact Person'),
                'title' => __('Contact Person'),
            ]
        );*/
         $fieldset->addField(
            'dop',
            'text',
            [
                'name'  => 'dop',
                'label' => __('Date Of Birth'),
                'title' => __('Date Of Birth'),
            ]
        );
         $fieldset->addField(
            'taxvat',
            'text',
            [
                'name'  => 'taxvat',
                'label' => __('Tax/VAT Number'),
                'title' => __('Tax/VAT Number'),
            ]
        );
        $fieldset->addField(
            'gender',
            'date',
            [
                'name'  => 'gender',
                'label' => __('Gender'),
                'title' => __('Gender'),
            ]
        );
       
        

        $customerData = $this->_session->getData('consnet_managecustomer_customer_data', true);
        if ($customerData) {
            $customer->addData($customerData);
        } else {
            if (!$customer->getId()) {
                $customer->addData($customer->getDefaultValues());
            }
        }
        $form->addValues($customer->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Customer');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
