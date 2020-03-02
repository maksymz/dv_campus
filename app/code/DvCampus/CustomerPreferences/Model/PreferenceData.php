<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Model;

use DvCampus\CustomerPreferences\Api\Data\PreferenceInterface;

class PreferenceData extends \Magento\Framework\Api\AbstractSimpleObject implements
    \DvCampus\CustomerPreferences\Api\Data\PreferenceInterface
{
    /**
     * @inheritDoc
     */
    public function getPreferenceId(): int
    {
        return (int) $this->_get(PreferenceInterface::PREFERENCE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPreferenceId(int $preferenceId): PreferenceInterface
    {
        $this->setData(self::PREFERENCE_ID, $preferenceId);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId(): int
    {
        return (int) $this->_get(PreferenceInterface::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId(int $customerId): \DvCampus\CustomerPreferences\Api\Data\PreferenceInterface
    {
        $this->setData(self::CUSTOMER_ID, $customerId);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttributeId(): int
    {
        return (int) $this->_get(PreferenceInterface::ATTRIBUTE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAttributeId(int $attributeId): PreferenceInterface
    {
        $this->setData(self::ATTRIBUTE_ID, $attributeId);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttributeCode(): string
    {
        return (string) $this->_get(PreferenceInterface::ATTRIBUTE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setAttributeCode(string $attributeCode): PreferenceInterface
    {
        $this->setData(self::ATTRIBUTE_CODE, $attributeCode);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getWebsiteId(): int
    {
        return (int) $this->_get(PreferenceInterface::WEBSITE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setWebsiteId(int $websiteId): PreferenceInterface
    {
        $this->setData(self::WEBSITE_ID, $websiteId);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPreferredValues(): string
    {
        return (string) $this->_get(PreferenceInterface::PREFERRED_VALUES);
    }

    /**
     * @inheritDoc
     */
    public function setPreferredValues(string $preferredValues): PreferenceInterface
    {
        $this->setData(self::WEBSITE_ID, $preferredValues);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return (string) $this->_get(PreferenceInterface::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $createdAt): PreferenceInterface
    {
        $this->setData(self::WEBSITE_ID, $createdAt);

        return $this;
    }
}
