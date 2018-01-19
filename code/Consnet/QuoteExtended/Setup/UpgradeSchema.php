<?php

namespace Consnet\QuoteExtended\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();

        $quote = 'quote';
       


        if ($setup->getConnection()->tableColumnExists($quote , 'DELIVERY_DATE') === false) {
            $setup->getConnection()
                    ->addColumn(
                            $setup->getTable($quote), 'DELIVERY_DATE', [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'Delivery date Custom Attribute'
                            ]
            );
        }


        if ($setup->getConnection()->tableColumnExists($quote, 'ECC_ORDER') === false) {
            $setup->getConnection()
                    ->addColumn(
                            $setup->getTable($quote), 'ECC_ORDER', [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'Ecc order number Custom Attribute'
                            ]
            );
        }

        if ($setup->getConnection()->tableColumnExists($quote, 'STP_ID') === false) {
            $setup->getConnection()
                    ->addColumn(
                            $setup->getTable($quote), 'STP_ID', [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'Ecc CUSTOMER number Custom Attribute'
                            ]
            );
        }

        


        



        $setup->endSetup();
    }

}