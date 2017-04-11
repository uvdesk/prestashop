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

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__).'/classes/WkUvdeskHelper.php';
class WkUvDeskTicketSystem extends Module
{
    private $_html = '';
    private $_postErrors = array();
    public function __construct()
    {
        $this->name = 'wkuvdeskticketsystem';
        $this->tab = 'front_office_features';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->confirmUnistall = $this->l('Are you sure you want to uninstall this module?');
        parent::__construct();
        $this->displayName = $this->l('UVdesk – Prestashop Free Helpdesk Ticket System');
        $this->description = $this->l('Customer can create tickets for his/her query.');
    }
    
    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminUvdeskConfiguration'));
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCss($this->_path.'views/css/admin/css/wkuvdeskticketadminmenu.css');
    }

    public function hookDisplayCustomerAccount()
    {
        if (isset($this->context->customer->id)
            && Configuration::get('WK_UVDESK_ACCESS_TOKEN')
            && Configuration::get('WK_UVDESK_COMPANY_DOMAIN')) {
            return $this->display(__FILE__, 'uvdeskticketlist.tpl');
        }
    }

    public function registerModuleHook()
    {
        return $this->registerHook(array(
                'displayCustomerAccount', 'displayBackOfficeHeader'
            ));
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerModuleHook()
            || !$this->callInstallTab()
            ) {
            return false;
        }

        return true;
    }

    public function callInstallTab()
    {
        $this->installTab('AdminUvdeskTicketSystem', 'Uvdesk Ticket System');
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
        $moduleConfigData = array('WK_UVDESK_ACCESS_TOKEN', 'WK_UVDESK_COMPANY_DOMAIN');
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
