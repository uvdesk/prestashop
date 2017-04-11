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

            if (!$subject) {
                $this->errors[] = $this->module->l('Subject is required field.', 'createticket');
            }
            if (!$reply) {
                $this->errors[] = $this->module->l('Message is required field.', 'createticket');
            }

            if (empty($this->errors)) {
                $data = array(
                    'name'    => $customerName,
                    'from'    => $customerEmail,
                    'subject' => $subject,
                    'reply'   => $reply,
                    'type'    => '1', //1|2|3|4|5|6 for open|pending|resolved|closed|Spam|Answered repectively
                );

                $objUvdesk = new WkUvdeskHelper();
                $tickets = $objUvdesk->createTicket($data);
                if ($tickets && isset($tickets->ticketId) && $tickets->ticketId) {
                    Tools::redirect($this->context->link->getModuleLink('wkuvdeskticketsystem', 'customerticketlist', array('created' => 1)));
                }
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/uvdeskticketlist.css');
    }
}
