<?php

namespace Consnet\CompanyExtended\Setup;

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

        $company = 'company';
        $companyTable = 'company';


        if ($setup->getConnection()->tableColumnExists($company, 'VKORG') === false) {
            $setup->getConnection()
                    ->addColumn(
                            $setup->getTable($company), 'VKORG', [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'VKORG Custom Attribute'
                            ]
            );
        }


        if ($setup->getConnection()->tableColumnExists($company, 'PARVW') === false) {
            $setup->getConnection()
                    ->addColumn(
                            $setup->getTable($company), 'PARVW', [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'PARVW Custom Attribute'
                            ]
            );
        }


        if ($setup->getConnection()->tableColumnExists($company, 'SPART') === false) {
            $setup->getConnection()
                    ->addColumn(
                            $setup->getTable($company), 'SPART', [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'SPART Custom Attribute'
                            ]
            );
        }

        if ($setup->getConnection()->tableColumnExists($company, 'VTWEG') === false) {
            $setup->getConnection()
                    ->addColumn(
                            $setup->getTable($company), 'VTWEG', [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'VTWEG Custom Attribute'
                            ]
            );
        }

        if ($setup->getConnection()->tableColumnExists($company, 'STP_ID') === false) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($company), 'STP_ID', [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'STP_ID Custom Attribute'
                    ]
                );
        }

        if ($setup->getConnection()->tableColumnExists($company, 'ZTERM') === false) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($company), 'ZTERM', [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'ZTERM Custom Attribute'
                    ]
                );
        }

        if ($setup->getConnection()->tableColumnExists($company, 'PLANT') === false) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($company), 'PLANT', [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'PLANT Custom Attribute'
                    ]
                );
        }






        $setup->endSetup();
    }

}
