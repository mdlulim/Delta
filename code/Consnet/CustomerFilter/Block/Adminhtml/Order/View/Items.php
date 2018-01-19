<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Consnet\CustomerFilter\Block\Adminhtml\Order\View;

use Magento\Sales\Model\ResourceModel\Order\Item\Collection;

/**
 * Adminhtml order items grid
 *
 * @api
 * @since 100.0.2
 */
class Items extends \Magento\Sales\Block\Adminhtml\Items\AbstractItems
{
    /**
     * @return array
     * @since 100.1.0
     */
    public function getColumns()
    {
        $columns = array_key_exists('columns', $this->_data) ? $this->_data['columns'] : [];
        $columns = array('product' => 'Product', 'status'=> '','|'=>'<spam style="color: white;">|</spam>' , 'price'=> 'Price', 
        'ordered-qty'=> 'Qty', 'subtotal' => 'Subtotal', ''=> '', 'tax-percent'=> '<spam style="color: white;">|</spam>','discount-amount'=> '<spam style="color: white;">|</spam>') ;

        /*$columns = array('product' => 'Product', 'status'=> 'Item Status','|'=>'<spam style="color: white;">|</spam>' , 'price'=> 'Price', 
        'ordered-qty'=> 'Qty', 'subtotal' => 'Subtotal', ''=> '', 'tax-percent'=> '<spam style="color: white;">|</spam>','discount-amount'=> '<spam style="color: white;">|</spam>', 'total'=> 'Row Total') ;*/

        return $columns;
    }

    /**
     * Retrieve required options from parent
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
     
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid parent block for this block'));
        }
        $this->setOrder($this->getParentBlock()->getOrder());
        parent::_beforeToHtml();
    }

    /**
     * Retrieve order items collection
     *
     * @return Collection
     */
    public function getItemsCollection()
    {
        return $this->getOrder()->getItemsCollection();
    }
}
