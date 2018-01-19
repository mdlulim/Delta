<?php
namespace Consnet\SharedCatalog\Plugin;

class SharedCatalog
{
    public function __construct(){
        
    }

    public function afterSave($result){
        //var_dump(get_called_class($result));
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $sharedcatalog = $om->create('Magento\SharedCatalog\Model\ProductManagement');
        $product = $om->create('Magento\Catalog\Model\Product')->load(461);
        $sharedcatalog->assignProducts(2, array($product));//$result->getId()
        //$sharedcatalog->save();
    }
}
?>
