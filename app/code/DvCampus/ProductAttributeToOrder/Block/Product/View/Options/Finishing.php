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

        /** @var Select $select */
        $select = $this->getLayout()->createBlock(
            Select::class
        )->setData([
            'id' => 'select_' . $attribute->getAttributeCode(),
            'class' => 'multiselect required product-custom-option admin__control-select',
            'name' => 'options[additional_' . $attribute->getAttributeCode() . '][]'
        ]);

        $extraParams = ' multiple="multiple"';
        $select->setExtraParams($extraParams);

        foreach (explode(',', $finishing) as $optionId) {
            $select->addOption(
                $optionId,
                $attribute->getFrontend()->getOption($optionId)
            );
        }

        return $select->toHtml();
    }
}
