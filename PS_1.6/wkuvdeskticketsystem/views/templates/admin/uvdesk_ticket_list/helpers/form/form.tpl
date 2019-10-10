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

{if isset($smarty.get.success)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Reply added successfully.' mod='wkuvdeskticketsystem'}
	</p>
{/if}
{if isset($ticket)}
	{*View ticket page*}
	<div class="wk-module-block">
		<div class="wk-module-content">
			<div class="wk-module-container row">
				<div class="col-md-3">
					{*Display labels*}
			    	{include file="$self/../../views/templates/admin/uvdeskticket-labels.tpl"}

					{*Manage collaborator*}
		        	{include file="$self/../../views/templates/front/_partials/add-collaborator.tpl"}

					{*Display custom field if exist in ticket*}
					{include file="$self/../../views/templates/front/_partials/display-custom-fields.tpl"}
			    </div>
			    <div class="wk-left-border col-md-9">
			        <div id="ticket-detail">
				        <h4>
				            #{if isset($ticket->incrementId)}{$ticket->incrementId|escape:'htmlall':'UTF-8'}{/if} {if isset($ticket->subject)}{$ticket->subject|escape:'htmlall':'UTF-8'}{/if}
				        </h4>
			          	<div class="ticket-labels">
				            <span class="label label-default">{$ticket->formatedCreatedAt|escape:'htmlall':'UTF-8'}</span>
				            {if isset($ticket->status->id)}
				            	<span class="label label-default" title="Threads">{$ticketTotalThreads|escape:'htmlall':'UTF-8'} {l s='Replies' mod='wkuvdeskticketsystem'}</span>
				            {/if}
				            {if isset($ticket->type->id)}
				            	<span class="label label-default" title="Type">{$ticket->type->name|escape:'htmlall':'UTF-8'}</span>
				            {/if}
				            <span class="label label-default" title="Status">{$ticket->status->name|escape:'htmlall':'UTF-8'}</span>
			          	</div>
			        </div>
			        <div class="thread">
				        <div class="col-md-12 thread-created-info text-center">
				            <span class="info">
				            	{if isset($ticket->customer->detail->customer->id)}{$ticket->customer->detail->customer->name|escape:'htmlall':'UTF-8'}{/if} {l s='created a ticket' mod='wkuvdeskticketsystem'}
				            </span>
				            <span class="text-right date pull-right">
				            	{if isset($ticket->formatedCreatedAt)}{$ticket->formatedCreatedAt|escape:'htmlall':'UTF-8'}{/if}
				            </span>
				        </div>
				        <div class="col-md-12">
				            <div class="pull-left">
				            	<span class="round-tabs">
				                	<img src="{if isset($ticket->customer->profileImage)}{$ticket->customer->profileImage|escape:'htmlall':'UTF-8'}{else}{$smarty.const._MODULE_DIR_}wkuvdeskticketsystem/views/img/wk-uvdesk-user.png{/if}">
				            	</span>
				            </div>
				            <div class="thread-info">
					            <div class="thread-info-row first">
					            	<span class="cust-name">{if isset($ticket->customer->detail->customer->id)}{$ticket->customer->detail->customer->name|escape:'htmlall':'UTF-8'}{/if}</span>
					            </div>
					            <div class="thread-info-row"></div>
				            </div>
				            <div class="clearfix"></div>
				            <div class="thread-body">
					            <div class="reply border-none">
					                <div class="main-reply">
					                	{if isset($ticket_reply)}{$ticket_reply}{/if}
					                </div>
					                {if isset($attachments) && $attachments}
					                	<div class="attachments first-attach">
						                	{foreach $attachments as $attachment}
						                    	<a href="{$link->getAdminLink('AdminUvdeskTicketList')|escape:'htmlall':'UTF-8'}&attach={$attachment->id|escape:'htmlall':'UTF-8'}">
						                    		<i class="icon-download wk-attachment" data-attachment-id="{$attachment->id|escape:'htmlall':'UTF-8'}" title="{$attachment->name|escape:'htmlall':'UTF-8'}"></i>
								                </a>
						                    {/foreach}
					                   	</div>
					                {/if}
					            </div>
				            </div>
				        </div>
			        </div>
			        <div class="text-center load-div">
			        	<button class="btn btn-primary" id="button-load"><i class="icon-spin icon-spinner"></i></button>
			        	<span class="loader-border"></span>
			        </div>
			        <div class="ticket-thread">{* ---- Load ticket threads here ---- *}</div>
			        <input type="hidden" name="threadPage" id="threadPage" value="0">
			        <div class="col-md-12" id="wk-ticket-reply-section">
			        	<div>
				        	<div class="pull-left">
					            <span class="round-tabs">
					            	<img src="{if isset($userDetails->pic)}{$userDetails->pic|escape:'htmlall':'UTF-8'}{else}{$smarty.const._MODULE_DIR_}wkuvdeskticketsystem/views/img/wk-uvdesk-user.png{/if}">
					            </span>
				        	</div>
				        	<div class="thread-info">
				        		<span class="cust-name">{$userDetails->name|escape:'htmlall':'UTF-8'}</span>
				        	</div>
				        	<div class="clearfix"></div>
				        </div>
			        	<div class="thread-body">
				            <div class="thread-form">
				              	<form action="{$current|escape:'htmlall':'UTF-8'}&id={$incrementId|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
					                <input type="hidden" name="ticketId" value="{$ticketId|escape:'htmlall':'UTF-8'}">
					                <div class="reply border-none" style="padding: 0;">
					                	<textarea name="reply" id="reply" class="wk_tinymce form-control"></textarea>
						                <div class="form-group attachment-div">
								    		<div class="labelWidget">
								    			<i class="icon-trash remove-file" onclick="$(this).parent().remove();"></i>
												<label class="attach-file pointer"></label>
												<div class="uploader" style="display:none;">
													<input type="file" name="attachment[]" class="fileUpload">
												</div>
											</div>
											<span id="addFile">+ {l s='Attach File' mod='wkuvdeskticketsystem'}</span>
											<div class="clearfix"></div>
								    	</div>
						                <button type="submit" name="submitReply" class="btn btn-success">{l s='Reply' mod='wkuvdeskticketsystem'}</button>
						            </div>
				              	</form>
				            </div>
			          	</div>
			        </div>
			    </div>
			</div>
		</div>
	</div>
{else if isset($customerTickets)}
	<div class="wk-module-container">
		<div class="wk-module-item-list row">
			<div class="col-md-3">
		    	{include file="$self/../../views/templates/admin/uvdeskticket-labels.tpl"}
		     	<div class="panel wk-uvdesk-search">
			        <h3>{l s='Filter Tickets' mod='wkuvdeskticketsystem'}</h3>
		          	<div>
		            	<label for="filter-assigned" class="control-label">{l s='Agent' mod='wkuvdeskticketsystem'}</label>
			            <div class="pos-relative" filter-type="agent">
			            	<input type="text" placeholder="{l s='Type atleast one letter' mod='wkuvdeskticketsystem'}" class="form-control inputbox" id="filter-assigned">
			            	<div class="wk-uvdesk-dropdown-menu open wk-agent-list" style="left:0;"></div>
			            </div>
					  	{if isset($currentAgent)}
						  	<div class="btn btn-primary btn-sm selected_person closefilter" data-action="agent">
								<span>X</span>
						  		&nbsp;{$currentAgent.name|escape:'htmlall':'UTF-8'}
							</div>
						{/if}
		          	</div>
		         	<div>
		           		<label for="filter-customer" class="control-label">{l s='Customer' mod='wkuvdeskticketsystem'}</label>
		            	<div class="pos-relative">
		              		<input type="text" placeholder="{l s='Type atleast one letter' mod='wkuvdeskticketsystem'}" class="form-control inputbox" id="filter-customer">
		              		<div class="wk-uvdesk-dropdown-menu open wk-customer-list" style="left:0;"></div>
		            	</div>
						{if isset($currentCustomer->data['0']) }
						  	<div class="btn btn-primary btn-sm selected_person closefilter" data-action="customer">
								<span>X</span>
						  		&nbsp;{$currentCustomer->data['0']->firstName|escape:'htmlall':'UTF-8'} {$currentCustomer->data['0']->lastName|escape:'htmlall':'UTF-8'}
							</div>
						{/if}
		          	</div>
		          	<div>
		            	<label for="filter-group" class="control-label">{l s='Group' mod='wkuvdeskticketsystem'}</label>
		            	<div class="pos-relative">
		            		<select name="filter-group" id="filter-group" class="wk-filter-ticket">
		            			<option value="" data-action="group" {if $activeGroup == ''}selected="selected"{/if}>{l s='All' mod='wkuvdeskticketsystem'}</option>
		              		</select>
		            	</div>
		          	</div>
		          	<div>
		            	<label for="filter-team" class="control-label">{l s='Team' mod='wkuvdeskticketsystem'}</label>
		            	<div class="pos-relative">
	            			<select name="filter-team" id="filter-team" class="wk-filter-ticket">
	            				<option value="" data-action="team" {if $activeTeam == ''}selected="selected"{/if}>{l s='All' mod='wkuvdeskticketsystem'}</option>
	              			</select>
		            	</div>
		          	</div>
		          	<div>
			            <label for="filter-priority" class="control-label">{l s='Priority' mod='wkuvdeskticketsystem'}</label>
			            <div class="pos-relative">
	            			<select name="filter-priority" id="filter-priority" class="wk-filter-ticket">
	            				<option value="" data-action="priority" {if $activePriority == ''}selected="selected"{/if}>{l s='All' mod='wkuvdeskticketsystem'}</option>
	              			</select>
			            </div>
			        </div>
			        <div>
			            <label for="filter-type" class="control-label">{l s='Type' mod='wkuvdeskticketsystem'}</label>
			            <div class="pos-relative">
	            			<select name="filter-type" id="filter-type" class="wk-filter-ticket">
	            				<option value="" data-action="type" {if $activeType == ''}selected="selected"{/if}>{l s='All' mod='wkuvdeskticketsystem'}</option>
	              			</select>
			            </div>
			        </div>
			    </div>
		    </div>
			<div class="tabs col-md-9">
				{if isset($ticketAllStatusData)}
					<ul class="nav nav-tabs">
						{foreach $ticketAllStatusData as $tstatus}
							<li class="nav-item {if $tstatus->id == $tabStatus}active{/if}" style="{if $tstatus->id == '1'}border-left:1px solid #d3d8db !important;{/if}">
								<a class="nav-link {if $tstatus->id == $tabStatus}active{/if}" {if $tstatus->id != $tabStatus}href="{$current|escape:'htmlall':'UTF-8'}&status={$tstatus->id|escape:'htmlall':'UTF-8'}{if isset($activeLabel)}&label={$activeLabel|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}{/if}">
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
									<span class="label {if $tstatus->id == $tabStatus}label-primary{else}label-default{/if}">{$tabNumberofTickets->{$tstatus->id}|escape:'htmlall':'UTF-8'}</span>
								</a>
							</li>
						{/foreach}
					</ul>
				{/if}
				<div class="tab-content" id="tab-content" style="margin: 25px 0px;">
					<div class="tab-pane fade in active wk-uvdesk-search" id="wk-uvdesk-Open">
						<div class="table-responsive">
							<table class="table table-striped">
								<thead>
									<tr>
										<th><input type="checkbox" id="wk_uvdeskticket_list_all"></th>
										<th>{l s='Priority' mod='wkuvdeskticketsystem'}</th>
										<th>{l s='Ticket' mod='wkuvdeskticketsystem'}</th>
										<th>{l s='Customer Name' mod='wkuvdeskticketsystem'}</th>
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
											<tr class="{if $key%2 == 0}even{else}odd{/if}" id="wk_ticket_row_{$tickets->id|escape:'htmlall':'UTF-8'}">
												<td><input type="checkbox" name="wk_uvdeskticket_list[]" value="{$tickets->id|escape:'htmlall':'UTF-8'}"></td>
												<td>
													<strong>
														<span style="color:{$tickets->priority->color|escape:'htmlall':'UTF-8'};">{$tickets->priority->name|escape:'htmlall':'UTF-8'}</span>
													</strong>
												</td>
												<td>#{$tickets->incrementId|escape:'htmlall':'UTF-8'}</td>
												<td>{$tickets->customer->name|escape:'htmlall':'UTF-8'}</td>
												<td>{$tickets->subject|truncate:30:'..':true:true|escape:'htmlall':'UTF-8'}</td>
												<td>{$tickets->formatedCreatedAt|escape:'htmlall':'UTF-8'}</td>
												<td><center>{$tickets->totalThreads|escape:'htmlall':'UTF-8'}</center></td>
												<td>
													<div class="getAllAgent">
														{if isset($tickets->agent->name)}
															{assign var=agent_name value=" "|explode:$tickets->agent->name}
															<span class="badge badge-sm badge-primary"><i class="icon-pencil"></i></span> {$agent_name[0]|escape:'htmlall':'UTF-8'}
														{else}
															<button class="btn btn-success edit-ticket-agent"><i class="icon-plus-circle"></i> {l s='Agent' mod='wkuvdeskticketsystem'}</button>
														{/if}
													</div>
													<div class="wk-uvdesk-dropdown-menu open wk-agent-list" data-ticket-id="{$tickets->id|escape:'htmlall':'UTF-8'}"></div>
												</td>
												<td>
													<center>
													<a title="{l s='View' mod='wkuvdeskticketsystem'}" href="{$current|escape:'htmlall':'UTF-8'}&id={$tickets->incrementId|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}">
														<button class="btn btn-primary">
															<i class="icon-eye"></i> {l s='View' mod='wkuvdeskticketsystem'}
														</button>
													</a>
													</center>
												</td>
											</tr>
										{/foreach}
									{else}
										<tr><td colspan="9">{l s='No tickets yet' mod='wkuvdeskticketsystem'}</td></tr>
									{/if}
								</tbody>
							</table>
						</div>
					</div>
				</div>
				{if isset($customerTickets) && $customerTickets}
					<div class="form-group">
						<button id="wk-delete-tickets" class="btn btn-danger">
							<i class="icon-trash-o"></i> {l s='Delete' mod='wkuvdeskticketsystem'}
						</button>
					</div>
				{/if}
				<div class="content_sortPagiBar">
					<div class="bottom-pagination-content clearfix">
						{include file="$self/../../views/templates/front/uvdesk-pagination.tpl" paginationId='bottom'}
					</div>
				</div>
			</div>
		</div>
	</div>
{else}
	<div class="alert alert-danger">{l s='Something went wrong' mod='wkuvdeskticketsystem'}</div>
{/if}

<div id="wk-loading-overlay">
	<i class="icon-spinner icon-spin"></i>
</div>

{strip}
	{if isset($ticketId) && $ticketId}
		{addJsDef ticketId = $ticketId}
	{/if}
	{addJsDef backend_controller = 1}
	{addJsDef uvdesk_ticket_controller = $link->getAdminLink('AdminUvdeskTicketList')}
	{addJsDefL name='confirm_delete'}{l s='Are you sure want to delete?' mod='wkuvdeskticketsystem'}{/addJsDefL}
	{addJsDefL name='choose_one'}{l s='Select atleast one ticket' mod='wkuvdeskticketsystem'}{/addJsDefL}
	{addJsDefL name='max_file'}{l s='Maximum Number of file is ' mod='wkuvdeskticketsystem'}{/addJsDefL}
	{addJsDefL name='invalid_file'}{l s='Invalid file type or size' mod='wkuvdeskticketsystem'}{/addJsDefL}
	{addJsDefL name='assign_success'}{l s='Ticket(s) assigned successfully' mod='wkuvdeskticketsystem'}{/addJsDefL}
	{addJsDefL name='delete_success'}{l s='Ticket(s) deleted successfully' mod='wkuvdeskticketsystem'}{/addJsDefL}
	{addJsDefL name='some_error'}{l s='Some error occured' mod='wkuvdeskticketsystem'}{/addJsDefL}
	{addJsDefL name='all_expended'}{l s='All Expanded' mod='wkuvdeskticketsystem'}{/addJsDefL}
	{addJsDefL name='show_more'}{l s='Show More' mod='wkuvdeskticketsystem'}{/addJsDefL}
	{addJsDefL name='replied'}{l s='replied' mod='wkuvdeskticketsystem'}{/addJsDefL}
	{addJsDefL name='no_result'}{l s='No result found' mod='wkuvdeskticketsystem'}{/addJsDefL}
{/strip}