<?php
/**
 *                     Consnet_ManageCustomer extension
 */
namespace Consnet\ManageCustomer\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * install tables
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('consnet_managecustomer')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('consnet_managecustomer')
            )
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'Customer ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'Customer Name'
            )
            ->addColumn(
                'url_key',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Customer URL Key'
            )
            ->addColumn(
                'customer_content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Customer Customer Content'
            )
            ->addColumn(
                'tags',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Customer Tags'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                1,
                [],
                'Customer Status'
            )
            ->addColumn(
                'featured_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Customer Featured Image'
            )
            ->addColumn(
                'sample_country_selection',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                3,
                [],
                'Customer Sample Country Selection'
            )
            ->addColumn(
                'sample_upload_file',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Customer Sample File'
            )
            ->addColumn(
                'sample_multiselect',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Customer Sample Multiselect'
            )

            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Customer Created At'
            )
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Customer Updated At'
            )
            ->setComment('Customer Table');
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                $installer->getTable('consnet_managecustomer'),
                $setup->getIdxName(
                    $installer->getTable('consnet_managecustomer'),
                    ['name','url_key','customer_content','tags','featured_image','sample_upload_file'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name','url_key','customer_content','tags','featured_image','sample_upload_file'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
        $installer->endSetup();
    }
}
