<?php

namespace Consnet\Promotions\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface {

    
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
        /**
         * Create table 'Consnet_demo'
         */
        $table = $installer->getConnection()->newTable(
                        $installer->getTable('Consnet_demo')
                )->addColumn(
                        'demo_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['identity' => true, 'nullable' => false, 'primary'
                    => true], 'Demo ID'
                )->addColumn(
                        'title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Demo Title'
                )->addColumn(
                        'creation_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Creation Time'
                )->addColumn(
                        'update_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Modification Time'
                )->addColumn(
                        'is_active', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '1'], 'Is Active'
                )->addColumn(
                        'validFrom', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'valid From'
                )->addColumn(
                        'knuma_promo', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Promotion ID in ERP'   
                )->addColumn(
                        'vkorg_salesorg', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Sales Org.'  
                )->addColumn(
                        'vtweg_division', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Division'  
                )->addColumn(
                        'spart_distribution', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Distribution'  
                )->addColumn(
                        'ernam_personCreated', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Person Created'  
                )->addColumn(
                        'erdat_dateCreated', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'date Created'  
                )->addColumn(
                        'erzet_timeCreated', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Time Created'  
                )->addColumn(
                        'datab_aggreementValidFrom', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Agreement valid-from date'  
                )->addColumn(
                        'datbi_AgreementValidTo', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Agreement valid-to date' 
                )->addColumn(
                        'botext_DescriptionOfAgreement', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Description of agreement'  
                )->addColumn(
                        'knumh_ConditionRecordNumber', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Condition record number'
                )->addColumn(
                        'kschl_ConditionType', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Condition type'  
                )->addColumn(
                        'kstbw_ScaleValue', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Scale value' 
                )->addColumn(
                        'konws_scaleCurrency', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Scale currency' 
                )->addColumn(
                        'Matnr_product', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Materials' 
                )->addColumn(
                        'validTo', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'valid To'
                )->addIndex(
                        $setup->getIdxName(
                                $installer->getTable('Consnet_demo'), ['title'], AdapterInterface::INDEX_TYPE_FULLTEXT
                        ), ['title'], ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
                )->setComment(
                'Demo Table'
        );
        $installer->getConnection()->createTable($table);


        $tableDelta_promotions = $installer->getConnection()->newTable(
                        $installer->getTable('delta_promotion')
                )->addColumn(
                        'promo_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['identity' => true, 'nullable' => false, 'primary' => true], 'Magento Promotion ID'
                )->addColumn(
                        'knuma_promo', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Promotion ID in ERP'   
                )->addColumn(
                        'vkorg_salesorg', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Sales Org.'  
                )->addColumn(
                        'vtweg_division', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Division'  
                )->addColumn(
                        'spart_distribution', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Distribution'  
                )->addColumn(
                        'ernam_personCreated', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Person Created'  
                )->addColumn(
                        'erdat_dateCreated', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'date Created'  
                )->addColumn(
                        'erzet_timeCreated', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Time Created'  
                )->addColumn(
                        'datab_aggreementValidFrom', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Agreement valid-from date'  
                )->addColumn(
                        'datbi_AgreementValidTo', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Agreement valid-to date' 
                )->addColumn(
                        'botext_DescriptionOfAgreement', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Description of agreement'  
                )->addColumn(
                        'knumh_ConditionRecordNumber', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Condition record number'
                )->addColumn(
                        'kschl_ConditionType', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Condition type'  
                )->addColumn(
                        'kstbw_ScaleValue', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Scale value' 
                )->addColumn(
                        'konws_scaleCurrency', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Scale currency' 
                )->addColumn(
                        'creation_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Creation Time'
                )->addColumn(
                        'update_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Modification Time'
                )->addColumn(
                        'Matnr_product', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Materials' 
                )->addColumn(
                        'kunnr', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Partner number' 
                )->addColumn(
                        'title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Demo Title'
                )->addIndex(
                        $setup->getIdxName(
                                $installer->getTable('delta_promotion'), ['title'], AdapterInterface::INDEX_TYPE_FULLTEXT
                        ), ['title'], ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
                )->setComment(
                'Promo Table'
        );
        $installer->getConnection()->createTable($tableDelta_promotions);


           
            
     
       // `abrex_ExternalDescriptionForAgreement` int(11) NOT NULL COMMENT 'External Description',
                
        
//        `kunnr_customerNumber` text NOT NULL COMMENT 'Customer Number',
//        `bomat_MaterialNumber` text NOT NULL COMMENT 'Material Number',
//        `kbrue_AccrualAmount` text NOT NULL COMMENT 'Accrual Amount',
//        `knuma_pi_Promotion` text NOT NULL COMMENT 'Promotion',
//        `knuma_ag_SalesDeal` text NOT NULL COMMENT 'Sales Deal',
//        `knuma_sq_salesQuote` text NOT NULL COMMENT 'Sales quote',
//                
//        `aktnr_Promo` varchar(10) NOT NULL COMMENT 'Promo',
//        `knuma_bo_AgreementSubsequentSettlement` varchar(10) NOT NULL COMMENT 'Agreement (subsequent settlement)',
//        `matnr_MaterialNumber` text NOT NULL COMMENT 'Material Number',
//        `kfrst_ReleaseStatus` varchar(1) NOT NULL COMMENT 'Release status',
//        `kbstat_ProcessingStatusForConditions` varchar(2) NOT NULL COMMENT 'Processing status for conditions',
//        `kosrt_SearchTermForConditions` varchar(10) NOT NULL COMMENT 'Search term for conditions',
//        `knuma_sd_Standard agreement` varchar(10) NOT NULL COMMENT 'Standard agreement',
//        `kondm_MaterialPricingGroup` varchar(2) NOT NULL COMMENT 'Material pricing group',
//        `bzirk_SalesDistrict` varchar(6) NOT NULL COMMENT 'Sales district'
//        ) ENGINE = InnoDB DEFAULT CHARSET = latin1;



        $installer->endSetup();
    }


}
