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
                    <div class="coll-div" id="coll-div-{$collaborator->id}">
                        <img src="{if $collaborator->smallThumbnail}{$collaborator->smallThumbnail}{else}{$smarty.const._MODULE_DIR_}wkuvdeskticketsystem/views/img/wk-uvdesk-user.png{/if}" class="img-responsive pull-left">
                        <span>
                            {if isset($collaborator->detail->agent)}
                                {$collaborator->detail->agent->name}
                            {elseif isset($collaborator->detail->customer->name)}
                                {$collaborator->detail->customer->name}
                            {elseif isset($collaborator->email)}
                                {$collaborator->email}
                            {/if}
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
        <form name="collaborator_form" action="{if isset($backendController)}{$link->getAdminLink('AdminUvdeskTicketList')}&id={$incrementId}{else}{$link->getModuleLink('wkuvdeskticketsystem', 'viewticket', ['id' => $incrementId])}{/if}" method="post" enctype="multipart/form-data">
            <input type="hidden" name="ticketId" value="{$ticketId}">
            <div class="form-group">
                <input type="email"
                class="form-control"
                name="collaboratorEmail"
                id="collaboratorEmail"
                style="font-size:13px;"
                placeholder="{l s='Type e-mail to add collaborator...' mod='wkuvdeskticketsystem'}"
                required>
            </div>
            <div class="form-group">
                <button class="btn btn-success" type="submit" name="submitCollaborator">{l s='Add' mod='wkuvdeskticketsystem'}</button>
            </div>
        </form>
    </div>
</div>