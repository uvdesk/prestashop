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

{if isset($no_follow) AND $no_follow}
	{assign var='no_follow_text' value='rel="nofollow"'}
{else}
	{assign var='no_follow_text' value=''}
{/if}

{if isset($p) AND $p}
	{if isset($smarty.get.id_category) && $smarty.get.id_category && isset($category)}
		{if !isset($current_url)}
			{assign var='requestPage' value=$link->getPaginationLink('category', $category, false, false, true, false)}
		{else}
			{assign var='requestPage' value=$current_url}
		{/if}
		{assign var='requestNb' value=$link->getPaginationLink('category', $category, true, false, false, true)}
	{else}
		{if !isset($current_url)}
			{assign var='requestPage' value=$link->getPaginationLink(false, false, false, false, true, false)}
		{else}
			{assign var='requestPage' value=$current_url}
		{/if}
		{assign var='requestNb' value=$link->getPaginationLink(false, false, true, false, false, true)}
	{/if}
	<!-- Pagination -->
	<div id="pagination{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}" class="pagination clearfix">
		{if $start!=$stop}
			<ul class="pagination">
				{if $p != 1}
					{assign var='p_previous' value=$p-1}
					<li id="pagination_previous{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}" class="pagination_previous">
						<a {$no_follow_text|escape:'html':'UTF-8'} href="{$link->goPage($requestPage, $p_previous)|addslashes}" rel="prev">
							<i class="icon-chevron-left"></i> <b>{l s='Previous' mod='wkuvdeskticketsystem'}</b>
						</a>
					</li>
				{else}
					<li id="pagination_previous{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}" class="disabled pagination_previous">
						<span>
							<i class="icon-chevron-left"></i> <b>{l s='Previous' mod='wkuvdeskticketsystem'}</b>
						</span>
					</li>
				{/if}
				{if $start==3}
					<li>
						<a {$no_follow_text}  href="{$link->goPage($requestPage, 1)|addslashes}">
							<span>1</span>
						</a>
					</li>
					<li>
						<a {$no_follow_text}  href="{$link->goPage($requestPage, 2)|addslashes}">
							<span>2</span>
						</a>
					</li>
				{/if}
				{if $start==2}
					<li>
						<a {$no_follow_text}  href="{$link->goPage($requestPage, 1)|addslashes}">
							<span>1</span>
						</a>
					</li>
				{/if}
				{if $start>3}
					<li>
						<a {$no_follow_text}  href="{$link->goPage($requestPage, 1)|addslashes}">
							<span>1</span>
						</a>
					</li>
					<li class="truncate">
						<span>
							<span>...</span>
						</span>
					</li>
				{/if}
				{section name=pagination start=$start loop=$stop+1 step=1}
					{if $p == $smarty.section.pagination.index}
						<li class="active current">
							<span>
								<span>{$p|escape:'html':'UTF-8'}</span>
							</span>
						</li>
					{else}
						<li>
							<a {$no_follow_text|escape:'html':'UTF-8'} href="{$link->goPage($requestPage, $smarty.section.pagination.index)|addslashes}">
								<span>{$smarty.section.pagination.index|escape:'html':'UTF-8'}</span>
							</a>
						</li>
					{/if}
				{/section}
				{if $pages_nb>$stop+2}
					<li class="truncate">
						<span>
							<span>...</span>
						</span>
					</li>
					<li>
						<a href="{$link->goPage($requestPage, $pages_nb)|addslashes}">
							<span>{$pages_nb|intval}</span>
						</a>
					</li>
				{/if}
				{if $pages_nb==$stop+1}
					<li>
						<a href="{$link->goPage($requestPage, $pages_nb)|addslashes}">
							<span>{$pages_nb|intval}</span>
						</a>
					</li>
				{/if}
				{if $pages_nb==$stop+2}
					<li>
						<a href="{$link->goPage($requestPage, $pages_nb-1)|addslashes}">
							<span>{$pages_nb-1|intval}</span>
						</a>
					</li>
					<li>
						<a href="{$link->goPage($requestPage, $pages_nb)|addslashes}">
							<span>{$pages_nb|intval}</span>
						</a>
					</li>
				{/if}
				{if $pages_nb > 1 AND $p != $pages_nb}
					{assign var='p_next' value=$p+1}
					<li id="pagination_next{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}" class="pagination_next">
						<a {$no_follow_text|escape:'html':'UTF-8'} href="{$link->goPage($requestPage, $p_next)|addslashes}" rel="next">
							<b>{l s='Next' mod='wkuvdeskticketsystem'}</b> <i class="icon-chevron-right"></i>
						</a>
					</li>
				{else}
					<li id="pagination_next{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}" class="disabled pagination_next">
						<span>
							<b>{l s='Next' mod='wkuvdeskticketsystem'}</b> <i class="icon-chevron-right"></i>
						</span>
					</li>
				{/if}
			</ul>
		{/if}
	</div>
    <div class="product-count">
    	{if ($n*$p) < $nb_products }
    		{assign var='productShowing' value=$n*$p}
        {else}
        	{assign var='productShowing' value=($n*$p-$nb_products-$n*$p)*-1}
        {/if}
        {if $p==1}
        	{assign var='productShowingStart' value=1}
        {else}
        	{assign var='productShowingStart' value=$n*$p-$n+1}
        {/if}
        {if $nb_products > 1}
        	{l s='Showing %1$d - %2$d of %3$d items' sprintf=[$productShowingStart, $productShowing, $nb_products] mod='wkuvdeskticketsystem'}
		{else}
        	{l s='Showing %1$d - %2$d of 1 item' sprintf=[$productShowingStart, $productShowing] mod='wkuvdeskticketsystem'}
       	{/if}
    </div>
	<!-- /Pagination -->
{/if}
