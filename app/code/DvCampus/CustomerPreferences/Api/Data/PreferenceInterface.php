<?php

declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Api\Data;

interface PreferenceInterface
{
    public const PREFERENCE_ID = 'preference_id';

    public const CUSTOMER_ID = 'customer_id';

    public const ATTRIBUTE_ID = 'attribute_id';

    public const ATTRIBUTE_CODE = 'attribute_code';

    public const WEBSITE_ID = 'website_id';

    public const PREFERRED_VALUES = 'preferred_values';

    public const CREATED_AT = 'created_at';

    /**
     * @return int
     */
    public function getPreferenceId(): int;

    /**
     * @param int $preferenceId
     * @return $this
     */
    public function setPreferenceId(int $preferenceId): self;

    /**
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId(int $customerId): self;

    /**
     * @return int
     */
    public function getAttributeId(): int;

    /**
     * @param int $attributeId
     * @return $this
     */
    public function setAttributeId(int $attributeId): self;

    /**
     * @return string
     */
    public function getAttributeCode(): string;

    /**
     * @param string $attributeCode
     * @return $this
     */
    public function setAttributeCode(string $attributeCode): self;

    /**
     * @return int
     */
    public function getWebsiteId(): int;

    /**
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId(int $websiteId): self;

    /**
     * @return string
     */
    public function getPreferredValues(): string;

    /**
     * @param string $preferredValues
     * @return $this
     */
    public function setPreferredValues(string $preferredValues): self;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self;
}
