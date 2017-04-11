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

class AdminUvdeskTicketListController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        parent::__construct();
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
        //tinymce setup
        $this->context->smarty->assign(array(
                'self' => dirname(__FILE__),
                'ad' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_),
                'iso' => $this->context->language->iso_code,
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
            }
        } else {
            //Ticket list page
            $activeFilter = 0;
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
                $activeFilter = $group;
            } else {
                $group = '';
            }
            if (Tools::getValue('team')) {
                $team = Tools::getValue('team');
                $activeFilter = $team;
            } else {
                $team = '';
            }
            if (Tools::getValue('priority')) {
                $priority = Tools::getValue('priority');
                $activeFilter = $priority;
            } else {
                $priority = '';
            }
            if (Tools::getValue('type')) {
                $type = Tools::getValue('type');
                $activeFilter = $type;
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
                $objUvdesk->pagination($ticketList->pagination->totalCount); // total no. of tickets by status

                $this->context->smarty->assign(array(
                        'customerTickets' => $customerTickets,
                        'ticketAllStatusData' => $ticketList->status,
                        'preDefinedLabels' => $ticketList->labels->predefind,
                        'customerLabels' => $ticketList->labels->custom,
                        'tabNumberofTickets' => $ticketList->tabs,
                        'tabStatus' => $status,
                        'activeLabel' => $label,
                        'activeAgent' => $agent,
                        'activeGroup' => $group,
                        'activeTeam' => $team,
                        'activePriority' => $priority,
                        'activeType' => $type,
                        'n' => $ticketList->pagination->numItemsPerPage, //no. of item for per page
                    ));

                Media::addJsDef(array(
                        'allAgentList' => $objUvdesk->getMembers(),
                        'activeFilter' => $activeFilter,
                    ));
            }
        }

        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        return parent::renderForm();
    }

    public function filterTicketsByPage($customerTickets, $p, $n)
    {
        $result = array();
        $start = ($p - 1) * $n;
        $end = $start + $n;
        for ($i=$start; $i<$end; $i++) {
            if (array_key_exists($i, $customerTickets)) {
                $result[] = $customerTickets[$i];
            }
        }
        
        return $result;
    }

    public function postProcess()
    {
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
                            Tools::redirectAdmin(self::$currentIndex.'&id='.(int) $incrementId.'&success='.(int) $success.'&token='.$this->token);
                        }
                    }
                }

                if (!$success) {
                    $this->errors[] = Tools::displayError($this->l('Something went wrong'));
                }
            }
        }

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
        } else if ($filterAction == 'team') {
            $filterData = $objUvdesk->getFilteredData('team')->team; //uvdesk team
        } else if ($filterAction == 'priority') {
            $filterData = $objUvdesk->getFilteredData('priority')->priority; //uvdesk priority
        } else if ($filterAction == 'type') {
            $filterData = $objUvdesk->getFilteredData('type')->type; //uvdesk type
        }
        die(Tools::jsonEncode($filterData)); //ajax close
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

    public function setMedia()
    {
        parent::setMedia();

        //tinymce
        $this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
        if (version_compare(_PS_VERSION_, '1.6.0.11', '>')) {
            $this->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
        } else {
            $this->addJS(_PS_JS_DIR_.'tinymce.inc.js');
        }

        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/uvdeskticketlist.css');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/uvdeskticketlist.js');
    }
}
