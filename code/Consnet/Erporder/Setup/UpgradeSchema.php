<?php

namespace Consnet\Erporder\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();
        //if (version_compare($context->getVersion(), '1.0.1', '<')) {
          $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'erp_order',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'ERP Order Number'
                ]
            );
        //}
        $installer->endSetup();
    }
}