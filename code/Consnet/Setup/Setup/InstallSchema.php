<?php


namespace Consnet\Setup\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        //create  erp_magento
        $this->createErpMagentoTable($installer);
        $this->createCRMMagentoTable($installer);
        $this->createCompanyUserTable($installer);
        $this->createMultiCustomerUserTable($installer);
        $this->createMultiCustomerUserTable($installer);


    }

    public  function  createErpMagentoTable($installer){
        $installer->startSetup();
        $erp_magento = $installer->getConnection()
            ->newTable($installer->getTable('erp_magento'))//Correct Table Name sap_erp_soldtoparty1
            ->addColumn(
                'magOrderId',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'magOrderId'
            )
            ->addColumn(
                'magCustomerId',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'magCustomerId'
            )
            ->addColumn(
                'erpOrderId',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'erpOrderId'
            )
            ->addColumn(
                'erpOrderCreated',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'erpOrderCreated'
            )
            ->setComment('SAP ERP MAGENTO MAPPER');
        $installer->getConnection()->createTable($erp_magento);

        $installer->endSetup();

    }

    public function createCRMMagentoTable($installer){
        $crm_magento = $installer->getConnection()
            ->newTable($installer->getTable('crm_service_requests'))//Correct Table Name sap_erp_soldtoparty1
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'id'
            )
            ->addColumn(
                'customer',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'customer'
            )
            ->addColumn(
                'request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'request_id'
            )
            ->addColumn(
                'request_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'request_title'
            )
            ->addColumn(
                'createDate',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'createDate'
            )
            ->setComment('SAP CRM SERVICE REQUEST');
        $installer->getConnection()->createTable($crm_magento);

        $installer->endSetup();
    }

    public function createCompanyUserTable($installer){
        $crm_magento = $installer->getConnection()
            ->newTable($installer->getTable('consnet_company_user'))//Correct Table Name sap_erp_soldtoparty1
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'id'
            )
            ->addColumn(
                'user_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'user_email'
            )
            ->addColumn(
                'company_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'company_id'
            )
            ->addColumn(
                'request_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'request_title'
            )
            ->addColumn(
                'createDate',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'createDate'
            )
            ->setComment('Company Users');
        $installer->getConnection()->createTable($crm_magento);

        $installer->endSetup();
    }

    public function createMultiCustomerUserTable($installer){
        $crm_magento = $installer->getConnection()
            ->newTable($installer->getTable('consnet_multi_customer_user'))//Correct Table Name sap_erp_soldtoparty1
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'id'
            )
            ->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'user_id'
            )
            ->addColumn(
                'company_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'company_id'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'name'
            )
            ->setComment('Multi user mapping table');
        $installer->getConnection()->createTable($crm_magento);

        $installer->endSetup();
    }
}
