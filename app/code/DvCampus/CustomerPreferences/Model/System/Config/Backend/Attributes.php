<?php

declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Model\System\Config\Backend;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;
use Magento\Framework\Serialize\Serializer\Json;

class Attributes extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * Attributes constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        Json $serializer = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data,
            $serializer
        );
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        /** @var array $values */
        $values = $this->getValue();

        $customerAttributeOptionCodes = [];
        /** @var AttributeCollection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();
        $attributeCollection->addIsFilterableFilter();
        $attributeCollection->setFrontendInputTypeFilter(['in' => ['select', 'multiselect']]);

        /** @var Attribute $attribute */
        foreach ($attributeCollection as $attribute) {
            $customerAttributeOptionCodes[$attribute->getAttributeCode()] = 1;
        }

        foreach ($values as $index => $value) {
            // remove attribute from the list if it was deleted
            if (!isset($customerAttributeOptionCodes[$value['attribute_code']])) {
                unset($values[$index]);
            }

            unset($customerAttributeOptionCodes[$value['attribute_code']]);
        }

        // add new attributes to the list
        foreach ($customerAttributeOptionCodes as $attributeCode => $enabled) {
            $values[] = [
                'attribute_code' => $attributeCode,
                'enabled' => $enabled
            ];
        }

        $this->setValue($values);
    }
}
