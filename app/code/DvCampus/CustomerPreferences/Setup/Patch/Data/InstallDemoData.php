<?php

namespace DvCampus\CustomerPreferences\Setup\Patch\Data;

use Magento\Framework\Component\ComponentRegistrar;

class InstallDemoData implements
    \Magento\Framework\Setup\Patch\DataPatchInterface,
    \Magento\Framework\Setup\Patch\PatchVersionInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var \Magento\Framework\File\Csv $csv
     */
    private $csv;

    /**
     * @var \Magento\Framework\Component\ComponentRegistrar $componentRegistrar
     */
    private $componentRegistrar;

    /**
     * UpgradeData constructor.
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Framework\Component\ComponentRegistrar $componentRegistrar
     * @param \Magento\Framework\File\Csv $csv
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Framework\Component\ComponentRegistrar $componentRegistrar,
        \Magento\Framework\File\Csv $csv
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->componentRegistrar = $componentRegistrar;
        $this->csv = $csv;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function apply(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $filePath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'DvCampus_CustomerPreferences')
            . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'data.csv';

        $tableName = $this->moduleDataSetup->getTable('dv_campus_customer_preferences');
        $csvData = $this->csv->getData($filePath);

        try {
            $connection->beginTransaction();
            $columns = [
                'customer_id',
                'attribute_id',
                'prefered_values',
            ];

            foreach ($csvData as $rowNumber => $data) {
                $insertedData = array_combine($columns, $data);

                $connection->insertOnDuplicate(
                    $tableName,
                    $insertedData,
                    $columns
                );
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '1.0.2';
    }
}
