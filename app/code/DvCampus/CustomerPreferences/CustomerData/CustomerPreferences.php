<?php

declare(strict_types=1);

namespace DvCampus\CustomerPreferences\CustomerData;

use DvCampus\CustomerPreferences\Model\PreferenceData;

class CustomerPreferences implements \Magento\Customer\CustomerData\SectionSourceInterface
{
    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    private $customerSession;

    /**
     * @var \DvCampus\CustomerPreferences\Model\PreferenceManagement $preferenceManagement
     */
    private $preferenceManagement;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private $storeManager;

    /**
     * CustomerPreferences constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \DvCampus\CustomerPreferences\Model\PreferenceManagement $preferenceManagement
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \DvCampus\CustomerPreferences\Model\PreferenceManagement $preferenceManagement,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->customerSession = $customerSession;
        $this->preferenceManagement = $preferenceManagement;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSectionData(): array
    {
        if ($this->customerSession->isLoggedIn()) {
            $data = [];

            $customerPreferences = $this->preferenceManagement->getCustomerPreferences(
                (int) $this->customerSession->getId(),
                (int) $this->storeManager->getWebsite()->getId()
            );

            /** @var PreferenceData $customerPreference */
            foreach ($customerPreferences as $customerPreference) {
                $data[$customerPreference->getAttributeCode()] = $customerPreference->getPreferredValues();
            }
        } else {
            $data = $this->customerSession->getData('customer_preferences') ?? [];
        }

        return $data;
    }
}
