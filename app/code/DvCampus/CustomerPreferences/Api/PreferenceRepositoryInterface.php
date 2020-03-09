<?php

declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Api;

use DvCampus\CustomerPreferences\Api\Data\PreferenceInterface;
use DvCampus\CustomerPreferences\Api\Data\PreferenceSearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface PreferenceRepositoryInterface
{
    /**
     * Save preference item
     *
     * @param PreferenceInterface $preference
     * @return PreferenceInterface
     */
    public function save(PreferenceInterface $preference): PreferenceInterface;

    /**
     * Get customer preference by preference_id
     *
     * @param int $preferenceId
     * @return PreferenceInterface
     */
    public function get(int $preferenceId): PreferenceInterface;

    /**
     * Get list of customer preferences
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \DvCampus\CustomerPreferences\Api\Data\PreferenceSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): PreferenceSearchResultInterface;

    /**
     * Delete customer preference object
     *
     * @param PreferenceInterface $preference
     * @return bool
     */
    public function delete(PreferenceInterface $preference): bool;

    /**
     * Delete customer preference by preference_id
     *
     * @param int $preferenceId
     * @return bool
     */
    public function deleteById(int $preferenceId): bool;
}
