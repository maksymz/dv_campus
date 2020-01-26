<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Block;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * Form constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        parent::_construct();

        $this->addData(
            [
                'cache_lifetime' => 86400,
                'cache_tags' => [
                    \Magento\Catalog\Model\Product::CACHE_TAG,
                    \Magento\Eav\Model\Cache\Type::CACHE_TAG
                ]
            ]
        );
    }

    /**
     * @return string
     */
    public function getAttributesJson(): string
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

        return $this->jsonSerializer->serialize($attributes);
    }
}
