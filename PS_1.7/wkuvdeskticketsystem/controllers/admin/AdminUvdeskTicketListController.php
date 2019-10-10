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

class AdminUvdeskTicketListController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        parent::__construct();
        $this->toolbar_title = $this->l('Manage Ticket List');
    }

    public function initContent()
    {
        parent::initContent();

        $this->initToolbar();
        $this->display = '';

        $this->content .= $this->renderForm();
        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));
    }

    public function renderForm()
    {
        $this->context->smarty->assign(array(
                'self' => dirname(__FILE__),
                'backendController' => 1,
            ));

        $objUvdesk = new WkUvdeskHelper();
        if ($incrementId = Tools::getValue('id')) { //Increment Id is a ticket id for a particular company
            //View ticket details page
            $ticketDetail = $objUvdesk->getTicket($incrementId);
            if (isset($ticketDetail->ticket) && $ticketDetail->ticket) {
                $this->context->smarty->assign(array(
                    'incrementId' => $incrementId,
                    'ticket' => $ticketDetail->ticket,
                    'userDetails' => $ticketDetail->userDetails,
                    'ticketId' => $ticketDetail->ticket->id,
                    'ticketTotalThreads' => $ticketDetail->ticketTotalThreads,
                    'ticket_reply' => $ticketDetail->createThread->reply,
                    'preDefinedLabels' => $ticketDetail->labels->predefind,
                    'customerLabels' => $ticketDetail->labels->custom,
                    'attachments' => $ticketDetail->createThread->attachments,
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
            }
        } else {
            //Ticket list page
            $activeFilter = array();
            if (Tools::getValue('p')) {
                $page = Tools::getValue('p');
            } else {
                $page = 1;
            }
            if (Tools::getValue('status')) {
                $status = Tools::getValue('status');
            } else {
                $status = 1;
            }
            if (Tools::getValue('order')) {
                $order = Tools::getValue('order');
            } else {
                $order = 'DESC';
            }
            if (Tools::getValue('search')) {
                $search = Tools::getValue('search');
            } else {
                $search = '';
            }
            if (Tools::getValue('sort_by')) {
                $sort = Tools::getValue('sort_by');
            } else {
                $sort = 't.id';
            }
            if (Tools::getValue('label')) {
                $label = Tools::getValue('label');
            } else {
                $label = 'all';
            }
            if (Tools::getValue('agent')) {
                $agent = Tools::getValue('agent');
            } else {
                $agent = '';
            }
            if (Tools::getValue('customer')) {
                $customer = Tools::getValue('customer');
            } else {
                $customer = '';
            }
            if (Tools::getValue('group')) {
                $group = Tools::getValue('group');
                $activeFilter[] = $group;
            } else {
                $group = '';
            }
            if (Tools::getValue('team')) {
                $team = Tools::getValue('team');
                $activeFilter[] = $team;
            } else {
                $team = '';
            }
            if (Tools::getValue('priority')) {
                $priority = Tools::getValue('priority');
                $activeFilter[] = $priority;
            } else {
                $priority = '';
            }
            if (Tools::getValue('type')) {
                $type = Tools::getValue('type');
                $activeFilter[] = $type;
            } else {
                $type = '';
            }

            $filterData = array(
                'sort'  => $sort,
                'order' => $order,
                'page'  => $page,
                'status' => $status,
                'search' => $search,
                'label' => $label,
                'agent' => $agent,
                'customer' => $customer,
                'group' => $group,
                'team' => $team,
                'priority' => $priority,
                'type' => $type
            );

            $ticketList = $objUvdesk->getTickets($filterData);
            if (isset($ticketList->tickets)) {
                $customerTickets = (array) $ticketList->tickets;
                if ($ticketList->pagination->totalCount) {
                    // total no. of tickets by status
                    $objUvdesk->pagination(
                        $ticketList->pagination->totalCount,
                        $ticketList->pagination->numItemsPerPage
                    );
                }

                //get all agent members
                $allAgentList = $objUvdesk->getMembers();
                if ($allAgentList && $agent) {
                    $currentAgent = array();
                    foreach ($allAgentList as $agentList) {
                        if ($agent == $agentList->id) {
                            $currentAgent = (array) $agentList;
                            break;
                        }
                    }

                    //get current filter agent
                    if ($currentAgent) {
                        $this->context->smarty->assign('currentAgent', $currentAgent);
                    }
                }

                //get current filter customer
                if ($customer) {
                    if ($currentCustomer = $objUvdesk->getCustomersById($customer)) {
                        $this->context->smarty->assign('currentCustomer', $currentCustomer);
                    }
                }

                $this->context->smarty->assign(array(
                        'customerTickets' => $customerTickets,
                        'ticketAllStatusData' => $ticketList->status,
                        'preDefinedLabels' => $ticketList->labels->predefind,
                        'customerLabels' => $ticketList->labels->custom,
                        'tabNumberofTickets' => $ticketList->tabs,
                        'tabStatus' => $status,
                        'activeLabel' => $label,
                        'activeAgent' => $agent,
                        'activeCustomer' => $customer,
                        'activeGroup' => $group,
                        'activeTeam' => $team,
                        'activePriority' => $priority,
                        'activeType' => $type,
                        'n' => $ticketList->pagination->numItemsPerPage, //no. of item for per page
                    ));

                Media::addJsDef(array(
                        'allAgentList' => $allAgentList,
                        'activeFilter' => Tools::jsonEncode($activeFilter),
                        'activeGroup' => $group,
                        'activeTeam' => $team,
                        'activePriority' => $priority,
                        'activeType' => $type,
                        'activeAgent' => $agent,
                        'activeCustomer' => $customer,
                    ));
            }
        }

        Media::addJsDef(array(
            'wk_uvdesk_user_img' => _MODULE_DIR_.'wkuvdeskticketsystem/views/img/wk-uvdesk-user.png',
            'allowTinymce' => 1,
        ));

        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        //submit ticket reply
        if (Tools::isSubmit('submitReply')) {
            $incrementId = Tools::getValue('id'); //Increment Id is a ticket id for a particular company
            $ticketId = Tools::getValue('ticketId');
            $reply = Tools::getValue('reply');

            if (!$reply) {
                $this->errors[] = Tools::displayError($this->l('Message is required field.'));
            }

            if (empty($this->errors)) {
                $success = 0;
                $objUvdesk = new WkUvdeskHelper();
                $ticketDetail = $objUvdesk->getTicket($incrementId);
                if ($ticketDetail) {
                    if (isset($ticketDetail->ticket->id) && $ticketDetail->ticket->id == $ticketId) {
                        $objUvdesk = new WkUvdeskHelper();
                        $actAsType = 'agent'; //as 'customer' or 'agent'
                        $tickets = $objUvdesk->addThread($ticketId, $reply, $actAsType);
                        if ($tickets && isset($tickets->id) && $tickets->id) {
                            $success = 1;
                            Tools::redirectAdmin(
                                self::$currentIndex.'&id='.(int) $incrementId.'&success='.(int) $success.'&token='.$this->token
                            );
                        }
                    }
                }

                if (!$success) {
                    $this->errors[] = Tools::displayError($this->l('Something went wrong'));
                }
            }
        }

        //Add collaborator of ticket
        if (Tools::isSubmit('submitCollaborator')) {
            $incrementId = Tools::getValue('id'); //Increment Id is a ticket id for a particular company
            $collaboratorEmail = Tools::getValue('collaboratorEmail');
            $ticketId = Tools::getValue('ticketId');
            if (!$collaboratorEmail) {
                $this->errors[] = $this->l('Email is required field.');
            } elseif (!Validate::isEmail($collaboratorEmail)) {
                $this->errors[] = $this->l('Email must be valid.');
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
                            Tools::redirectAdmin(
                                self::$currentIndex.'&id='.(int) $incrementId.'&conf=3&token='.$this->token
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
                    $this->errors[] = $this->l('Something went wrong');
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

        parent::postProcess();
    }

    public function ajaxProcessDeleteCustomerTickets()
    {
        $checkedTicketIds = Tools::getValue('checked_ticketsIds');
        if ($checkedTicketIds) {
            $objUvdesk = new WkUvdeskHelper();
            $deleteSuccess = $objUvdesk->deleteTickets($checkedTicketIds);
            $deleteSuccess = (array) $deleteSuccess;
            if (!isset($deleteSuccess['error'])) {
                die(Tools::jsonEncode($deleteSuccess));
            }
        }
        die('0');//ajax close
    }

    public function ajaxProcessAssignAgent()
    {
        $memberId = Tools::getValue('member_id');
        $ticketId = Tools::getValue('ticket_id');
        if ($ticketId && $memberId) {
            $objUvdesk = new WkUvdeskHelper();
            $assignSuccess = $objUvdesk->assignAgent($ticketId, $memberId);
            $assignSuccess = (array) $assignSuccess;
            if (!isset($assignSuccess['error'])) {
                die(Tools::jsonEncode($assignSuccess));
            }
        }
        die('0');//ajax close
    }

    public function ajaxProcessSearchAgentByName()
    {
        $searchName = Tools::getValue('search_member_name');
        if ($searchName) {
            $objUvdesk = new WkUvdeskHelper();
            $resultSuccess = $objUvdesk->getMembers($searchName);
            $resultSuccess = (array) $resultSuccess->users;
            if (!isset($resultSuccess['error'])) {
                die(Tools::jsonEncode($resultSuccess));
            }
        }
        die('0');//ajax close
    }

    public function ajaxProcessSearchFilterByName()
    {
        $searchName = Tools::getValue('search_name');
        if ($searchName) {
            $objUvdesk = new WkUvdeskHelper();
            $filterValue = Tools::getValue('filterValue');
            if ($filterValue == 'customer') {
                $resultSuccess = $objUvdesk->getCustomers($searchName);
                $resultSuccess = (array) $resultSuccess->customers;
            }
            if (!isset($resultSuccess['error'])) {
                die(Tools::jsonEncode($resultSuccess));
            }
        }
        die('0');//ajax close
    }

    public function ajaxProcessLoadFilterData()
    {
        $filterAction = Tools::getValue('filterAction');
        $filterData = array();
        $objUvdesk = new WkUvdeskHelper();
        if ($filterAction == 'group') {
            $filterData = $objUvdesk->getFilteredData('group')->group; //uvdesk group
        } elseif ($filterAction == 'team') {
            $filterData = $objUvdesk->getFilteredData('team')->team; //uvdesk team
        } elseif ($filterAction == 'priority') {
            $filterData = $objUvdesk->getFilteredData('priority')->priority; //uvdesk priority
        } elseif ($filterAction == 'type') {
            $filterData = $objUvdesk->getFilteredData('type')->type; //uvdesk type
        }
        die(Tools::jsonEncode($filterData)); //ajax close
    }

    public function ajaxProcessGetTicketThreads()
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

    public function ajaxProcessDeleteCollaborator()
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

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        //tinymce
        $this->addJS(
            "https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=".Configuration::get('WK_UVDESK_TINYMCE_KEY')
        );

        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/uvdeskticketlist.css');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/uvdeskticketlist.js');
    }
}
