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

<div class="panel panel-default">
    <div {if !isset($backendController)}class="panel-body"{/if}>
        {if isset($backendController)}
            <h3>{l s='Collaborators' mod='wkuvdeskticketsystem'}</h3>
        {else}
            <div class="wk-custom-field-heading">{l s='Collaborators' mod='wkuvdeskticketsystem'}</div>
        {/if}
        <div id="collaborator-panel">
            {if isset($ticket->collaborators) && $ticket->collaborators}
                {foreach $ticket->collaborators as $collaborator}
                    <div class="coll-div" id="coll-div-{$collaborator->id|escape:'htmlall':'UTF-8'}">
                        <img src="{if $collaborator->smallThumbnail}{$collaborator->smallThumbnail|escape:'htmlall':'UTF-8'}{else}{$smarty.const._MODULE_DIR_}wkuvdeskticketsystem/views/img/wk-uvdesk-user.png{/if}" class="img-responsive pull-left">
                        <span>
                            {if isset($collaborator->detail->agent)}{$collaborator->detail->agent->name|escape:'htmlall':'UTF-8'}{else}{$collaborator->detail->customer->name|escape:'htmlall':'UTF-8'}{/if}
                        </span>
                        <div class="pull-right removeCollaborator" data-col-id="{$collaborator->id|escape:'htmlall':'UTF-8'}">
                            <i class="icon-trash"></i>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                {/foreach}
            {else}
                {l s='There is no collaborator available for this ticket.' mod='wkuvdeskticketsystem'}
            {/if}
        </div>
        <form name="collaborator_form" action="{if isset($backendController)}{$link->getAdminLink('AdminUvdeskTicketList')}&id={$incrementId}{else}{$link->getModuleLink('wkuvdeskticketsystem', 'viewticket', ['id' => $incrementId])|escape:'htmlall':'UTF-8'}{/if}" method="post" enctype="multipart/form-data">
            <input type="hidden" name="ticketId" value="{$ticketId|escape:'htmlall':'UTF-8'}">
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