<?php

declare(strict_types=1);

namespace DvCampus\ProductAttributeToOrder\Plugin;

class AdditionalOptionsToOrderItem
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * AddFinishingToBuyRequest constructor.
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $subject
     * @param array $result
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function afterGetOrderOptions(
        \Magento\Catalog\Model\Product\Type\AbstractType $subject,
        array $result,
        \Magento\Catalog\Model\Product $product
    ): array {
        if ($additionalOptions = $product->getCustomOption('additional_options')) {
            $result['additional_options'] = $this->serializer->unserialize($additionalOptions->getValue());
        }

        return $result;
    }
}
