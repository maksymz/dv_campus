<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\CustomerData;

use DvCampus\CustomerPreferences\Model\Preference;
use DvCampus\CustomerPreferences\Model\ResourceModel\Preference\Collection as PreferenceCollection;

class CustomerPreferences implements \Magento\Customer\CustomerData\SectionSourceInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \DvCampus\CustomerPreferences\Model\ResourceModel\Preference\CollectionFactory
     */
    private $preferenceCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * CustomerPreferences constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \DvCampus\CustomerPreferences\Model\ResourceModel\Preference\CollectionFactory $preferenceCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \DvCampus\CustomerPreferences\Model\ResourceModel\Preference\CollectionFactory $preferenceCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->customerSession = $customerSession;
        $this->preferenceCollectionFactory = $preferenceCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function getSectionData(): array
    {
        // @TODO: we must deal with deleted attributes or deleted attribute options
        if ($this->customerSession->isLoggedIn()) {
            $data = [];
            /** @var PreferenceCollection $preferenceCollection */
            $preferenceCollection = $this->preferenceCollectionFactory->create();
            // SHOW WITHOUT int typecast and where to see error message!!!!!!!!!!!!!!!!
            $preferenceCollection->addCustomerFilter((int) $this->customerSession->getId())
                ->addWebsiteFilter((int) $this->storeManager->getWebsite()->getId());

            /** @var Preference $customerPreference */
            foreach ($preferenceCollection as $customerPreference) {
                $data[$customerPreference->getAttributeCode()] = $customerPreference;
            }
        } else {
            $data = $this->customerSession->getData('customer_preferences') ?? [];
        }

        return $data;
    }
}
