<?php
declare(strict_types=1);

namespace DvCampus\Catalog\ViewModel;

use Magento\Framework\Data\Collection;

class Product implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return Collection
     */
    public function getProductCategoriesCollection(\Magento\Catalog\Model\Product $product): Collection
    {
        return $product->getCategoryCollection()
            ->addAttributeToSelect('name')
            ->addFieldToFilter('level', ['gteq' => 2]);
    }
}
