<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Controller\Preferences;

use DvCampus\CustomerPreferences\Model\Preference;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DB\Transaction;

class Save extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @var \DvCampus\CustomerPreferences\Model\PreferenceFactory $preferenceFactory
     */
    private $preferenceFactory;

    /**
     * @var \Magento\Framework\DB\TransactionFactory $transactionFactory
     */
    private $transactionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private $storeManager;

    /**
     * Save constructor.
     * @param \DvCampus\CustomerPreferences\Model\PreferenceFactory $preferenceFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \DvCampus\CustomerPreferences\Model\PreferenceFactory $preferenceFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->preferenceFactory = $preferenceFactory;
        $this->transactionFactory = $transactionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        // @TODO: implement security layer when we get back to JS
        // @TODO: save data to customer session for guests
        /** @var Transaction $transaction */
        $transaction = $this->transactionFactory->create();

        // Every fail should be controlled
        try {
            foreach ($this->getRequest()->getParam('attributes') as $attributeCode => $value) {
                /** @var Preference $preference */
                $preference = $this->preferenceFactory->create();

                // @TODO: get `customer_id` from the session
                $preference->setCustomerId(1)
                    ->setWebsiteId((int) $this->storeManager->getWebsite()->getId())
                    ->setAttributeId($attributeCode)
                    ->setPreferredValues($value);

                $transaction->addObject($preference);
            }

            $transaction->save();
            $message = __('Saved!');
        } catch (\Exception $e) {
            $message = __('Your preferences can\'t be saved. Please, contact us if ypu see this message.');
        }

        /** @var JsonResult $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $response->setData([
            'message' => $message
        ]);

        return $response;
    }
}
