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

<div class="panel wk-ticket-labels">
	<h3>{l s='Labels' mod='wkuvdeskticketsystem'}</h3>
	{if isset($preDefinedLabels) && $preDefinedLabels}
      	{foreach $preDefinedLabels as $key => $value}
          	<a href="{$current}&label={$key}&token={$token}" style="{if isset($activeLabel)}{if $activeLabel == $key}color:#434a54;{/if}{/if}">{ucfirst($key)}</a>
          	<span class="label label-success">{$value}</span>
          	<br/>
    	{/foreach}
    {/if}
	{if isset($customerLabels) && $customerLabels}
    	<div class="showLabel">
            {foreach $customerLabels as $value}
            	<a href="{$custom_label_url}{$value->id}" style="{if $activeCustomLabel == $value->id}color:#434a54;{/if}">{ucfirst($value->name)}</a>
            	<span class="label label-success" style="background-color: {$value->color};">{$value->count}</span>
            	<br/>
            {/foreach}
      	</div>
    {/if}
</div>