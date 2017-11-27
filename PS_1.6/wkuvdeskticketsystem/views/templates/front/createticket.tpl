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

{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('wkuvdeskticketsystem', 'customerticketlist')|escape:'html':'UTF-8'}"{/if}>
		{l s='Ticket List' mod='wkuvdeskticketsystem'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
	<span class="navigation_page">{l s='Create Ticket' mod='wkuvdeskticketsystem'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<div class="wk-module-block">
	<div class="wk-module-content">
		<div class="wk-module-page-title">
			<span>{l s='Create Ticket' mod='wkuvdeskticketsystem'}</span>
		</div>
		<div class="wk-module-container row">
			<div class="col-md-8">
		        <div class="panel panel-default">
		          	<div class="panel-body">
			            <form action="{$link->getModuleLink('wkuvdeskticketsystem', 'createticket')|escape:'htmlall':'UTF-8'}" method="post" id="wk_uvdesk_ticket" name="wk_uvdesk_ticket" enctype="multipart/form-data">
				            {if isset($ticketTypes) && $ticketTypes}
								<div class="form-group">
									<label for="input-type" class="control-label required">{l s='Type' mod='wkuvdeskticketsystem'}</label>
									<div class="row">
										<div class="col-md-5">
											<select name="type" id="ticket-type" class="form-control form-control-select" required>
												<option value="">{l s='Select Type' mod='wkuvdeskticketsystem'}</option>
												{foreach $ticketTypes as $types}
													<option value="{$types->id|escape:'htmlall':'UTF-8'}">{$types->name|escape:'htmlall':'UTF-8'}</option>
												{/foreach}
											</select>
										</div>
									</div>
								</div>
								<input type="hidden" name="ticketTypeExist" class="form-control" value="1" >
							{/if}
				            <div class="form-group">
				                <label for="subject" class="control-label required">{l s='Subject' mod='wkuvdeskticketsystem'}</label>
				                <input type="text" name="subject" class="form-control" placeholder="{l s='Enter Subject' mod='wkuvdeskticketsystem'}" value="" required>
				            </div>
			             	<div class="form-group required">
				                <label for="message" class="control-label required">{l s='Message' mod='wkuvdeskticketsystem'}</label>
				                <textarea name="message" class="form-control" placeholder="{l s='Brief Description about your query' mod='wkuvdeskticketsystem'}" rows="5" required></textarea>
			              	</div>
							<div class="form-group attachment-div">
								<div class="labelWidget">
									<i class="icon-trash remove-file" onclick="$(this).parent().remove();"></i>
									<label class="attach-file pointer"></label>
									{*In PS V1.6 uploader class automatically added but in PS V1.7, we need to add this class as a div *}
									<input type="file" name="attachment[]" class="fileUpload" style="display: none;" >
								</div>
								<span id="addFile">+ {l s='Attach File' mod='wkuvdeskticketsystem'}</span>
								<div class="clearfix"></div>
							</div>
							{*Display custom field according to plan*}
							{if isset($customerActiveFields)}
								{foreach $customerActiveFields as $fields}
									<div class="form-group 
										{if $fields->customFieldsDependency}wk-dependent 
											{foreach $fields->customFieldsDependency as $dependentTypes}
												wk-dependent{$dependentTypes->id|escape:'htmlall':'UTF-8'}
											{/foreach}
										{/if}" data-required="{if $fields->required == '1'}1{else}0{/if}" data-custom-field="{$fields->id}">

										<label for="customFields[{$fields->id|escape:'htmlall':'UTF-8'}]" class="control-label {if $fields->required == '1'}required{/if}">{$fields->name|escape:'htmlall':'UTF-8'}</label>
										{*Display form field according to field type*}
										{if $fields->fieldType == 'text'}
											<input type="{$fields->validation->fieldtype|escape:'htmlall':'UTF-8'}"
											name="customFields[{$fields->id|escape:'htmlall':'UTF-8'}]"
											class="form-control"
											placeholder="{$fields->value|escape:'htmlall':'UTF-8'}"
											value=""
											{if empty($fields->customFieldsDependency) && $fields->required == '1'}required{/if} >
										{elseif $fields->fieldType == 'textarea'}
											<textarea
											name="customFields[{$fields->id|escape:'htmlall':'UTF-8'}]"
											class="form-control"
											placeholder="{$fields->value|escape:'htmlall':'UTF-8'}"
											{if empty($fields->customFieldsDependency) && $fields->required == '1'}required{/if}></textarea>
										{elseif $fields->fieldType == 'select'}
											{if $fields->customFieldValues}
												<div class="row">
													<div class="col-md-5">
														<select
														name="customFields[{$fields->id|escape:'htmlall':'UTF-8'}]"
														class="form-control form-control-select"
														{if empty($fields->customFieldsDependency) && $fields->required == '1'}required{/if}>
															{foreach $fields->customFieldValues as $optionValues}
																<option value="{$optionValues->id|escape:'htmlall':'UTF-8'}">
																	{$optionValues->name|escape:'htmlall':'UTF-8'}
																</option>
															{/foreach}
														</select>
													</div>
												</div>
											{/if}
										{elseif $fields->fieldType == 'radio' || $fields->fieldType == 'checkbox'}
											{if $fields->customFieldValues}
												{foreach $fields->customFieldValues as $optionValues}
													<div class="wk-form-{$fields->fieldType|escape:'htmlall':'UTF-8'}" style="padding:5px 0;">
														<label class="pull-left">
															{if $fields->fieldType == 'checkbox'}
																<input type="{$fields->fieldType}"
																name="customFields[{$fields->id|escape:'htmlall':'UTF-8'}][]"
																id="customFields_{$fields->id|escape:'htmlall':'UTF-8'}_{$optionValues->id|escape:'htmlall':'UTF-8'}"
																class="form-control"
																value="{$optionValues->id}"
																{if empty($fields->customFieldsDependency) && 																				$fields->required == '1'}required{/if} >
															{else}
																<input type="{$fields->fieldType|escape:'htmlall':'UTF-8'}"
																name="customFields[{$fields->id|escape:'htmlall':'UTF-8'}]"
																id="customFields_{$fields->id|escape:'htmlall':'UTF-8'}_{$optionValues->id|escape:'htmlall':'UTF-8'}"
																class="form-control"
																value="{$optionValues->id|escape:'htmlall':'UTF-8'}"
																{if empty($fields->customFieldsDependency) && 																				$fields->required == '1'}required{/if} >
															{/if}
														</label>
														<label for="customFields_{$fields->id|escape:'htmlall':'UTF-8'}_{$optionValues->id|escape:'htmlall':'UTF-8'}" class="pull-left">&nbsp;{$optionValues->name|escape:'htmlall':'UTF-8'}</label>
														<div class="clearfix"></div>
													</div>
												{/foreach}
											{/if}
										{elseif $fields->fieldType == 'date' || $fields->fieldType == 'time' || $fields->fieldType == 'datetime'}
											<div class="row">
												<div class="col-md-5">
													<div class="input-group">
														<input type="text"
														name="customFields[{$fields->id|escape:'htmlall':'UTF-8'}]"
														class="form-control {if $fields->fieldType == 'date'}wk-datepicker{elseif $fields->fieldType == 'time'}wk-timepicker{else}wk-datetimepicker{/if}"
														placeholder="{$fields->value|escape:'htmlall':'UTF-8'}"
														value=""
														{if empty($fields->customFieldsDependency) && $fields->required == '1'}required{/if} >
														<span class="input-group-addon">
															<i class="icon-calendar"></i>
														</span>
													</div>
												</div>
											</div>
										{elseif $fields->fieldType == 'file'}
											<div class="row">
												<div class="col-md-5">
													<input type="file"
													name="customFields[{$fields->id|escape:'htmlall':'UTF-8'}]"
													class="form-control"
													{if empty($fields->customFieldsDependency) && $fields->required == '1'}required{/if} >
												</div>
											</div>
										{/if}
									</div>
								{/foreach}
								<input type="hidden" name="requiedCustomFields" value="">
							{/if}
			              	<div class="wk_text_right">
			              		<input type="submit" name="createTicket" class="btn btn-success" value="Create Ticket">
			              	</div>
			          	</form>
		          	</div>
		        </div>
		    </div>
		    <div class="col-md-4">
		        <div class="panel panel-default">
			        <div class="panel-body">
			            <div>
			              	<span class="badge"><i class="icon-info"></i></span>
			              	<label>{l s='Help and Information' mod='wkuvdeskticketsystem'}</label>
			            </div>
			            <div>
			            	<label>{l s='Ticket' mod='wkuvdeskticketsystem'}</label>
			            	<p>{l s='A ticket is the support request submitted by the customers to inquire about their problems.' mod='wkuvdeskticketsystem'}</p>
			            </div>
			            <div>
			            	<label>{l s='Ticket Creation' mod='wkuvdeskticketsystem'}</label>
			            	<p>{l s='The moment when the users enter their basic details, they get registered with UVdesk services and confirmation mail regarding the account activation is sent to their ID’s. They have to click on the link provided for setting the password.' mod='wkuvdeskticketsystem'}</p>
			            </div>
			        </div>
		        </div>
		    </div>
		</div>
	</div>
</div>
