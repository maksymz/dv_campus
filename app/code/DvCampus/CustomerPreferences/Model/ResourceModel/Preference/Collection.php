<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Model\ResourceModel\Preference;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \DvCampus\CustomerPreferences\Model\Preference::class,
            \Klarna\Kp\Model\ResourceModel\Quote::class
        );
    }
}
