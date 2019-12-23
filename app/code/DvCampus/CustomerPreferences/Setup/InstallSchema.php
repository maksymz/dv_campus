<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        // The code in the install and upgrade scripts is the same
        // Though, right now this file  will not work because first version of this module did not have any models
    }
}
