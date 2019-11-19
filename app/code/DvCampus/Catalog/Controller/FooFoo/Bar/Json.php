<?php
declare(strict_types=1);

namespace DvCampus\Catalog\Controller\FooFoo\Bar;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json as JsonResult;

class Json extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpGetActionInterface
{
    /**
     * @inheritDoc
     * https://maksym-zaporozhets.local/some-pretty-url/fooFoo_bar/json/string_parameter/some%20string/integer_value/12
     */
    public function execute()
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();

        /** @var JsonResult $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $response->setData([
            'String value from request' => $request->getParam('string_parameter'),
            'Integer value from request' => (int) $request->getParam('integer_value'),
            'Product weight' => (float) $request->getParam('product_weight', 1)
        ]);

        return $response;
    }
}
