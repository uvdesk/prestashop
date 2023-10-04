<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
class WkUvDeskTicketSystemCreateTicketModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if (isset($this->context->customer->id)
            && Configuration::get('WK_UVDESK_ACCESS_TOKEN')
            && Configuration::get('WK_UVDESK_COMPANY_DOMAIN')) {
            $objUvdesk = new WkUvdeskHelper();
            if (isset($objUvdesk->getFilteredData('type')->type)) {
                $ticketTypes = $objUvdesk->getFilteredData('type')->type;
                // Get Custom Fields if Plan allowed
                $objUvdesk = new WkUvdeskHelper();
                $customerAllFields = $objUvdesk->checkCustomFields();
                if ($customerAllFields && is_array($customerAllFields)) {
                    $nonDependentFields = [];
                    $customerActiveFields = [];
                    foreach ($customerAllFields as &$fields) {
                        if ($fields->status == 1
                        && ($fields->agentType == 'customer' || $fields->agentType == 'both')) {
                            $customerActiveFields[] = $fields;

                            // Get all non dependent custom fields
                            if (empty($fields->customFieldsDependency) && $fields->required == '1') {
                                $nonDependentFields[] = $fields->id;
                            }
                        }
                    }

                    if ($nonDependentFields) {
                        Media::addJsDef([
                            'nonDependentFields' => json_encode($nonDependentFields),
                        ]);
                    }

                    $this->context->smarty->assign('customerActiveFields', $customerActiveFields);
                }

                $this->context->smarty->assign('ticketTypes', $ticketTypes);
            }

            Media::addJsDef([
                'allowDatepicker' => 1,
            ]);

            $this->setTemplate('module:wkuvdeskticketsystem/views/templates/front/createticket.tpl');
        } else {
            Tools::redirect(
                'index.php?controller=authentication&back=' . urlencode(
                    $this->context->link->getModuleLink('wkuvdeskticketsystem', 'createticket')
                )
            );
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('createTicket')) {
            $customerName = $this->context->customer->firstname . ' ' . $this->context->customer->lastname;
            $customerEmail = $this->context->customer->email;
            $subject = trim(Tools::getValue('subject'));
            $reply = trim(Tools::getValue('message'));
            $ticketTypeExist = Tools::getValue('ticketTypeExist');
            $ticketType = Tools::getValue('type');
            if ($ticketTypeExist && !$ticketType) {
                $this->errors[] = $this->module->l('Ticket Type is required field.', 'createticket');
            }
            if (!$subject) {
                $this->errors[] = $this->module->l('Subject is required field.', 'createticket');
            } elseif (!Validate::isName($subject)) {
                $this->errors[] = $this->module->l('Please provide a valid subject.', 'createticket');
            }

            if (!$reply) {
                $this->errors[] = $this->module->l('Message is required field.', 'createticket');
            } elseif (!Validate::isMessage($reply)) {
                $this->errors[] = $this->module->l('Please provide a valid message.', 'createticket');
            }

            // Custom Field validation
            $fieldRequired = false;
            $customFields = Tools::getValue('customFields');
            $requiedCustomFields = Tools::getValue('requiedCustomFields');
            if ($requiedCustomFields && $customFields) {
                $requiedCustomFields = explode(',', $requiedCustomFields);

                foreach ($requiedCustomFields as $reqFieldId) {
                    if (isset($customFields[$reqFieldId])) {
                        if (!$customFields[$reqFieldId]) {
                            $fieldRequired = true;
                        }
                    } else {
                        // If custom file field is required
                        if (isset($_FILES['customFields']['tmp_name'][$reqFieldId])
                        && $_FILES['customFields']['tmp_name'][$reqFieldId] == '') {
                            $fieldRequired = true;
                        }
                    }
                }
            }

            $objUvdesk = new WkUvdeskHelper();
            $customerAllFields = $objUvdesk->checkCustomFields();
            if ($customerAllFields && is_array($customerAllFields)) {
                foreach ($customerAllFields as $fields) {
                    if (isset($customFields[$fields->id]) && trim($customFields[$fields->id])) {
                        $field = trim($customFields[$fields->id]);
                        $type = $fields->fieldType;
                        if ($type == 'text' || $type == 'textarea') {
                            if (!Validate::isMessage($field)) {
                                $this->errors[] = $this->module->l(sprintf('Please provide valid %s.', $fields->name), 'createticket');
                            }
                        } elseif ($type == 'date') {
                            if (!Validate::isDate($field)) {
                                $this->errors[] = $this->module->l(sprintf('Please provide valid %s.', $fields->name), 'createticket');
                            }
                        } elseif ($type == 'time') {
                            $regex = '/^(0?[1-9]|1[012])(:[0-5]\d) [APap][mM]$/';
                            if (!preg_match($regex, $field)) {
                                $this->errors[] = $this->module->l(sprintf('Please provide valid %s.', $fields->name), 'createticket');
                            }
                        }
                    }
                }
            }

            if ($fieldRequired) {
                $this->errors[] = $this->module->l('Fill all mandatory fields.', 'createticket');
            }

            if (empty($this->errors)) {
                $data = [
                    'name' => $customerName,
                    'from' => $customerEmail,
                    'subject' => $subject,
                    'reply' => $reply,
                    'type' => $ticketType,
                    'customFields' => $customFields,
                    'actAsType' => 'customer',
                    'actAsEmail' => $customerEmail,
                ];

                $objUvdesk = new WkUvdeskHelper();
                $tickets = $objUvdesk->createTicket($data);
                if ($tickets && isset($tickets->ticketId) && $tickets->ticketId) {
                    Tools::redirect(
                        $this->context->link->getModuleLink(
                            'wkuvdeskticketsystem',
                            'customerticketlist',
                            ['created' => 1]
                        )
                    );
                } elseif (isset($tickets->error)) {
                    if (isset($tickets->error_description)) {
                        $this->errors[] = $tickets->error_description;
                    } else {
                        $this->errors[] = $tickets->error;
                    }
                } else {
                    $this->errors[] = $this->module->l('Some error occured', 'createticket');
                }
            }
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Ticket List', 'customerticketlist'),
            'url' => $this->context->link->getModuleLink('wkuvdeskticketsystem', 'customerticketlist'),
        ];

        $breadcrumb['links'][] = [
            'title' => $this->module->l('Create Ticket', 'createticket'),
            'url' => '',
        ];

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryUI('ui.datepicker');
        $this->addJqueryUI(['ui.slider', 'ui.datepicker']);
        $this->context->controller->registerJavascript(
            'wk_timepicker-js',
            'js/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js',
            ['position' => 'bottom', 'priority' => 1000]
        );

        $this->registerStylesheet(
            'uvdeskticketlist-css',
            'modules/' . $this->module->name . '/views/css/uvdeskticketlist.css'
        );
        $this->registerJavascript(
            'uvdeskticketlist-js',
            'modules/' . $this->module->name . '/views/js/uvdeskticketlist.js'
        );
    }
}
