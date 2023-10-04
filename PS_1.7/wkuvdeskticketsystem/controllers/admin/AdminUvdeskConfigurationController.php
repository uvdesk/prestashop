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
class AdminUvdeskConfigurationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'configuration';

        parent::__construct();
        $this->toolbar_title = $this->l('Configuration');
        $this->fields_options = [
            'Configuration' => [
                'title' => $this->l('General configuration'),
                'fields' => [
                    'WK_UVDESK_ACCESS_TOKEN' => [
                        'type' => 'text',
                        'title' => $this->l('Access token'),
                        'hint' => $this->l('Generate the access token from the uvdesk admin panel'),
                        'required' => true,
                        'autocomplete' => false,
                    ],
                    'WK_UVDESK_COMPANY_DOMAIN' => [
                        'type' => 'text',
                        'title' => $this->l('Company domain'),
                        'hint' => $this->l('Specify your company domain name'),
                        'suffix' => '.uvdesk.com',
                        'required' => true,
                        'autocomplete' => false,
                    ],
                ],
                'submit' => ['title' => $this->l('Save')],
            ],
        ];
    }

    public function initContent()
    {
        parent::initContent();

        $this->initToolbar();
        $this->display = '';

        $this->content .= $this->renderForm();
        $this->context->smarty->assign([
            'content' => $this->content,
        ]);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $accessToken = trim(Tools::getValue('WK_UVDESK_ACCESS_TOKEN'));
            $companyDomain = trim(Tools::getValue('WK_UVDESK_COMPANY_DOMAIN'));

            if ($accessToken == '') {
                $this->errors[] = $this->l('UVDesk access token is not specified.');
            } elseif (!Validate::isGenericName($accessToken)) {
                $this->errors[] = $this->l('Please use a valid string for UVDesk access token.');
            }
            if ($companyDomain == '') {
                $this->errors[] = $this->l('Your company domain name is not specified.');
            }

            if (empty($this->errors)) {
                Configuration::updateValue(
                    'WK_UVDESK_ACCESS_TOKEN',
                    $accessToken
                );
                Configuration::updateValue(
                    'WK_UVDESK_COMPANY_DOMAIN',
                    $companyDomain
                );
                Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        Media::addJsDef([
            'module_dir' => _MODULE_DIR_,
            'wkModuleAddonKey' => $this->module->module_key,
            'wkModuleAddonsId' => '',
            'wkModuleTechName' => $this->module->name,
            'wkModuleDoc' => file_exists(_PS_MODULE_DIR_ . $this->module->name . '/doc_en.pdf'),
        ]);
        $this->addJs('https://prestashop.webkul.com/crossselling/wkcrossselling.min.js?t=' . time());
    }
}
