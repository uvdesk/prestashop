<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkUvDeskTicketSystemViewTicketModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if (isset($this->context->customer->id)
            && Configuration::get('WK_UVDESK_ACCESS_TOKEN')
            && Configuration::get('WK_UVDESK_COMPANY_DOMAIN')) {
            $objUvdesk = new WkUvdeskHelper();
            $uvdeskCustomers = $objUvdesk->getCustomerByEmail($this->context->customer->email);
            if (isset($uvdeskCustomers->customers[0]) && $uvdeskCustomers->customers[0]) {
                if ($uvdeskCustomerId = $uvdeskCustomers->customers[0]->id) {
                    if ($incrementId = Tools::getValue('id')) { //Increment Id is a ticket id for a particular company
                        $ticketDetail = $objUvdesk->getTicket($incrementId);
                        if (isset($ticketDetail->ticket->customer->id)
                        && ($ticketDetail->ticket->customer->id == $uvdeskCustomerId)) {
                            $this->context->smarty->assign(array(
                                'incrementId' => $incrementId,
                                'ticket' => $ticketDetail->ticket,
                                'ticketId' => $ticketDetail->ticket->id,
                                'ticketTotalThreads' => $ticketDetail->ticketTotalThreads,
                                'ticket_reply' => $ticketDetail->createThread->reply,
                                'attachments' => $ticketDetail->createThread->attachments,
                                'self' => dirname(__FILE__),
                            ));

                            //Get custom fields of this ticket
                            if (isset($ticketDetail->ticket->customFieldValues)) {
                                $customFieldValues = WkUvdeskHelper::storeTicketCustomFieldValues(
                                    $ticketDetail->ticket->customFieldValues
                                );

                                if ($customFieldValues) {
                                    $this->context->smarty->assign('customFieldValues', $customFieldValues);
                                }
                            }

                            Media::addJsDef(array(
                                'wk_uvdesk_user_img' => _MODULE_DIR_.'wkuvdeskticketsystem/views/img/wk-uvdesk-user.png',
                                'iso' => $this->context->language->iso_code,
                                'allowTinymce' => 1,
                            ));

                            $this->setTemplate('viewticket.tpl');
                        } else {
                            Tools::redirect($this->context->link->getPageLink('pagenotfound'));
                        }
                    }
                }
            } else {
                Tools::redirect($this->context->link->getPageLink('pagenotfound'));
            }
        } else {
            Tools::redirect(
                'index.php?controller=authentication&back='.urlencode(
                    $this->context->link->getModuleLink('wkuvdeskticketsystem', 'customerticketlist')
                )
            );
        }
    }

    public function postProcess()
    {
        //submit ticket reply
        if (Tools::isSubmit('submitReply')) {
            $incrementId = Tools::getValue('id'); //Increment Id is a ticket id for a particular company
            $ticketId = Tools::getValue('ticketId');
            $reply = Tools::getValue('reply');

            if (!$reply) {
                $this->errors[] = $this->module->l('Message is required field.', 'viewticket');
            }

            if (empty($this->errors)) {
                $success = 0;
                $objUvdesk = new WkUvdeskHelper();
                $ticketDetail = $objUvdesk->getTicket($incrementId);
                if ($ticketDetail) {
                    if (isset($ticketDetail->ticket->id) && $ticketDetail->ticket->id == $ticketId) {
                        $actAsType = 'customer'; //as 'customer' or 'agent'
                        $tickets = $objUvdesk->addThread($ticketId, $reply, $actAsType);
                        if ($tickets && isset($tickets->id) && $tickets->id) {
                            $success = 1;
                            Tools::redirect(
                                $this->context->link->getModuleLink(
                                    'wkuvdeskticketsystem',
                                    'viewticket',
                                    array('id' => $incrementId, 'success' => $success)
                                )
                            );
                        }
                    }
                }

                if (!$success) {
                    $this->errors[] = $this->module->l('Something went wrong', 'viewticket');
                }
            }
        }

        //Add collaborator of ticket
        if (Tools::isSubmit('submitCollaborator')) {
            $incrementId = Tools::getValue('id'); //Increment Id is a ticket id for a particular company
            $collaboratorEmail = Tools::getValue('collaboratorEmail');
            $ticketId = Tools::getValue('ticketId');
            if (!$collaboratorEmail) {
                $this->errors[] = $this->module->l('Email is required field.', 'viewticket');
            } elseif (!Validate::isEmail($collaboratorEmail)) {
                $this->errors[] = $this->module->l('Email must be valid.', 'viewticket');
            }

            if (empty($this->errors)) {
                $success = 0;
                $objUvdesk = new WkUvdeskHelper();
                $ticketDetail = $objUvdesk->getTicket($incrementId);
                if ($ticketDetail) {
                    if (isset($ticketDetail->ticket->id) && $ticketDetail->ticket->id == $ticketId) {
                        $addedSuccess = $objUvdesk->addCollaborator($ticketId, $collaboratorEmail);
                        $success = 1;
                        if ($addedSuccess && isset($addedSuccess->collaborator->id)) {
                            Tools::redirect(
                                $this->context->link->getModuleLink(
                                    'wkuvdeskticketsystem',
                                    'viewticket',
                                    array('id' => $incrementId, 'success_col' => $success)
                                )
                            );
                        } else {
                            if (isset($addedSuccess->error)) {
                                if (isset($addedSuccess->description)) {
                                    $this->errors[] = $addedSuccess->description;
                                } else {
                                    $this->errors[] = $addedSuccess->error;
                                }
                            }
                        }
                    }
                }

                if (!$success) {
                    $this->errors[] = $this->module->l('Something went wrong', 'viewticket');
                }
            }
        }

        //Download attachment
        if (Tools::getValue('attach')) {
            $attachmentId = Tools::getValue('attach');
            $objUvdesk = new WkUvdeskHelper();
            $attahchmentURL = $objUvdesk->downloadAttachment($attachmentId);
            Tools::redirectAdmin($attahchmentURL);
        }
    }

    public function displayAjaxGetTicketThreads()
    {
        $ticketId = Tools::getValue('ticketId');
        if ($ticketId) {
            $threadPage = Tools::getValue('threadPage');
            $objUvdesk = new WkUvdeskHelper();
            $ticketThreads = $objUvdesk->getThreads($ticketId, $threadPage);
            if (isset($ticketThreads->threads) && $ticketThreads->threads) {
                $ascendingThreads = array_reverse($ticketThreads->threads);
                die(Tools::jsonEncode(array(
                        'threads' => (array) $ascendingThreads,
                        'threadsPagination' => $ticketThreads->pagination,
                    )));
            }
        }
        die('0');
    }

    public function displayAjaxDeleteCollaborator()
    {
        $collaboratorId = Tools::getValue('collaborator_id');
        $ticketId = Tools::getValue('ticketId');
        if ($collaboratorId && $ticketId) {
            $objUvdesk = new WkUvdeskHelper();
            $deleteSuccess = $objUvdesk->removeCollaborator($ticketId, $collaboratorId);
            $deleteSuccess = (array) $deleteSuccess;
            if (!isset($deleteSuccess['error'])) {
                die(Tools::jsonEncode($deleteSuccess));
            }
        }
        die('0');
    }

    public function setMedia()
    {
        parent::setMedia();
        //tinymce
        $this->addJS(
            "https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=".Configuration::get('WK_UVDESK_TINYMCE_KEY')
        );

        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/uvdeskticketlist.css');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/uvdeskticketlist.js');
    }
}
