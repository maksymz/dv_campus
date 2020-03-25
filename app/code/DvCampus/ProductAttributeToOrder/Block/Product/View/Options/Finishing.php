<?php

declare(strict_types=1);

namespace DvCampus\ProductAttributeToOrder\Block\Product\View\Options;

use Magento\Framework\View\Element\Html\Select;

class Finishing extends \Magento\Catalog\Block\Product\View\Options
{
    // Based on \Magento\Catalog\Block\Product\View\Options\Type\Select\Multiple
    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        $product = $this->getProduct();

        if (!$finishing = $product->getData('finishing')) {
            return '';
        }

        $attribute = $product->getAttributes()['finishing'];
        $optionIds = explode(',', $finishing);
        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/finishing');

        // Must provide at least 4 options to select from. Otherwise available options will be used
        // and there is no need to show the select field.
        // If we're on the Edit Product page - show the field anyway.
        if (!$configValue && count($optionIds) < 4) {
            return '';
        }

        /** @var Select $select */
        $select = $this->getLayout()->createBlock(
            Select::class
        )->setData([
            'id' => 'select_' . $attribute->getAttributeCode(),
            // @TODO: find proper validation rule or add customer to have 3 options selected
            'class' => 'multiselect required product-custom-option admin__control-select',
            'name' => 'options[' . $attribute->getAttributeCode() . '][]'
        ]);

        $extraParams = ' multiple="multiple"';
        $select->setExtraParams($extraParams);

        foreach (explode(',', $finishing) as $optionId) {
            $select->addOption(
                $optionId,
                $attribute->getFrontend()->getOption($optionId)
            );
        }

        if ($configValue) {
            $select->setValue($configValue);
        }

        return $select->toHtml();
    }
}
