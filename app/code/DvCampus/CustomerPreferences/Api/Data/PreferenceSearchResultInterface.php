<?php

declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Api\Data;

/**
 * Must redefine the interface methods for \Magento\Framework\Reflection\DataObjectProcessor::buildOutputDataArray()
 * Must not declare return types to keep the interface consistent with the parent interface
 */
interface PreferenceSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \DvCampus\CustomerPreferences\Api\Data\PreferenceInterface[]
     */
    public function getItems();

    /**
     * Set items list.
     *
     * @param \DvCampus\CustomerPreferences\Api\Data\PreferenceInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
