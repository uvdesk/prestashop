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
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__) . '/classes/WkUvdeskHelper.php';
class WkUvDeskTicketSystem extends Module
{
    public function __construct()
    {
        $this->name = 'wkuvdeskticketsystem';
        $this->tab = 'front_office_features';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->version = '4.0.3';
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->confirmUnistall = $this->l('Are you sure you want to uninstall this module?');
        parent::__construct();
        $this->displayName = $this->l('UVdesk - Prestashop Free Helpdesk Ticket System');
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
        return $this->registerHook([
            'displayCustomerAccount', 'displayNav1',
        ]);
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
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        if ($className == 'AdminUvdeskTicketSystem') {
            $tab->icon = 'list'; // Material Icon name
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
        $moduleConfigData = ['WK_UVDESK_ACCESS_TOKEN', 'WK_UVDESK_COMPANY_DOMAIN', 'WK_UVDESK_TINYMCE_KEY'];
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
