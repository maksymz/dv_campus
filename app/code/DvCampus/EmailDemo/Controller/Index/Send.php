<?php

declare(strict_types=1);

namespace DvCampus\EmailDemo\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;

class Send extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpGetActionInterface
{
    /**
     * @var \DvCampus\EmailDemo\Model\Email $email
     */
    private $email;

    /**
     * Send constructor.
     * @param \DvCampus\EmailDemo\Model\Email $email
     * @param Context $context
     */
    public function __construct(
        \DvCampus\EmailDemo\Model\Email $email,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->email = $email;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     * https://maksym-zaporozhets.local/email-demo/index/send
     */
    public function execute()
    {
        $this->email->send();
        /** @var Raw $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setContents('');

        return $result;
    }
}
