<?php
/**
 * Consnet_ManageCustomer extension
 */
namespace Consnet\ManageCustomer\Block\Adminhtml\Customer\Helper;

/**
 * @method string getValue()
 */
class Image extends \Magento\Framework\Data\Form\Element\Image
{
    /**
     * Post image model
     * 
     * @var \Consnet\ManageCustomer\Model\Post\Image
     */
    protected $_imageModel;

    /**
     * constructor
     * 
     * @param \Consnet\ManageCustomer\Model\Post\Image $imageModel
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Consnet\ManageCustomer\Model\Customer\Image $imageModel,
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $data
    )
    {
        $this->_imageModel = $imageModel;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $urlBuilder, $data);
    }

    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = $this->_imageModel->getBaseUrl().$this->getValue();
        }
        return $url;
    }
}
