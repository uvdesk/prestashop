{*
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{extends file=$layout}
{block name='content'}
<script type="text/javascript" src="{$smarty.const._MODULE_DIR_}wkuvdeskticketsystem/views/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="{$smarty.const._MODULE_DIR_}wkuvdeskticketsystem/views/js/tinymce/tinymce_wk_setup.js"></script>
{if isset($smarty.get.success)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Reply added successfully.' mod='wkuvdeskticketsystem'}
	</p>
{/if}
{if isset($smarty.get.success_col)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Collaborator added successfully.' mod='wkuvdeskticketsystem'}
	</p>
{/if}
<div class="wk-module-block">
	<div class="wk-module-content">
		<div class="wk-module-page-title">
			<span>{l s='View Ticket' mod='wkuvdeskticketsystem'}</span>
		</div>
		<div class="wk-module-container row">
			<div class="col-md-3">
		        <div class="panel panel-default">
		          <div class="panel-body">
		            <div id="collaborator-panel">
		              	{if isset($ticket->collaborators) && $ticket->collaborators}
			              	{foreach $ticket->collaborators as $collaborator}
					            <div class="coll-div" id="coll-div-{$collaborator->id}">
					                <img src="{if $collaborator->smallThumbnail}{$collaborator->smallThumbnail}{else}https://cdn.uvdesk.com/uvdesk/images/d94332c.png{/if}" class="img-responsive pull-left">
					                <span>
					                	{if isset($collaborator->detail->agent)}{$collaborator->detail->agent->name}{else}{$collaborator->detail->customer->name}{/if}
					                </span>
					                <div class="pull-right removeCollaborator" data-col-id="{$collaborator->id}">
					                	<i class="material-icons">&#xE872;</i>
					                </div>
					                <div class="clearfix"></div>
					            </div>
				           	{/foreach}
				        {else}
				        	{l s='There is no collaborator available for this ticket.' mod='wkuvdeskticketsystem'}
				        {/if}
		            </div>
		            <form name="collaborator_form" action="{$link->getModuleLink('wkuvdeskticketsystem', 'viewticket', ['id' => $incrementId])}" method="post" enctype="multipart/form-data">
						<input type="hidden" name="ticketId" value="{$ticketId}">
		            	<div class="form-group">
		              		<input type="email"
		              		class="form-control"
		              		name="collaboratorEmail"
		              		id="collaboratorEmail"
		              		placeholder="{l s='Type e-mail to add collaborator...' mod='wkuvdeskticketsystem'}"
		              		required>
		              	</div>
		              	<div class="form-group">
		              		<button class="btn btn-success" type="submit" name="submitCollaborator">{l s='Add' mod='wkuvdeskticketsystem'}</button>
		            	</div>
		            </form>
		          </div>
		        </div>
		    </div>
		    <div class="panel panel-default col-md-9">
		        <div id="ticket-detail">
			        <h4>
			            #{if isset($ticket->incrementId)}{$ticket->incrementId}{/if} {if isset($ticket->subject)}{$ticket->subject}{/if}
			        </h4>
		          	<div class="ticket-labels">
			            <span class="label label-default">{$ticket->formatedCreatedAt}</span>
			            {if isset($ticket->status->id)}
			            	<span class="label label-default" title="Threads">{$ticketTotalThreads} {l s='Replies' mod='wkuvdeskticketsystem'}</span>
			            {/if}
			            {if isset($ticket->type->id)}
			            	<span class="label label-default" title="Type">{$ticket->type->name}</span>
			            {/if}
			            <span class="label label-default" title="Status">{$ticket->status->name}</span>
		          	</div>
		        </div>
		        <div class="thread">
			        <div class="col-md-12 thread-created-info text-center">
			            <span class="info">
			            	{if isset($ticket->customer->detail->customer->id)}{$ticket->customer->detail->customer->name}{/if} {l s='created a ticket' mod='wkuvdeskticketsystem'}
			            </span>
			            <span class="text-right date pull-right">
			            	{if isset($ticket->formatedCreatedAt)}{$ticket->formatedCreatedAt}{/if}
			            </span>
			        </div>
			        <div class="col-md-12">
			        	<div class="">
				            <div class="pull-left">
				            	<span class="round-tabs">
				                	<img src="{if isset($ticket->customer->profileImage)}{$ticket->customer->profileImage}{else}https://cdn.uvdesk.com/uvdesk/images/d94332c.png{/if}">
				            	</span>
				            </div>
				            <div class="thread-info">
					            <div class="thread-info-row first">
					            	<span class="cust-name">{if isset($ticket->customer->detail->customer->id)}{$ticket->customer->detail->customer->name}{/if}</span>
					            </div>
					            <div class="thread-info-row"></div>
				            </div>
						</div>
			            <div class="clearfix"></div>
			            <div class="thread-body">
				            <div class="reply border-none">
				                <div class="main-reply">
				                	{if isset($ticket_reply)}{$ticket_reply}{/if}
				                </div>
				                {if isset($attachments) && $attachments}
				                	<div class="attachments">
					                	{foreach $attachments as $attachment}
					                    	<a href="{$link->getModuleLink('wkuvdeskticketsystem', 'viewticket', ['attach' => $attachment->id])}">
					                			<i class="material-icons wk-attachment" data-attachment-id="{$attachment->id}" title="{$attachment->name}">&#xE2C0;</i>
					                		</a>
					                    {/foreach}
				                   	</div>
				                {/if}
				            </div>
			            </div>
			        </div>
		        </div>
		        <div class="text-center load-div">
		        	<button class="btn btn-primary" id="button-load"><i class="wk-icon-spin wk-icon-spinner"></i></button>
		        	<span class="loader-border"></span>
		        </div>
			    <div class="ticket-thread">{* ---- Load ticket threads here ---- *}</div>
			    <input type="hidden" name="threadPage" id="threadPage" value="0">
		        <div class="col-md-12" id="wk-ticket-reply-section">
		        	<div>
			        	<div class="pull-left">
				            <span class="round-tabs">
				            	<img src="{if isset($ticket->customer->profileImage) && $ticket->customer->profileImage}{$ticket->customer->profileImage}{else}https://cdn.uvdesk.com/uvdesk/images/d94332c.png{/if}">
				            </span>
			        	</div>
			        	<div class="thread-info">
			        		<span class="cust-name">{$ticket->customer->detail->customer->name}</span>
			        	</div>
			        	<div class="clearfix"></div>
			        </div>
		        	<div class="thread-body">
			            <div class="thread-info">
			              	<form action="{$link->getModuleLink('wkuvdeskticketsystem', 'viewticket', ['id' => $incrementId])}" method="post" enctype="multipart/form-data">
				                <input type="hidden" name="ticketId" value="{$ticketId}">
				                <div class="reply border-none" style="padding: 0;">
				                	<textarea name="reply" id="reply" class="wk_tinymce form-control"></textarea>
					                <div class="form-group attachment-div">
							    		<div class="labelWidget">
							    			<i style="font-size:16px;" class="material-icons remove-file" onclick="$(this).parent().remove();">&#xE872;</i>
											<label class="attach-file pointer"></label>
											<input type="file" name="attachment[]" class="fileUpload" style="display: none;" >
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
<div id="wk-loading-overlay">
	<i class="wk-icon-spinner wk-icon-spin"></i>
</div>
{/block}