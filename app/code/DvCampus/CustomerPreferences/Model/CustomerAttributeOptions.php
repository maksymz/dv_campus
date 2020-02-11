<?php

declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Model;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;

class CustomerAttributeOptions implements \Magento\Framework\Data\OptionSourceInterface
{
    public const XML_PATH_ATTRIBUTES = 'dvcampus_customer_preferences/general/attributes';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigs
     */
    private $scopeConfigs;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     */
    private $jsonSerializer;

    /**
     * CustomerAttributeOptions constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigs
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigs,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->scopeConfigs = $scopeConfigs;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $attributes = [];
        $allowedAttributeCodes = [];
        $attributesConfig = $this->scopeConfigs->getValue(self::XML_PATH_ATTRIBUTES);

        if (!$attributesConfig
            || !is_array($attributesConfig = $this->jsonSerializer->unserialize($attributesConfig))
        ) {
            return $attributes;
        }

        foreach ($attributesConfig as $attributeConfig) {
            if ($attributeConfig['enabled']) {
                $allowedAttributeCodes[] = $attributeConfig['attribute_code'];
            }
        }

        // @TODO: too many attributes at once. Need to add configuration not to use all of them!
        /** @var AttributeCollection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();
        $attributeCollection->addIsFilterableFilter();
        $attributeCollection->setFrontendInputTypeFilter(['in' => ['select', 'multiselect']]);
        $attributeCollection->setCodeFilter($allowedAttributeCodes);

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
