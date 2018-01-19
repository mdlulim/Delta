<?php

namespace Consnet\Promotions\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.24', '<')) {
            $connection = $setup->getConnection();
            $column = [
                'type' => Table::TYPE_SMALLINT,
                'length' => 6,
                'nullable' => false,
                'comment' => 'Is Visible',
                'default' => '1'
            ];
            $connection->addColumn($setup->getTable('Consnet_demo'), 'is_visible', $column);



            // $column1 = [
            //     'type' => Table::TYPE_TEXT,
            //     'length' => 10,
            //     'nullable' => false,
            //     'comment' => 'Erp Order Id' 
            // ];
            // $connection->addColumn($setup->getTable('sales_order_grid'), 'erpOrderId', $column1);



        }
        $setup->endSetup();
    }

}



   

  