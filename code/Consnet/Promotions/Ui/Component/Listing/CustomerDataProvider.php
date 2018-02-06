<?php
namespace Consnet\Promotions\Ui\Component\Listing ;


class CustomerDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
   protected function _initSelect()
   {
      parent::_initSelect();
      $this->getSelect()->joinLeft(
        ['secondTable' => $this->getTable('company')],
        'main_table.entity_id = secondTable.entity_id',
        ['STP_ID']
      );
      // $this->getSelect()->joinLeft(
      //   ['secondTable' => $this->getTable('consnet_multi_customer_user')],
      //   'main_table.entity_id = secondTable.user_id',
      //   ['company_id']
      // );
      return $this;
  }
}


// consnet_multi_customer_user
// company_id

	// user_id