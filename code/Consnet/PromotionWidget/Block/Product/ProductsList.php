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

    //    $SalesOrg = $_SESSION['VKORG'];

    //     $om = \Magento\Framework\App\ObjectManager::getInstance();
    //     $resource = $om->get('Magento\Framework\App\ResourceConnection');
    //     $connection = $resource->getConnection();
    //     $tableName = $resource->getTableName('delta_promotion');

    //     $custID = $_SESSION['kunnr'];
    //     $sql = "Select spart_distribution FROM " . $tableName." WHERE  vkorg_salesorg = '$SalesOrg'    and kunnr = $custID  ";
    //     $result = $connection->fetchAll($sql); 
      
    //     $productsIds = [];
    //     $id = 0;
    //     foreach($result as $arr){
    //         $productsIds[$id] = $arr['spart_distribution'];
    //         $id++;
    //     } 
            $myJob =  new \Consnet\Promotions\Controller\Index\CronJobPromotion() ;
            $productsIds =  $myJob->getMyListOfMatErp($_SESSION['kunnr']) ;

          //  var_dump( $productsIds ) ;

 

        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter() ;
            // ->setPageSize($this->getPageSize())
            // ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1))
            // ->setOrder($this->getSortBy(), $this->getSortOrder());
       if($collection == null){
           return "there is no promotions for this customer";
       }else{
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

