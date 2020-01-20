<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Block;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * Form constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeCollectionFactory = $attributeCollectionFactory;
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
     * @return AttributeCollection
     */
    public function getAttributeCollection(): AttributeCollection
    {
        // @TODO: too many attributes at once. Need to add configuration not to use all of them!
        /** @var AttributeCollection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();
        $attributeCollection->addIsFilterableFilter();
        $attributeCollection->setFrontendInputTypeFilter(['in' => ['select', 'multiselect']]);

        return $attributeCollection;
    }
}
