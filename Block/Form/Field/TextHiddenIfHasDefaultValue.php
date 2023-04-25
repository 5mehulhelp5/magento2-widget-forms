<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Block\Form\Field;

/**
 * Class TextHiddenIfHasDefaultValue
 * @package Alekseon\WidgetForms\Block\Form\Field
 */
class TextHiddenIfHasDefaultValue extends \Alekseon\WidgetForms\Block\Form\Field\Text
{
     /**
     * @return string
     */
    public function getTemplate()
    {
        if ($this->getField()->hasDefaultValue()) {
            return $this->getHiddenTemplate();
        } else {
            return parent::getTemplate();
        }
    }
}
