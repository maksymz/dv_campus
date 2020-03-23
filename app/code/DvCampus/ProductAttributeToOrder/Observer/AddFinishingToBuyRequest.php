<?php

declare(strict_types=1);

namespace DvCampus\ProductAttributeToOrder\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;

class AddFinishingToBuyRequest implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    private $serializer;

    /**
     * @var \Magento\Framework\Escaper $escaper
     */
    private $escaper;

    /**
     * AddFinishingToBuyRequest constructor.
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->serializer = $serializer;
        $this->escaper = $escaper;
    }

    /**
     * Add attribute options as custom options to prevent from merging with other items
     * See \Magento\Checkout\Model\Quote::addProduct()
     * $cartCandidates = $product->getTypeInstance()->prepareForCartAdvanced($request, $product, $processMode);
     * @inheritDoc
     */
    public function execute(Observer $observer): void
    {
        if (($requestInfo = $observer->getEvent()->getData('info'))
            && isset($requestInfo['options']['finishing'])
        ) {
            /** @var Product $product */
            $product = $observer->getEvent()->getData('product');

            if (!$finishing = $product->getData('finishing')) {
                return;
            }

            $attribute = $product->getAttributes()['finishing'];
            $product->setData('finishing', implode(',', $requestInfo['options']['finishing']));
            $value = $attribute->getFrontend()->getValue($product);
            $product->setData('finishing', $finishing);

            if (($additionalOptions = $product->getCustomOption('additional_options'))
                && !is_array($additionalOptions)
            ) {
                $additionalOptions = $this->serializer->unserialize($additionalOptions->getValue());
            } else {
                $additionalOptions = [];
            }

            $additionalOptions[] = [
                'label' => $attribute->getFrontend()->getLabel(),
                'value' => $this->escaper->escapeHtml($value),
                'print_value' => $this->escaper->escapeHtml($value),
                'option_id' => 'dv_campus_' . $attribute->getAttributeCode(),
                'option_type' => \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_MULTIPLE,
                'custom_view' => false,
            ];

            $product->addCustomOption(
                'additional_options',
                $this->serializer->serialize($additionalOptions)
            );
        }
    }
}
