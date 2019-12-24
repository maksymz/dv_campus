<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Setup\Patch\Schema;

class RemoveDemoColumn implements \Magento\Framework\Setup\Patch\SchemaPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface $schemaSetup
     */
    private $schemaSetup;

    /**
     * RemoveOldForeignKeys constructor.
     * @param \Magento\Framework\Setup\SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        \Magento\Framework\Setup\SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * @inheritDoc
     */
    public function apply(): void
    {
        $this->schemaSetup->getConnection()
            ->dropColumn(
                $this->schemaSetup->getTable('dv_campus_customer_preferences'),
                'demo'
            );
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
