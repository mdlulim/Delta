<?php

namespace Consnet\ManageCustomer\Block;

class Listing extends \Magento\Framework\View\Element\Template
{
	public function __construct(
        \Magento\Backend\Block\Template\Context                         $context,
        \Consnet\ManageCustomer\Helper\Data                               $moduleHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  $productCollectionFactory,
        \Magento\Catalog\Block\Product\ListProduct                      $listProductBlock,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_moduleHelper                            = $moduleHelper;
        $this->_productCollectionFactory                = $productCollectionFactory;
        $this->listProductBlock                         = $listProductBlock;
    }

    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set($this->_moduleHelper->getAdminjoinsTitle());
        $this->pageConfig->setDescription(
            $this->escapeHtml($this->stripTags($this->_moduleHelper->getDescription()))
        );
        $this->pageConfig->setKeywords($this->_moduleHelper->getMetaKeywords());
        return parent::_prepareLayout();
    }
}