<?php

declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Model;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;

class CustomerAttributeOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $attributeCollectionFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        // @TODO: too many attributes at once. Need to add configuration not to use all of them!
        /** @var AttributeCollection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();
        $attributeCollection->addIsFilterableFilter();
        $attributeCollection->setFrontendInputTypeFilter(['in' => ['select', 'multiselect']]);
        $attributes = [];

        /** @var Attribute $attribute */
        foreach ($attributeCollection as $attribute) {
            $attributes[] = [
                'attribute_code' => $attribute->getAttributeCode(),
                'attribute_label' => $attribute->getStoreLabel()
            ];
        }

        return $attributes;
    }
}
