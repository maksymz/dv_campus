<?php
declare(strict_types=1);

namespace DvCampus\Catalog\Controller\FooFoo\Bar;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect;

class RedirectExample extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpGetActionInterface
{
    /**
     * @inheritDoc
     * https://maksym-zaporozhets.local/some-pretty-url/fooFoo_bar/redirectExample
     */
    public function execute()
    {

        /** @var Redirect $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $response->setHttpResponseCode(301);
        $response->setPath(
            'dv_campus_catalog/fooFoo_bar/json',
            [
                '_secure' => true,
                'string_parameter' => 'Redirect from another controller',
                'integer_value' => 301,
                'product_weight' => 1.12
            ]
        );

        return $response;
    }
}
