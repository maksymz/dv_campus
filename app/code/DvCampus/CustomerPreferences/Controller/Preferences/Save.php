<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Controller\Preferences;

use DvCampus\CustomerPreferences\Model\Preference;
use DvCampus\CustomerPreferences\Model\ResourceModel\Preference\Collection as PreferenceCollection;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    public const XML_PATH_ENABLED = 'dvcampus_customer_preferences/general/enabled';

    public const XML_PATH_ALLOW_FOR_GUESTS = 'dvcampus_customer_preferences/general/allow_for_guests';

    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
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
     * @var \Psr\Log\LoggerInterface $logger
     */
    private $logger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigs
     */
    private $scopeConfig;

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
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
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
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
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
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        // @TODO: merge with customer preferences on login or not? Maybe merge only if empty?
        // Every fail should be controlled
        try {
            if (!$this->validateRequest()) {
                throw new LocalizedException(__('Unable to save preferences.'));
            }

            $websiteId = (int) $this->storeManager->getWebsite()->getId();

            if ($this->customerSession->isLoggedIn()) {
                $customerId = (int) $this->customerSession->getId();
                $preferencesByAttributeCode = [];

                /** @var PreferenceCollection $preferenceCollection */
                $preferenceCollection = $this->preferenceCollectionFactory->create();
                $preferenceCollection->addCustomerFilter($customerId)
                    ->addWebsiteFilter($websiteId);

                /** @var Preference $existingPreference */
                foreach ($preferenceCollection as $existingPreference) {
                    $preferencesByAttributeCode[$existingPreference->getAttributeCode()] = $existingPreference;
                }

                /** @var Transaction $saveTransaction */
                $saveTransaction = $this->transactionFactory->create();
                /** @var Transaction $deleteTransaction */
                $deleteTransaction = $this->transactionFactory->create();

                foreach ($this->getPreferencesFromRequest() as $attributeCode => $value) {
                    if (isset($preferencesByAttributeCode[$attributeCode])) {
                        $preference = $preferencesByAttributeCode[$attributeCode];

                        if ($preference->getPreferredValues() !== $value) {
                            if ($value) {
                                $preference->setPreferredValues($value);
                                $saveTransaction->addObject($preference);
                            } else {
                                $deleteTransaction->addObject($preference);
                            }
                        }
                    } elseif ($value) {
                        /** @var Preference $preference */
                        $preference = $this->preferenceFactory->create();

                        $preference->setCustomerId($customerId)
                            ->setWebsiteId($websiteId)
                            ->setAttributeId($attributeCode)
                            ->setPreferredValues($value);
                        $saveTransaction->addObject($preference);
                    }
                }

                $saveTransaction->save();
                $deleteTransaction->delete();
                $message = __('Your preferences have been updated.');
            } else {
                $preferencesByAttributeCode = array_merge(
                    $this->customerSession->getData('customer_preferences') ?? [],
                    $this->getPreferencesFromRequest()
                );

                $preferencesByAttributeCode = array_filter($preferencesByAttributeCode, static function ($value) {
                    return $value || $value === '0';
                });

                $this->customerSession->setCustomerPreferences($preferencesByAttributeCode);
                $message = __('Your preferences have been updated. Please, log in to save them permanently.');
            }
        } catch (LocalizedException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $message = __('Your preferences can\'t be saved. Please, contact us if you see this message.');
        }

        /** @var JsonResult $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $response->setData([
            'message' => $message
        ]);

        return $response;
    }

    /**
     * @return bool
     */
    private function validateRequest(): bool
    {
        $request = $this->getRequest();
        $allowSavingPreferences = true;

        if (!$this->formKeyValidator->validate($request)) {
            // This message is translated in the module Magento_Checkout
            $allowSavingPreferences = false;
        }

        if (!$this->scopeConfig->getValue(self::XML_PATH_ENABLED)
            || (!$this->customerSession->isLoggedIn() && !$this->scopeConfig->getValue(self::XML_PATH_ALLOW_FOR_GUESTS))
        ) {
            $allowSavingPreferences = false;
        }

        $eventParameters = [
            'allow_saving_preferences' => $allowSavingPreferences
        ];
        $this->_eventManager->dispatch('dvcampus_customer_preferences_allow_save', $eventParameters);

        return $allowSavingPreferences;
    }

    /**
     * @return array
     */
    private function getPreferencesFromRequest(): array
    {
        $preferencesByAttributeCode = [];

        foreach ($this->getRequest()->getParam('attributes') as $data) {
            $preferencesByAttributeCode[$data['attribute_code']] = trim($data['value']) ?? '';
        }

        return $preferencesByAttributeCode;
    }
}
