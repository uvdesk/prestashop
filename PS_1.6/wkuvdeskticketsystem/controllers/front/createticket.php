<?php
/**
* 2010-2017 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
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
                //Get Custom Fields if Plan allowed
                $objUvdesk = new WkUvdeskHelper();
                $customerAllFields = $objUvdesk->checkCustomFields();
                if ($customerAllFields) {
                    $nonDependentFields = array();
                    $customerActiveFields = array();
                    foreach ($customerAllFields as &$fields) {
                        if ($fields->status == 1 && ($fields->agentType == 'customer' || $fields->agentType == 'both')) {
                            $customerActiveFields[] = $fields;

                            //Get all non dependent custom fields
                            if (empty($fields->customFieldsDependency) && $fields->required == '1') {
                                $nonDependentFields[] = $fields->id;
                            }
                        }
                    }

                    if ($nonDependentFields) {
                        Media::addJsDef(array(
                            'nonDependentFields' => Tools::jsonEncode($nonDependentFields),
                        ));
                    }
                    
                    $this->context->smarty->assign('customerActiveFields', $customerActiveFields);
                }

                $this->context->smarty->assign('ticketTypes', $ticketTypes);
            }

            Media::addJsDef(array(
                'allowDatepicker' => 1,
            ));
            
            $this->setTemplate('createticket.tpl');
        } else {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('wkuvdeskticketsystem', 'createticket')));
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('createTicket')) {
            $customerName = $this->context->customer->firstname.' '.$this->context->customer->lastname;
            $customerEmail = $this->context->customer->email;
            $subject = Tools::getValue('subject');
            $reply = Tools::getValue('message');
            $ticketTypeExist = Tools::getValue('ticketTypeExist');
            $ticketType = Tools::getValue('type');

            if ($ticketTypeExist && !$ticketType) {
                $this->errors[] = $this->module->l('Ticket Type is required field.', 'createticket');
            }
            if (!$subject) {
                $this->errors[] = $this->module->l('Subject is required field.', 'createticket');
            }
            if (!$reply) {
                $this->errors[] = $this->module->l('Message is required field.', 'createticket');
            }
            
            //Custom Field validation
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
                        //If custom file field is required
                        if (isset($_FILES['customFields']['tmp_name'][$reqFieldId]) && $_FILES['customFields']['tmp_name'][$reqFieldId] == '') {
                            $fieldRequired = true;
                        }
                    }
                }
            }

            if ($fieldRequired) {
                $this->errors[] = $this->module->l('Fill all mandatory fields.', 'createticket');
            }

            if (empty($this->errors)) {
                $data = array(
                    'name'    => $customerName,
                    'from'    => $customerEmail,
                    'subject' => $subject,
                    'reply'   => $reply,
                    'type'    => $ticketType,
                    'customFields' => $customFields,
                );

                $objUvdesk = new WkUvdeskHelper();
                $tickets = $objUvdesk->createTicket($data);
                if ($tickets && isset($tickets->ticketId) && $tickets->ticketId) {
                    Tools::redirect($this->context->link->getModuleLink('wkuvdeskticketsystem', 'customerticketlist', array('created' => 1)));
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

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryUI('ui.datepicker');
        $this->addJqueryUI(array('ui.slider', 'ui.datepicker'));
        $this->addJS(_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');

        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/uvdeskticketlist.css');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/uvdeskticketlist.js');
    }
}
