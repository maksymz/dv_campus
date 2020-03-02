<?php

declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Model;

use DvCampus\CustomerPreferences\Api\Data\PreferenceInterface;

class PreferenceManagement
{
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
     * CustomerPreferences constructor.
     * @param \DvCampus\CustomerPreferences\Model\PreferenceRepository $preferenceRepository
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \DvCampus\CustomerPreferences\Model\PreferenceRepository $preferenceRepository,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->preferenceRepository = $preferenceRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param int $customerId
     * @param int $websiteId
     * @return PreferenceInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerPreferences(int $customerId, int $websiteId): array
    {
        $this->searchCriteriaBuilder->addFilters([
            $this->filterBuilder
                ->setField('customer_id')
                ->setValue($customerId)
                ->setConditionType('eq')
                ->create(),
            $this->filterBuilder
                ->setField('website_id')
                ->setValue($websiteId)
                ->setConditionType('eq')
                ->create()
        ]);

        return $this->preferenceRepository->getList($this->searchCriteriaBuilder->create())
            ->getItems();
    }
}
