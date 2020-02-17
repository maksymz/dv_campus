<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Model\ResourceModel\Preference;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'preference_id';

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \DvCampus\CustomerPreferences\Model\Preference::class,
            \DvCampus\CustomerPreferences\Model\ResourceModel\Preference::class
        );
    }

    /**
     * @param int $websiteId
     * @return Collection
     */
    public function addWebsiteFilter(int $websiteId): self
    {
        return $this->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * @param int $customerId
     * @return Collection
     */
    public function addCustomerFilter(int $customerId): self
    {
        return $this->addFieldToFilter('customer_id', $customerId);
    }
}
