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

{capture name=path}
	<span class="navigation_page">{l s='Ticket List' mod='wkuvdeskticketsystem'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
{if isset($smarty.get.created)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Ticket has been created successfully.' mod='wkuvdeskticketsystem'}
	</p>
{/if}
<div class="wk-module-block">
	<div class="wk-module-content">
		<div class="wk-module-page-title">
			<span><i class="icon-list"></i> {l s='Ticket List' mod='wkuvdeskticketsystem'}</span>
		</div>
		<div class="wk-module-container">
			<div class="wk-module-item-list">
				<p class="wk_text_right">
					<a href="{$link->getModuleLink('wkuvdeskticketsystem', 'createticket')|escape:'html':'UTF-8'}" title="{l s='Create Ticket' mod='wkuvdeskticketsystem'}">
						<button class="btn btn-success">{l s='Create Ticket' mod='wkuvdeskticketsystem'}</button>
					</a>
				</p>
				<div class="tabs">
					{if isset($ticketAllStatusData)}
						<ul class="nav nav-tabs">
							{foreach $ticketAllStatusData as $tstatus}
								<li class="nav-item {if $tstatus->id == $tabStatus}active{/if}" style="{if $tstatus->id == '1'}border-left:1px solid #d3d8db !important;{/if}{if $tstatus->id == $tabStatus}border-right:1px solid #d3d8db !important;{/if}">
									<a class="nav-link {if $tstatus->id == $tabStatus}active{/if}" {if $tstatus->id != $tabStatus}href="{$link->getModuleLink('wkuvdeskticketsystem', 'customerticketlist', ['status' => $tstatus->id])|escape:'htmlall':'UTF-8'}"{/if}>
										{if $tstatus->id == '1'}
											<i class="icon-inbox"></i>
										{elseif $tstatus->id == '2'}
											<i class="icon-exclamation-triangle"></i>
										{elseif $tstatus->id == '6'}
											<i class="icon-lightbulb-o"></i>
										{elseif $tstatus->id == '3'}
											<i class="icon-check-circle"></i>
										{elseif $tstatus->id == '4'}
											<i class="icon-minus-circle"></i>
										{else}
											<i class="icon-ban"></i>
										{/if}
										{$tstatus->name|escape:'htmlall':'UTF-8'}
										<span class="label {if $tstatus->id == $tabStatus}label-primary{else}label-default{/if} wk-front-label">{$tabNumberofTickets->{$tstatus->id}|escape:'htmlall':'UTF-8'}</span>
									</a>
								</li>
							{/foreach}
						</ul>
					{/if}
					<div class="tab-content" id="tab-content">
						<div class="tab-pane fade in active" id="wk-uvdesk-Open">
							<div class="table-responsive" style="font-size:14px;">
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th>{l s='Priority' mod='wkuvdeskticketsystem'}</th>
											<th>{l s='Ticket' mod='wkuvdeskticketsystem'}</th>
											<th width="20%">{l s='Subject' mod='wkuvdeskticketsystem'}</th>
											<th>{l s='Date Added' mod='wkuvdeskticketsystem'}</th>
											<th><center>{l s='Replies' mod='wkuvdeskticketsystem'}</center></th>
											<th>{l s='Agent' mod='wkuvdeskticketsystem'}</th>
											<th><center>{l s='Action' mod='wkuvdeskticketsystem'}</center></th>
										</tr>
									</thead>
									<tbody>
										{if isset($customerTickets) && $customerTickets}
											{foreach $customerTickets as $key => $tickets}
												<tr class="{if $key%2 == 0}even{else}odd{/if}">
													<td>
														<strong>
															<span style="color:{$tickets->priority->color|escape:'htmlall':'UTF-8'};">{$tickets->priority->name|escape:'htmlall':'UTF-8'}</span>
														</strong>
													</td>
													<td>#{$tickets->incrementId|escape:'htmlall':'UTF-8'}</td>
													<td>{$tickets->subject|truncate:30:'..':true:true|escape:'htmlall':'UTF-8'}</td>
													<td>{$tickets->formatedCreatedAt|escape:'htmlall':'UTF-8'}</td>
													<td><center>{$tickets->totalThreads|escape:'htmlall':'UTF-8'}</center></td>
													<td>{if isset($tickets->agent->name)}<i class="icon-user"></i> {$tickets->agent->name|escape:'htmlall':'UTF-8'}{else}-{/if}</td>
													<td>
														<center>
														<a title="{l s='View' mod='wkuvdeskticketsystem'}" href="{$link->getModuleLink('wkuvdeskticketsystem', 'viewticket', ['id' => $tickets->incrementId])|escape:'htmlall':'UTF-8'}">
															<button class="btn btn-primary">
																<i class="icon-eye"></i> {l s='View' mod='wkuvdeskticketsystem'}
															</button>
														</a>
														</center>
													</td>
												</tr>
											{/foreach}
										{else}
											<tr><td colspan="7">{l s='No tickets yet' mod='wkuvdeskticketsystem'}</td></tr>
										{/if}
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="content_sortPagiBar">
						<div class="bottom-pagination-content clearfix">
							{include file="./uvdesk-pagination.tpl" paginationId='bottom'}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>