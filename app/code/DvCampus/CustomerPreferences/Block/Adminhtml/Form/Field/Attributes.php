<?php

declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Block\Adminhtml\Form\Field;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class Attributes extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var YesNoColumn $optionsRenderer
     */
    private $optionsRenderer;

    /**
     * @inheritDoc
     */
    protected $_template = 'DvCampus_CustomerPreferences::system/config/form/field/array.phtml';

    /**
     * Prepare to render
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareToRender(): void
    {
        // @TODO: make these inputs disabled
        $this->addColumn(
            'attribute_code',
            [
                'label' => __('Attribute Code'),
                'class' => 'required-entry'
            ]
        );

        $this->addColumn(
            'enabled',
            [
                'label' => __('Enabled'),
                'renderer' => $this->getOptionsRenderer(),
                'class' => 'required-entry'
            ]
        );

        $this->_addAfter = false;
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $optionValue = $row->getData('enabled');

        if ($optionValue !== null) {
            $options['option_' . $this->getOptionsRenderer()->calcOptionHash($optionValue)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return YesNoColumn
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOptionsRenderer(): YesNoColumn
    {
        if (!$this->optionsRenderer) {
            $this->optionsRenderer = $this->getLayout()->createBlock(
                YesNoColumn::class,
                '',
                [
                    'data' => [
                        'is_render_to_js_template' => true,
                        'class' => 'input-text required-entry validate-no-empty',
                    ],
                ]
            );
        }

        return $this->optionsRenderer;
    }
}
