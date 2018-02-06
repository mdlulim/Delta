<?php
namespace Consnet\PromotionWidget\Block\Product;

class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
{
    const DEFAULT_COLLECTION_SORT_BY = 'name';
    const DEFAULT_COLLECTION_ORDER = 'asc';

    /**
     * Prepare and return product collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function createCollection()
    {  
  
            $myJob =  new \Consnet\Promotions\Controller\Index\CronJobPromotion() ;
            $productsIds =  $myJob->getMyListOfMatErp($_SESSION['kunnr']) ;
 
  
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter() ;
            
       if($collection == null){
           return "there is no promotions for this customer";
       }else{

           if(! is_array ($tab_result->ExPromoList->item)) {
            return  $collection->addAttributeToFilter('sku' , productsIds[0] ) ;
           }

          return $collection->addAttributeToFilter('sku', array('in' =>$productsIds) );
         
       }
        
    }

    /**
     * Retrieve sort by
     *
     * @return int
     */
    public function getSortBy()
    {
        if (!$this->hasData('collection_sort_by')) {
            $this->setData('collection_sort_by', self::DEFAULT_COLLECTION_SORT_BY);
        }
        return $this->getData('collection_sort_by');
    }

    /**
     * Retrieve sort order
     *
     * @return int
     */
    public function getSortOrder()
    {
        if (!$this->hasData('collection_sort_order')) {
            $this->setData('collection_sort_order', self::DEFAULT_COLLECTION_ORDER);
        }
        return $this->getData('collection_sort_order');
    }
}

