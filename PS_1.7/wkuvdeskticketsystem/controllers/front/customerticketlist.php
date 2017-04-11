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

class WkUvDeskTicketSystemCustomerTicketListModuleFrontController extends ModuleFrontController
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

                    $filterData = array(
                        'sort'  => $sort,
                        'order' => $order,
                        'page'  => $page,
                        'status' => $status,
                        'search' => $search,
                        'customer' => $uvdeskCustomerId
                    );

                    $ticketList = $objUvdesk->getTickets($filterData);
                    if (isset($ticketList->tickets)) {
                        $customerTickets = (array) $ticketList->tickets;
                        $objUvdesk->pagination($ticketList->pagination->totalCount); // total no. of tickets by status

                        $this->context->smarty->assign(array(
                                'customerTickets' => $customerTickets,
                                'ticketAllStatusData' => $ticketList->status,
                                'tabNumberofTickets' => $ticketList->tabs,
                                'tabStatus' => $status,
                                'n' => $ticketList->pagination->numItemsPerPage, //no. of item for per page
                                'self' => dirname(__FILE__),
                            ));
                    }
                }
            }
            $this->setTemplate('module:wkuvdeskticketsystem/views/templates/front/customerticketlist.tpl');
        } else {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('wkuvdeskticketsystem', 'customerticketlist')));
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->module->l('Ticket List', 'customerticketlist'),
            'url' => '',
        );

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->registerStylesheet('uvdeskticketlist-css', 'modules/'.$this->module->name.'/views/css/uvdeskticketlist.css');
        $this->registerStylesheet('wk-pagination-css', 'modules/'.$this->module->name.'/views/css/wk-pagination.css');
        $this->registerJavascript('uvdeskticketlist-js', 'modules/'.$this->module->name.'/views/js/uvdeskticketlist.js');
    }
}
