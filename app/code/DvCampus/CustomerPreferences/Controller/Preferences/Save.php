<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Controller\Preferences;

use DvCampus\CustomerPreferences\Model\Preference;
use DvCampus\CustomerPreferences\Model\ResourceModel\Preference\Collection as PreferenceCollection;
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
     * @var \DvCampus\CustomerPreferences\Model\ResourceModel\Preference\CollectionFactory $preferenceCollectionFactory
     */
    private $preferenceCollectionFactory;

    /**
     * @var \Magento\Framework\DB\TransactionFactory $transactionFactory
     */
    private $transactionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Save constructor.
     * @param \DvCampus\CustomerPreferences\Model\PreferenceFactory $preferenceFactory
     * @param \DvCampus\CustomerPreferences\Model\ResourceModel\Preference\CollectionFactory $preferenceCollectionFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \DvCampus\CustomerPreferences\Model\PreferenceFactory $preferenceFactory,
        \DvCampus\CustomerPreferences\Model\ResourceModel\Preference\CollectionFactory $preferenceCollectionFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->preferenceFactory = $preferenceFactory;
        $this->preferenceCollectionFactory = $preferenceCollectionFactory;
        $this->transactionFactory = $transactionFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
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
            $websiteId = (int) $this->storeManager->getWebsite()->getId();
            $preferencesByAttributeCode = [];

            /** @var PreferenceCollection $preferenceCollection */
            $preferenceCollection = $this->preferenceCollectionFactory->create();
            // @TODO: get `customer_id` from the session
            $preferenceCollection->addCustomerFilter(1)
                ->addWebsiteFilter($websiteId);

            /** @var Preference $existingPreference */
            foreach ($preferenceCollection as $existingPreference) {
                $preferencesByAttributeCode[$existingPreference->getAttributeCode()] = $existingPreference;
            }

            foreach ($this->getRequest()->getParam('attributes') as $attributeCode => $value) {
                if (isset($preferencesByAttributeCode[$attributeCode])) {
                    $preference = $preferencesByAttributeCode[$attributeCode];

                    if ($preference->getPreferredValues() !== $value) {
                        $preference->setPreferredValues($value);
                        $transaction->addObject($preference);
                    }
                } else {
                    /** @var Preference $preference */
                    $preference = $this->preferenceFactory->create();

                    // @TODO: get `customer_id` from the session
                    $preference->setCustomerId(1)
                        ->setWebsiteId($websiteId)
                        ->setAttributeId($attributeCode)
                        ->setPreferredValues($value);
                    $transaction->addObject($preference);
                }
            }

            $transaction->save();
            $message = __('Saved!');
        } catch (\Exception $e) {
            $this->logger->critical($e);
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
