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

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__).'/classes/WkUvdeskHelper.php';
class WkUvDeskTicketSystem extends Module
{
    public function __construct()
    {
        $this->name = 'wkuvdeskticketsystem';
        $this->tab = 'front_office_features';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->version = '4.0.2';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->confirmUnistall = $this->l('Are you sure you want to uninstall this module?');
        parent::__construct();
        $this->displayName = $this->l('UVdesk – Prestashop Free Helpdesk Ticket System');
        $this->description = $this->l('Customer can create tickets for his/her query.');
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminUvdeskConfiguration'));
    }

    public function hookDisplayCustomerAccount()
    {
        if (isset($this->context->customer->id)
            && Configuration::get('WK_UVDESK_ACCESS_TOKEN')
            && Configuration::get('WK_UVDESK_COMPANY_DOMAIN')) {
            return $this->display(__FILE__, 'uvdeskticketlist.tpl');
        }
    }

    public function hookDisplayNav1()
    {
        if (Configuration::get('WK_UVDESK_ACCESS_TOKEN')
            && Configuration::get('WK_UVDESK_COMPANY_DOMAIN')) {
            $this->context->smarty->assign(
                'ticketLink',
                $this->context->link->getModuleLink('wkuvdeskticketsystem', 'createticket')
            );

            return $this->display(__FILE__, 'ticket_nav.tpl');
        }
    }

    public function registerModuleHook()
    {
        return $this->registerHook(array(
                'displayCustomerAccount', 'displayNav1'
            ));
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerModuleHook()
            || !Configuration::updateValue('WK_UVDESK_TINYMCE_KEY', '0gvf38pq1y5zocjdzbz6koo08r423iy62dqm3wa3wsutrwmu')
            || !$this->callInstallTab()
            ) {
            return false;
        }

        return true;
    }

    public function callInstallTab()
    {
        $this->installTab('AdminUvdesk', 'Uvdesk');
        $this->installTab('AdminUvdeskTicketSystem', 'Uvdesk Ticket System', 'AdminUvdesk');
        $this->installTab('AdminUvdeskConfiguration', 'Set Configuration', 'AdminUvdeskTicketSystem');
        $this->installTab('AdminUvdeskTicketList', 'Manage Ticket List', 'AdminUvdeskTicketSystem');

        return true;
    }

    public function installTab($className, $tabName, $tabParentName = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        if ($className == 'AdminUvdeskTicketSystem') {
            $tab->icon = 'list'; //Material Icon name
        }
        if ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } else {
            $tab->id_parent = 0;
        }
        $tab->module = $this->name;
        return $tab->add();
    }

    public function deleteConfigKeys()
    {
        $moduleConfigData = array('WK_UVDESK_ACCESS_TOKEN', 'WK_UVDESK_COMPANY_DOMAIN', 'WK_UVDESK_TINYMCE_KEY');
        foreach ($moduleConfigData as $moduleConfigKey) {
            if (!Configuration::deleteByName($moduleConfigKey)) {
                return false;
            }
        }

        return true;
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !$this->deleteConfigKeys()
            ) {
            return false;
        }

        return true;
    }
}
