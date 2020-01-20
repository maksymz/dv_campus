<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Controller\Preferences;

use DvCampus\CustomerPreferences\Model\Preference;
use DvCampus\CustomerPreferences\Model\ResourceModel\Preference\Collection as PreferenceCollection;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

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
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \DvCampus\CustomerPreferences\Model\PreferenceFactory $preferenceFactory
     * @param \DvCampus\CustomerPreferences\Model\ResourceModel\Preference\CollectionFactory $preferenceCollectionFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \DvCampus\CustomerPreferences\Model\PreferenceFactory $preferenceFactory,
        \DvCampus\CustomerPreferences\Model\ResourceModel\Preference\CollectionFactory $preferenceCollectionFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
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
        // @TODO: merge with customer preferences on login or not? Maybe merge only if empty?
        $request = $this->getRequest();

        // Every fail should be controlled
        try {
            if (!$this->formKeyValidator->validate($request)) {
                // This message is translated in the module Magento_Checkout
                throw new LocalizedException(__('Your session has expired'));
            }

            $customerId = (int) $this->customerSession->getId();
            $websiteId = (int) $this->storeManager->getWebsite()->getId();

            if ($customerId) {
                $preferencesByAttributeCode = [];

                /** @var PreferenceCollection $preferenceCollection */
                $preferenceCollection = $this->preferenceCollectionFactory->create();
                $preferenceCollection->addCustomerFilter($customerId)
                    ->addWebsiteFilter($websiteId);

                /** @var Preference $existingPreference */
                foreach ($preferenceCollection as $existingPreference) {
                    $preferencesByAttributeCode[$existingPreference->getAttributeCode()] = $existingPreference;
                }

                /** @var Transaction $transaction */
                $transaction = $this->transactionFactory->create();

                foreach ($request->getParam('attributes') as $attributeCode => $value) {
                    if (isset($preferencesByAttributeCode[$attributeCode])) {
                        $preference = $preferencesByAttributeCode[$attributeCode];

                        if ($preference->getPreferredValues() !== $value) {
                            $preference->setPreferredValues($value);
                            $transaction->addObject($preference);
                        }
                    } else {
                        /** @var Preference $preference */
                        $preference = $this->preferenceFactory->create();

                        $preference->setCustomerId($customerId)
                            ->setWebsiteId($websiteId)
                            ->setAttributeId($attributeCode)
                            ->setPreferredValues($value);
                        $transaction->addObject($preference);
                    }
                }

                $transaction->save();
                $message = __('Your preferences have been updated.');
            } else {
                $preferencesByAttributeCode = array_merge(
                    $this->customerSession->getData('customer_preferences') ?? [],
                    $request->getParam('attributes')
                );
                $this->customerSession->setCustomerPreferences($preferencesByAttributeCode);
                $message = __('Your preferences have been updated. Please, log in to save them permanently.');
            }
        } catch (LocalizedException $e) {
            $message = $e->getMessage();
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
