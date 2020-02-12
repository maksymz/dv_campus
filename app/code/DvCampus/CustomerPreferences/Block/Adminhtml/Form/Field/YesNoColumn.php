<?php

declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Block\Adminhtml\Form\Field;

class YesNoColumn extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value): self
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    /**
     * @return array
     */
    private function getSourceOptions(): array
    {
        return [
            ['label' => __('Yes'), 'value' => '1'],
            ['label' => __('No'), 'value' => '0'],
        ];
    }
}
