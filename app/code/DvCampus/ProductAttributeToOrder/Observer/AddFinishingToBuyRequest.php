<?php

declare(strict_types=1);

namespace DvCampus\ProductAttributeToOrder\Observer;

use Magento\Framework\Event\Observer;

class AddFinishingToBuyRequest implements \Magento\Framework\Event\ObserverInterface
{
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
            $product = $observer->getEvent()->getData('product');
            asort($requestInfo['options']['finishing']);
            $product->addCustomOption('finishing', implode(',', $requestInfo['options']['finishing']));
        }
    }
}
