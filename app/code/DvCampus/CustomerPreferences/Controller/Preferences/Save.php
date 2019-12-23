<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Controller\Preferences;

use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\ResultFactory;

class Save extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var JsonResult $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $response->setData([
            'message' => 'Saved!'
        ]);

        return $response;
    }
}
