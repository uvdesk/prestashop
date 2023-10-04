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

<div class="panel wk-ticket-labels">
	<h3>{l s='Labels' mod='wkuvdeskticketsystem'}</h3>
	{if isset($preDefinedLabels) && $preDefinedLabels}
      	{foreach $preDefinedLabels as $key => $value}
          	<a href="{$wk_whole_url}&label={$key}" style="{if isset($activeLabel)}{if $activeLabel == $key}color:#434a54;{/if}{/if}">{ucfirst($key)}</a>
          	<span class="label label-success">{$value}</span>
          	<br/>
    	{/foreach}
    {/if}
</div>