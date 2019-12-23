<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'geekhub_request_sample'
         */
        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable('dv_campus_customer_preferences')
            )->addColumn(
                'preference_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Request ID'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Id'
            )->addColumn(
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Attribute Id'
            )->addColumn(
                'prefered_values',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                63,
                [],
                'Prefered Values'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation Time'
            )->setComment(
                'Just a demo table'
            );
        $installer->getConnection()->createTable($table);
    }
}
