<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Model;

class Preference extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(\DvCampus\CustomerPreferences\Model\ResourceModel\Preference::class);
    }
}
