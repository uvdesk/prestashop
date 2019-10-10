{*
* 2010-2019 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
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
*}

{if isset($customFieldValues)}
    <div class="panel panel-default">
        <div {if !isset($backendController)}class="panel-body"{/if}>
            {if isset($backendController)}
                <h3>{l s='Custom Fields' mod='wkuvdeskticketsystem'}</h3>
            {else}
                <div class="wk-custom-field-heading">{l s='Custom Fields' mod='wkuvdeskticketsystem'}</div>
            {/if}
            <div class="wk-custom-field-container">
                {foreach $customFieldValues as $fieldValues}
                    <div class="wk-custom-field-display">
                        <label>{$fieldValues.fieldName}</label>
                        <div class="wk-field-block">
                            {if $fieldValues.fieldType == 'file' && isset($fieldValues.value->id)}
                                <a href="{if isset($backendController)}{$link->getAdminLink('AdminUvdeskTicketList')}&attach={$fieldValues.value->id}{else}{$link->getModuleLink('wkuvdeskticketsystem', 'viewticket', ['attach' => $fieldValues.value->id])}{/if}">
                                    <i class="material-icons wk-attachment" data-attachment-id="{$fieldValues.value->id}">&#xE2C0;</i>
                                </a>
                            {else}
                                {if is_array($fieldValues.visibleValues)}
                                    {foreach $fieldValues.visibleValues as $visiValues}
                                        {$visiValues}<br>
                                    {/foreach}
                                {else}
                                    {$fieldValues.visibleValues}
                                {/if}
                            {/if}
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
{/if}