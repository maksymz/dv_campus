<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Component\ComponentRegistrar;

class UpgradeData implements UpgradeDataInterface
{
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
     * @param \Magento\Framework\Component\ComponentRegistrar $componentRegistrar
     * @param \Magento\Framework\File\Csv $csv
     */
    public function __construct(
        \Magento\Framework\Component\ComponentRegistrar $componentRegistrar,
        \Magento\Framework\File\Csv $csv
    ) {
        $this->componentRegistrar = $componentRegistrar;
        $this->csv = $csv;
    }
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $this->installDemoPreferences($setup, 'data.csv');
        }

        $setup->endSetup();
    }
    /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     * @throws \Exception
     */
    private function installDemoPreferences(ModuleDataSetupInterface $setup): void
    {
        $connection = $setup->getConnection();
        $filePath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'DvCampus_CustomerPreferences')
            . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'data.csv';

        $tableName = $setup->getTable('dv_campus_customer_preferences');
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

                $setup->getConnection()->insertOnDuplicate(
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
}
