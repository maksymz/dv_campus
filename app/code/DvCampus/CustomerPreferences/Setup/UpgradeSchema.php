<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $table = $setup->getConnection()
                ->newTable(
                    $setup->getTable('dv_campus_customer_preferences')
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
            $setup->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $connection = $setup->getConnection();

            $connection->changeColumn(
                $setup->getTable('dv_campus_customer_preferences'),
                'customer_id',
                'customer_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 10,
                    'nullable' => false,
                    'unsigned' => true
                ]
            );
            $connection->changeColumn(
                $setup->getTable('dv_campus_customer_preferences'),
                'attribute_id',
                'attribute_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 5,
                    'nullable' => false,
                    'unsigned' => true
                ]
            );
            $connection->addForeignKey(
                $setup->getFkName(
                    $setup->getTable('dv_campus_customer_preferences'),
                    'customer_id',
                    'customer_entity',
                    'entity_id'
                ),
                $setup->getTable('dv_campus_customer_preferences'),
                'customer_id',
                $setup->getTable('customer_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
            $connection->addForeignKey(
                $setup->getFkName(
                    $setup->getTable('dv_campus_customer_preferences'),
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                $setup->getTable('dv_campus_customer_preferences'),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        }
    }
}
