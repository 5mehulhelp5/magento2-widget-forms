<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Model\Attribute\DefaultValueProvider;

use Alekseon\AlekseonEav\Model\Attribute;
use Alekseon\AlekseonEav\Model\Attribute\DefaultValueProvider\AbstractProvider;

/**
 * Class CustomerEmail
 * @package Alekseon\WidgetForms\Model\Attribute\DefaultValueProvider
 */
class CustomerEmail extends AbstractProvider
{
    /**
     * @var string
     */
    protected $backendModelMode = Attribute\Backend\DefaultValue::MODE_FORCE_SET;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * CustomerEmail constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        $this->customerSession = $customerSession;
        parent::__construct($data);
    }

    /**
     * @param Attribute $attribute
     * @return bool|void
     */
    public function canBeUsedForAttribute(Attribute $attribute)
    {
        if (parent::canBeUsedForAttribute($attribute)) {
            if ($attribute->getForm()
                && $attribute->getForm()->getCanUseForWidget()
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Attribute\Backend\DefaultValue
     */
    public function getBackendModel()
    {
        $backendModel = parent::getBackendModel();
        if ($backendModel && $this->customerSession->getCustomer()) {
            $backendModel->setDefaultValue($this->customerSession->getCustomer()->getEmail());
        }
        return $backendModel;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return '';
    }

    /**
     * @return bool
     */
    public function hasValue()
    {
        if ($this->customerSession->getCustomer()->getGroupId()) {
            return true;
        }
        return parent::hasValue();
    }
}
