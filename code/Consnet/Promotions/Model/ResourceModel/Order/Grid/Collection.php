<?php
namespace Consnet\Promotions\Model\ResourceModel\Order\Grid;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;
use Vendor\ExtendGrid\Helper\Data as Helper;
/**
* Order grid extended collection
*/
class Collection extends OriginalCollection
{
		// protected function _renderFiltersBefore()
		// {
		// 	$joinTable = $this->getTable('sales_order');
		// 	$this->getSelect()->joinLeft($joinTable, 'main_table.entity_id = sales_order.entity_id', ['increment_id']);
		// 	parent::_renderFiltersBefore();
		// }
}