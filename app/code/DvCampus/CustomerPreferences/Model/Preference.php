<?php
declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Model;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;

/**
 * @method int getPreferenceId()
 * @method $this setPreferenceId(int $preferenceId)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $customerId)
 * @method int getAttributeId()
 * @method $this setAttributeId(int|string $productAttributeId) - set attribute id or code that will be converted to ID
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $websiteId)
 * @method string getPreferredValues()
 * @method $this setPreferredValues(string $preferredValues)
 */
class Preference extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Eav\Model\Config $eavConfig
     */
    private $eavConfig;

    /**
     * Preference constructor.
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->eavConfig = $eavConfig;
    }

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(\DvCampus\CustomerPreferences\Model\ResourceModel\Preference::class);
    }

    /**
     * Validate customer_id, website_id and attribute_id before saving data
     * Do not fill in data automatically not to break install scripts or import/export that may work from the
     * crontab area or from the CLI - e.g. follow the "Let it fail" principle
     *
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave(): self
    {
        // Allow changing data before save
        parent::beforeSave();

        // @TODO: see the AbstractModel::validateBeforeSave() method and its' implementation for better implementation
        $this->validate();

        return $this;
    }

    /**
     * Ensure that attribute_id is set and is a product attribute.
     * Allow overriding this method via plugins by making it public.
     * This method must be called in the ::beforeSave() method to guarantee that it is executed.
     * Moving the call to a controller means that somebody will forget to do the same in other place.
     * Write error-prone code!
     *
     * @throws LocalizedException
     */
    public function validate(): void
    {
        if (!$this->getCustomerId()) {
            throw new LocalizedException(__('Can\'t save customer preferences: %s is not set.', 'customer_id'));
        }

        if (!$this->getWebsiteId()) {
            throw new LocalizedException(__('Can\'t save customer preferences: %s is not set.', 'website_id'));
        }

        if (!$this->getAttributeId()) {
            throw new LocalizedException(__('Can\'t save customer preferences: %s is not set.', 'attribute_id'));
        }

        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $this->getAttributeId());

        if ($attribute->getId()) {
            $this->setAttributeId((int) $attribute->getId());
        } else {
            throw new LocalizedException(__('Attribute with ID %s is not a product attribute.'));
        }
    }

    /**
     * @return mixed|string
     * @throws LocalizedException
     */
    public function getAttributeCode(): ?string
    {
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $this->getAttributeId());
        return $attribute->getAttributeCode();
    }
}
