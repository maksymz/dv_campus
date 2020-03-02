<?php

declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Controller\Preferences;

use DvCampus\CustomerPreferences\Api\Data\PreferenceInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\ResultFactory;
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
     * @var \DvCampus\CustomerPreferences\Api\Data\PreferenceInterfaceFactory $preferenceFactory
     */
    private $preferenceFactory;

    /**
     * @var \DvCampus\CustomerPreferences\Model\PreferenceManagement $preferenceManagement
     */
    private $preferenceManagement;

    /**
     * @var \DvCampus\CustomerPreferences\Model\PreferenceRepository $preferenceRepository
     */
    private $preferenceRepository;

    /**
     * @var \Magento\Eav\Model\Config $eavConfig
     */
    private $eavConfig;

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
     * @param \DvCampus\CustomerPreferences\Api\Data\PreferenceInterfaceFactory $preferenceFactory
     * @param \DvCampus\CustomerPreferences\Model\PreferenceManagement $preferenceManagement
     * @param \DvCampus\CustomerPreferences\Model\PreferenceRepository $preferenceRepository
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \DvCampus\CustomerPreferences\Api\Data\PreferenceInterfaceFactory $preferenceFactory,
        \DvCampus\CustomerPreferences\Model\PreferenceManagement $preferenceManagement,
        \DvCampus\CustomerPreferences\Model\PreferenceRepository $preferenceRepository,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->preferenceFactory = $preferenceFactory;
        $this->preferenceManagement = $preferenceManagement;
        $this->preferenceRepository = $preferenceRepository;
        $this->eavConfig = $eavConfig;
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

                /** @var PreferenceInterface[] $preferences */
                $preferences = $this->preferenceManagement->getCustomerPreferences($customerId, $websiteId);

                /** @var PreferenceInterface $existingPreference */
                foreach ($preferences as $existingPreference) {
                    $preferencesByAttributeCode[$existingPreference->getAttributeCode()] = $existingPreference;
                }

                foreach ($this->getPreferencesFromRequest() as $attributeCode => $value) {
                    if (isset($preferencesByAttributeCode[$attributeCode])) {
                        $preference = $preferencesByAttributeCode[$attributeCode];

                        if ($preference->getPreferredValues() !== $value) {
                            if ($value) {
                                $preference->setPreferredValues($value);
                                $this->preferenceRepository->save($preference);
                            } else {
                                $this->preferenceRepository->delete($preference);
                            }
                        }
                    } elseif ($value) {
                        /** @var PreferenceInterface $preference */
                        $preference = $this->preferenceFactory->create();
                        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attributeCode);

                        $preference->setCustomerId($customerId)
                            ->setWebsiteId($websiteId)
                            ->setAttributeId((int) $attribute->getId())
                            ->setPreferredValues($value);
                        $this->preferenceRepository->save($preference);
                    }
                }

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
