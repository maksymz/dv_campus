<?php
declare(strict_types=1);

namespace DvCampus\Catalog\Block\Product\View;

use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class Demo
 * @package DvCampus\Catalog\Block\Product\View
 *
 * @method int getIntVariable()
 * @method string getStringVariable()
 */
class Demo extends \Magento\Catalog\Block\Product\View
{
    /**
     * @TODO: get real product qty here and change it when simple product is selected in configurable
     * @return float
     */
    public function getProductSalableQty(): float
    {
        return $this->getProduct()->getQty();
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->getProduct()->getName();
    }
}
