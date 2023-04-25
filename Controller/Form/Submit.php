<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Controller\Form;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Submit
 * @package Alekseon\WidgetForms\Controller
 */
class Submit extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var \Alekseon\CustomFormsBuilder\Model\FormRepository
     */
    protected $formRepository;
    /**
     * @var \Alekseon\CustomFormsBuilder\Model\FormRecordFactory
     */
    protected $formRecordFactory;
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Submit constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Alekseon\CustomFormsBuilder\Model\FormRepository $formRepository,
        \Alekseon\CustomFormsBuilder\Model\FormRecordFactory $formRecordFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->formRecordFactory = $formRecordFactory;
        $this->jsonFactory = $jsonFactory;
        $this->formRepository = $formRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();

        try {
            $form = $this->getForm();
            $this->validateData();
            $post = $this->getRequest()->getPost();
            $formRecord = $this->formRecordFactory->create();
            $formRecord->getResource()->setCurrentForm($form);
            $formRecord->setStoreId($form->getStoreId());
            $formRecord->setFormId($form->getId());
            $formFields = $form->getFieldsCollection();
            foreach ($formFields as $field) {
                $fieldCode = $field->getAttributeCode();
                if (isset($post[$fieldCode])) {
                    $value = $post[$fieldCode];
                } else {
                    $value = $field->getDefaultValue();
                }
                $formRecord->setData($fieldCode, $value);
            }

            $formRecord->getResource()->save($formRecord);
            $this->_eventManager->dispatch('alekseon_widget_form_after_submit', ['form_record' => $formRecord]);
            $resultJson->setData(
                [
                    'title' => $this->getSuccessTitle($formRecord),
                    'message' => $this->getSuccessMessage($formRecord),
                ]
            );
        } catch (LocalizedException $e) {
            $resultJson->setHttpResponseCode(500);
            $resultJson->setData(
                [
                    'message' => $e->getMessage()
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error('Widget Form Error during submit action: ' . $e->getMessage());
            $resultJson->setHttpResponseCode(500);
            $resultJson->setData(
                [
                    'message' => __('We are unable to process your request. Please, try again later.'),
                ]
            );
        }

        return $resultJson;
    }

    /**
     * @param $form
     */
    public function getSuccessMessage($formRecord)
    {
        $successMessage = $formRecord->getForm()->getFormSubmitSuccessMessage();
        if (!$successMessage) {
            $successMessage = __('Thank You!');
        }
        return $successMessage;
    }

    /**
     * @param $form
     */
    public function getSuccessTitle($formRecord)
    {
        $successTitle = $formRecord->getForm()->getFormSubmitSuccessTitle();
        if (!$successTitle) {
            $successTitle = __('Success');
        }
        return $successTitle;
    }

    /**
     *
     */
    protected function validateData()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            throw new \Exception(__('Incorrect Form Key'));
        }

        if ($this->getRequest()->getParam('hideit')) {
            throw new \Exception(__('Interrupted Data'));
        }
    }

    /**
     *
     */
    public function getForm()
    {
        $formId = $this->getRequest()->getParam('form_id');
        $form = $this->formRepository->getById($formId);
        return $form;
    }
}
