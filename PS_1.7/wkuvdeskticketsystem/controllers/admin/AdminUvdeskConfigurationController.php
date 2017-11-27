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

class AdminUvdeskConfigurationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'configuration';

        parent::__construct();
        $this->toolbar_title = $this->l('Set Configuration');
        $this->fields_options = array(
            'Configuration' => array(
                'title' => $this->l('General Configuration'),
                'fields' => array(
                    'WK_UVDESK_ACCESS_TOKEN' => array(
                        'type' => 'text',
                        'title' => $this->l('Access Token'),
                        'hint' => $this->l('Generate the Access Token from the uvdesk admin panel'),
                        'required' => true,
                        'autocomplete' => false,
                    ),
                    'WK_UVDESK_COMPANY_DOMAIN' => array(
                        'type' => 'text',
                        'title' => $this->l('Company Domain'),
                        'hint' => $this->l('Specify your company Domain Name'),
                        'suffix' => '.uvdesk.com',
                        'required' => true,
                        'autocomplete' => false,
                    ),
                ),
                'submit' => array('title' => $this->l('Save')),
            ),
        );
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
}
