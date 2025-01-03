<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Block\Adminhtml\Form\Edit\Tab;

use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\InputType;
use Alekseon\AlekseonEav\Api\Data\EntityInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class General
 * @package Alekseon\CustomFormsBuilder\Block\Adminhtml\Form\Edit\Tab
 */
class WidgetSettings extends \Alekseon\AlekseonEav\Block\Adminhtml\Entity\Edit\Form implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Widget Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Widget Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return (bool) $this->getDataObject()->getCanUseForWidget();
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getDataObject()
    {
        return $this->_coreRegistry->registry('current_form');
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $dataObject = $this->getDataObject();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $widgetFieldset = $form->addFieldset('widget_settings_fieldset', ['legend' => __('Widget Settings')]);
        $this->addAllAttributeFields($widgetFieldset, $dataObject, ['included' => ['widget_form_attribute']]);
        $this->setForm($form);

        return parent::_prepareForm();
    }


    /**
     * Initialize form fileds values
     *
     * @return $this
     */
    protected function _initFormValues()
    {
        $this->getForm()->addValues($this->getDataObject()->getData());
        return parent::_initFormValues();
    }


    /**
     * @inheritDoc
     */
    protected function _addAdditionalFormElementData(AbstractElement $element)
    {
        if (in_array($element->getId(), ['form_submit_success_message', 'form_submit_success_title'])) {
            $element->setNote(
                '<a href="https://github.com/Alekseon/magento2-widget-forms/wiki/Template-Variables" target="_blank">'
                . __('Template Variables')
                . ' </a>'
                . __( ' are allowed.'));
        }

        return parent::_addAdditionalFormElementData($element);
    }
}
