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
     * @var \DvCampus\CustomerPreferences\Model\PreferenceRepository $preferenceRepository
     */
    private $preferenceRepository;

    /**
     * @var \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    private $filterBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private $storeManager;

    /**
     * CustomerPreferences constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \DvCampus\CustomerPreferences\Model\PreferenceRepository $preferenceRepository
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \DvCampus\CustomerPreferences\Model\PreferenceRepository $preferenceRepository,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->customerSession = $customerSession;
        $this->preferenceRepository = $preferenceRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function getSectionData(): array
    {
        if ($this->customerSession->isLoggedIn()) {
            $data = [];

            $this->searchCriteriaBuilder->addFilters([
                $this->filterBuilder
                    ->setField('customer_id')
                    ->setValue((int) $this->customerSession->getId())
                    ->setConditionType('eq')
                    ->create(),
                $this->filterBuilder
                    ->setField('website_id')
                    ->setValue((int) $this->storeManager->getWebsite()->getId())
                    ->setConditionType('eq')
                    ->create()
            ]);

            $customerPreferences = $this->preferenceRepository->getList($this->searchCriteriaBuilder->create())
                ->getItems();

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
