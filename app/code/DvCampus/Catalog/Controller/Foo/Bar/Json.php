<?php
declare(strict_types=1);

namespace DvCampus\Catalog\Controller\Foo\Bar;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json as JsonResult;

class Json extends \Magento\Framework\App\Action\Action
{
    /**
     * @inheritDoc
     */
    public function execute(): JsonResult
    {
        /** @var Http $request */
        $request = $this->getRequest();

        /** @var JsonResult $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $response->setData([
            'param-1' => $request->getParam('param-1'),
            'param-2' => $request->getParam('param-2', 'No second parameter')
        ]);

        return $response;
    }
}
