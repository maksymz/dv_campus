<?php
declare(strict_types=1);

namespace DvCampus\Catalog\Controller\FooFoo\Bar;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

class HtmlResponse extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpGetActionInterface
{
    /**
     * @inheritDoc
     * https://maksym-zaporozhets.local/some-pretty-url/fooFoo_bar/htmlResponse
     */
    public function execute()
    {
        /** @var Page $response */
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
