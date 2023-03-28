{*
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