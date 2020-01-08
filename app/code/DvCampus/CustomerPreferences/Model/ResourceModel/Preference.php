<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Model\ResourceModel;

class Preference extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init('dv_campus_customer_preferences', 'preference_id');
    }
}
