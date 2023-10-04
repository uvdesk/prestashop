/**
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
*/

$(document).ready(function() {
    //load Tinymce
    if (typeof allowTinymce != 'undefined') {
        tinymce.init({
            selector: '.wk_tinymce'
        });
    }

    if (typeof allowDatepicker != 'undefined') {
        //Display datepicker
        $('.wk-datepicker').datepicker({
            dateFormat: "yy-mm-dd",
        });

        $('.wk-timepicker').timepicker({
            timeFormat: 'hh:mm tt',
            showSecond:true,
            ampm: true
        });

        $('.wk-datetimepicker').datetimepicker({
            dateFormat: "yy-mm-dd",
            timeFormat: 'hh:mm:ss'
        });
    }

    // on change of all checkbox
    $(document).on('change', '#wk_uvdeskticket_list_all', function() {
        $('input[name="wk_uvdeskticket_list[]"]').prop('checked', $(this).prop("checked"));
    });

    // on change of a particular checkbox
    $('input[name="wk_uvdeskticket_list[]"]').change(function() {
        if (false == $(this).prop("checked")) {
            $("#wk_uvdeskticket_list_all").prop('checked', false);
        }
    });

    $(document).on('click', '.attach-file', function() {
        var fileattach = $(this).next('.uploader').children('.fileUpload');
        fileattach.trigger('click');
    });

    $('#addFile').on('click', function() {
        var attachHTML = '<div class="labelWidget"><i style="font-size:16px;" class="material-icons remove-file" onclick="$(this).parent().remove();">&#xE872;</i><label class="attach-file pointer"></label><div class="uploader" style="display:none;"><input type="file" name="attachment[]" class="fileUpload"></div></div>';
        $(this).before(attachHTML);
    });

    $(document).on('change', 'input[name="attachment[]"]', function() {
        var size = this.files[0].size / 1000;
        var limit = 1;
        var max = 10;
        var maxsize = 300000;
        if (this.type == 'file') {
            fileName = this.value;
            var file_extension = fileName.split('.').pop();
            if (size < maxsize) {
                var getImagePath = URL.createObjectURL(this.files[0]);
                $(this).parent().prev().css('background-image', 'url(' + getImagePath + ')');
                $(this).parent().prev().css('background-size', 'cover');
                limit++;
                return true;
            }
        }
        if (limit > max) {
            alert(max_file + max);
        } else {
            alert(invalid_file);
            this.value = "";
            return false;
        }
    });

    //get All Agent
    $(document).on('click', '.getAllAgent', function() {
        var allAgentDetails = allAgentList;
        var wkagentlist = $(this).next('.wk-agent-list');
        if (wkagentlist.is(':hidden')) {
            $('.wk-agent-list').hide(); //hide from all other places
            var agentHTML = '<div class="bs-searchbox"><input type="text" class="form-control searchAgent" autocomplete="off" onkeyup="searchTicketAgent(this.value, 0);"></div>';
            wkagentlist.html(getAllAgentList(agentHTML, allAgentDetails, 0));
            wkagentlist.show();
        } else {
            wkagentlist.hide();
            wkagentlist.html('');
        }
    });

    //Filter Agents(members) on input box focus
    $("#filter-assigned").focus(function() {
        var allAgentDetails = allAgentList;
        var wkagentlist = $(this).next('.wk-agent-list');
        if (wkagentlist.is(':hidden')) {
            $('.wk-agent-list').hide(); //hide from all other places
            var agentHTML = '';
            wkagentlist.html(getAllAgentList(agentHTML, allAgentDetails, 1));
            wkagentlist.show();
        } else {
            wkagentlist.hide();
            wkagentlist.html('');
        }
    });

    //Filter Agents(members) on key up
    $("#filter-assigned").keyup(function() {
        $(this).siblings('.wk-icon-spin').remove();
        if ($(this).val() != '') {
            $(this).next('.wk-agent-list').html('<div class="get-agent-list"></div>');
            $(this).before('<i class="wk-icon-spinner wk-icon-spin"></i>');
            $(this).next('.wk-agent-list').show();
            searchTicketAgent($(this).val(), 1);
        } else {
            $(this).next('.wk-agent-list').hide();
        }
    });

    //Filter customers
    $("#filter-customer").keyup(function() {
        $(this).siblings('.wk-icon-spin').remove();
        if ($(this).val() != '') {
            $(this).next('.wk-customer-list').html('<div class="get-customer-list"></div>');
            $(this).before('<i class="wk-icon-spinner wk-icon-spin"></i>');
            filterTickets($(this).val(), 'customer');
        } else {
            $('.wk-customer-list').hide();
        }
    });

    //Hide search box
    $(document).click(function(event) {
        if (event.target.id == "filter-assigned") {
            if ($('.wk-customer-list').is(':visible')) {
                $('.wk-customer-list').hide();
            }
            return;
        } else {
            if ($('#filter-assigned').next('.wk-agent-list').is(':visible')) {
                $('#filter-assigned').next('.wk-agent-list').hide();
                $('#filter-assigned').next('.wk-agent-list').html('');
            }
        }

        if (event.target.id == "filter-customer") {
            if ($('#filter-assigned').next('.wk-agent-list').is(':visible')) {
                $('#filter-assigned').next('.wk-agent-list').hide();
                $('#filter-assigned').next('.wk-agent-list').html('');
            }
            return;
        } else {
            if ($('.wk-customer-list').is(':visible')) {
                $('.wk-customer-list').hide();
            }
        }
    });

    if (typeof backend_controller != 'undefined' && typeof ticketId == 'undefined') { //at admin list page only
        loadFilterData('group'); //Call group on page load
        loadFilterData('team'); //Call team on page load
        loadFilterData('priority'); //Call priority on page load
        loadFilterData('type'); //Call type on page load
    }

    //Filter group, team, priority, type
    $(document).on('change', '.wk-filter-ticket', function() {
        var filter_id = $(this).val();
        var filteraction = $(this).find(':selected').data('action');

        var extraParams = '';

        if (activeAgent != '') {
            extraParams += '&agent=' + activeAgent;
        }
        if (activeCustomer != '') {
            extraParams += '&customer=' + activeCustomer;
        }
        if (activeGroup != '' && filteraction != 'group') {
            extraParams += '&group=' + activeGroup;
        }
        if (activeTeam != '' && filteraction != 'team') {
            extraParams += '&team=' + activeTeam;
        }
        if (activePriority != '' && filteraction != 'priority') {
            extraParams += '&priority=' + activePriority;
        }
        if (activeType != '' && filteraction != 'type') {
            extraParams += '&type=' + activeType;
        }

        if (filter_id != '') {
            window.location.href = uvdesk_ticket_controller + extraParams + '&' + filteraction + '=' + filter_id;
        } else {
            window.location.href = uvdesk_ticket_controller + extraParams;
        }
    });

    $(document).on('click', '.assignAgentBtn', function() {
        var agent_container = $(this).closest('.wk-agent-list');
        var ticket_id = agent_container.data('ticket-id');
        var member_id = $(this).data('member-id');
        var member_firstname = $(this).data('name').split(' ').slice(0, -1).join(' ');
        if (ticket_id != '' && member_id != '') {
            $('.wk-agent-list').hide();
            $('#wk-loading-overlay').show();
            $.ajax({
                url: uvdesk_ticket_controller,
                method: 'POST',
                dataType: 'json',
                data: {
                    member_id: member_id,
                    ticket_id: ticket_id,
                    action: "assignAgent",
                    ajax: true
                },
                success: function(result) {
                    $('#wk-loading-overlay').hide();
                    if (result != '0') {
                        //alert(assign_success);
                        agent_container.siblings('.getAllAgent').html('<span class="badge badge-sm badge-primary"><i class="icon-pencil"></i></span> ' + member_firstname);
                    } else {
                        alert(some_error);
                    }
                }
            });
        } else {
            alert(some_error);
        }
    });

    //Ticket deleted by Admin
    $(document).on('click', '#wk-delete-tickets', function() {
        if ($("input[name='wk_uvdeskticket_list[]']:checked").val()) {
            if (confirm(confirm_delete)) {
                var checked_ticketsIds = [];
                $.each($("input[name='wk_uvdeskticket_list[]']:checked"), function() {
                    checked_ticketsIds.push($(this).val());
                });

                if (checked_ticketsIds != '') {
                    $('#wk-loading-overlay').show();
                    $.ajax({
                        url: uvdesk_ticket_controller,
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            checked_ticketsIds: checked_ticketsIds,
                            action: "deleteCustomerTickets",
                            ajax: true
                        },
                        success: function(result) {
                            $('#wk-loading-overlay').hide();
                            if (result != '0') {
                                alert(delete_success);
                                $.each(checked_ticketsIds, function(tindex, tvalue) {
                                    $('#wk_ticket_row_' + tvalue).remove();
                                });
                                //window.location.href = window.location.href;
                            } else {
                                alert(some_error);
                            }
                        }
                    });
                }

                return true;
            }
        } else {
            alert(choose_one);
        }

        return false;
    });

    //Remove collaborator from fronend view ticket
    $(document).on('click', '.removeCollaborator', function() {
        if (confirm(confirm_delete)) {
            var collaborator_id = $(this).data('col-id');
            if (collaborator_id != '') {
                $('#wk-loading-overlay').show();
                $.ajax({
                    url: uvdesk_ticket_controller,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        ticketId: ticketId,
                        collaborator_id: collaborator_id,
                        action: "deleteCollaborator",
                        ajax: true
                    },
                    success: function(result) {
                        $('#wk-loading-overlay').hide();
                        if (result != '0') {
                            $('#coll-div-' + collaborator_id).remove();
                        } else {
                            alert(some_error);
                        }
                    }
                });
                return true;
            }
        }

        return false;
    });

    if (typeof ticketId != 'undefined') { //at view ticket page only
        loadTicketThreads(ticketId, 1);

        $(document).on('click', '.show-more-thread', function() {
            var threadPage = $('#threadPage').val();
            loadTicketThreads(ticketId, threadPage);
        });
    }

    //Hide and show custom fields according to ticket type on create ticket page
    // Also add and remove required attribute for validation
    $(document).on('change', '#ticket-type', function() {
        var typeId = $(this).val();
        if (typeId) {
            $('.wk-dependent').hide();

            //remove required valiation from all dependent custom field
            $.each($('.wk-dependent'), function(resindex, resvalue) {
                var customFieldId = $(this).attr('data-custom-field');
                $('input[name="customFields[' + customFieldId + ']"]').removeAttr("required");
            });

            //Get all required fields according to ticket type
            if (typeof nonDependentFields != 'undefined') {
                var allRequiredFields = jQuery.parseJSON(nonDependentFields);
            } else {
                var allRequiredFields = [];
            }

            var allDependentDiv = $('.wk-dependent' + typeId);
            $.each(allDependentDiv, function(reskey, resval) {
                var customFieldId = $(this).attr('data-custom-field');
                if ($(this).attr('data-required') == '1') {
                    //required condition in dependent custom field of select type
                    $('input[name="customFields[' + customFieldId + ']"]').attr("required", "required");

                    allRequiredFields.push(customFieldId);
                }
            });

            $('input[name="requiedCustomFields"]').val(allRequiredFields);
            $('.wk-dependent' + typeId).show();
        }
    });

    //Change checkbox required validation
    var requiredCheckboxes = $('.wk-form-checkbox :checkbox[required]');
    requiredCheckboxes.change(function() {
        if (requiredCheckboxes.is(':checked')) {
            requiredCheckboxes.removeAttr('required');
        } else {
            requiredCheckboxes.attr('required', 'required');
        }
    });

    //delete filter of agent or customer in backend
    $(document).on('click', '.closefilter', function() {
        var closefilteraction = $(this).data('action');

        var extraParams = '';

        if (activeAgent != '' && closefilteraction != 'agent') {
            extraParams += '&agent=' + activeAgent;
        }
        if (activeCustomer != '' && closefilteraction != 'customer') {
            extraParams += '&customer=' + activeCustomer;
        }
        if (activeGroup != '' && closefilteraction != 'group') {
            extraParams += '&group=' + activeGroup;
        }
        if (activeTeam != '' && closefilteraction != 'team') {
            extraParams += '&team=' + activeTeam;
        }
        if (activePriority != '' && closefilteraction != 'priority') {
            extraParams += '&priority=' + activePriority;
        }
        if (activeType != '' && closefilteraction != 'type') {
            extraParams += '&type=' + activeType;
        }

        window.location.href = uvdesk_ticket_controller + extraParams;
    });
});

function loadTicketThreads(ticketId, threadPage) {
    if (ticketId != '') {
        $('#button-load').removeClass('show-more-thread');
        $('#button-load').html('<i class="wk-icon-spin wk-icon-spinner"></i>');

        $.ajax({
            url: uvdesk_ticket_controller,
            method: 'POST',
            dataType: 'json',
            data: {
                ticketId: ticketId,
                threadPage: threadPage,
                action: "getTicketThreads",
                ajax: true
            },
            success: function(resultThreads) {
                if (resultThreads !== null && resultThreads != '0') {
                    var threadHTML = '';
                    $.each(resultThreads.threads, function(tindex, thread) {
                        threadHTML += '<div class="thread"><div class="col-sm-12 thread-created-info text-center"><span class="info"><span id="thread' + thread.id + '" class="copy-thread-link">#' + thread.id + '</span> ' + thread.fullname + ' ' + replied + '</span>';
                        if (typeof thread.formatedCreatedAt !== 'undefined' && thread.formatedCreatedAt !== null) {
                            threadHTML += '<span class="text-right date pull-right">' + thread.formatedCreatedAt + '</span>';
                        }
                        threadHTML += '</div><div class="col-sm-12"><div class=""><div class="pull-left"><span class="round-tabs">';
                        if (typeof thread.user !== 'undefined' && thread.user !== null && typeof thread.user.smallThumbnail !== 'undefined' && thread.user.smallThumbnail !== null) {
                            threadHTML += '<img src="' + thread.user.smallThumbnail + '">';
                        } else {
                            threadHTML += '<img src="' + wk_uvdesk_user_img + '">';
                        }
                        threadHTML += '</span></div><div class="thread-info"><div class="thread-info-row first"><span class="cust-name"><strong>' + thread.fullname + '</strong></span>';
                        if (typeof thread.userType !== 'undefined' && thread.userType !== null) {
                            threadHTML += '<label class="user-type customer label label-info">' + thread.userType + '</label>';
                        }
                        threadHTML += '</div><div class="thread-info-row"></div></div><div class="clearfix"></div></div><div class="thread-body"><div class="reply"><div class="main-reply">' + thread.reply + '</div></div>';
                        if (thread.attachments) {
                            threadHTML += '<div class="attachments">';
                            $.each(thread.attachments, function(aindex, attachment) {
                                threadHTML += '<a href="' + uvdesk_ticket_controller + '&attach=' + attachment.id + '"><i class="material-icons wk-attachment" data-attachment-id="' + attachment.id + '" title="' + attachment.name + '">&#xE2C0;</i></a>';
                            });
                            threadHTML += '</div>';
                        }
                        threadHTML += '</div></div></div><hr>';
                    });

                    $('.ticket-thread').prepend(threadHTML);

                    if (resultThreads.threadsPagination.current == resultThreads.threadsPagination.endPage) {
                        //No more thread
                        $('#button-load').removeClass('show-more-thread');
                        $('#button-load').html(all_expended);
                    } else {
                        //More theads available
                        $('#button-load').addClass('show-more-thread');
                        $('#button-load').html(show_more);
                    }

                    $('#threadPage').val(resultThreads.threadsPagination.next); //next page for thread
                    if (resultThreads.threadsPagination.current == 1) {
                        //Scroll page to submit button
                        $('html,body').animate({ scrollTop: parseInt($('#wk-ticket-reply-section').offset().top) - 100 }, 2000);
                    }
                } else {
                    $('#button-load').html(all_expended);
                }
            }
        });
    }
}

//filter agent
function getAllAgentList(agentHTML, allAgentDetails, filterAgent) {
    agentHTML += '<div class="get-agent-list"><ul class="wk-uvdesk-dropdown-menu inner" role="listbox" aria-expanded="true">';
    if (allAgentDetails) {
        var extraParams = '';

        if (activeCustomer != '') {
            extraParams += '&customer=' + activeCustomer;
        }
        if (activeGroup != '') {
            extraParams += '&group=' + activeGroup;
        }
        if (activeTeam != '') {
            extraParams += '&team=' + activeTeam;
        }
        if (activePriority != '') {
            extraParams += '&priority=' + activePriority;
        }
        if (activeType != '') {
            extraParams += '&type=' + activeType;
        }

        $.each(allAgentDetails, function(index, agentDetail) {
            if (filterAgent) { //if search by filter
                agentHTML += '<li><a href="' + wk_whole_url + extraParams + '&agent=' + agentDetail.id + '">';
            } else {
                agentHTML += '<li class="assignAgentBtn" data-member-id="' + agentDetail.id + '" data-name="' + agentDetail.name + '"><a>';
            }
            if (agentDetail.smallThumbnail) {
                agentHTML += '<span class="round-tabs two"><img src="' + agentDetail.smallThumbnail + '"></span>';
            } else {
                agentHTML += '<span class="round-tabs two"><img src="' + wk_uvdesk_user_img + '"></span>';
            }
            agentHTML += '<div class="name">' + agentDetail.name + '</div>';
            agentHTML += '</a></li>';
        });
    }
    agentHTML += '</ul></div>';

    return agentHTML;
}

function searchTicketAgent(search_member_name, filterAgent) {
    if (typeof xhr !== 'undefined' && xhr.readyState != 4) {
        xhr.abort();
    }

    if (search_member_name != '') {
        xhr = $.ajax({
            url: uvdesk_ticket_controller,
            method: 'POST',
            dataType: 'json',
            data: {
                search_member_name: search_member_name,
                action: "searchAgentByName",
                ajax: true
            },
            success: function(result) {
                $('.wk-icon-spin').remove();
                if (result != '0') {
                    var allAgentDetails = result;
                    var wkagentlist = $('.get-agent-list');
                    var agentHTML = '';
                    wkagentlist.html(getAllAgentList(agentHTML, allAgentDetails, filterAgent));
                } else {
                    alert(some_error);
                }
            }
        });
    } else {
        $('.wk-icon-spin').remove();
    }
}

function filterTickets(search_name, filterValue) {
    if (typeof xhr !== 'undefined' && xhr.readyState != 4) {
        xhr.abort();
    }

    if (search_name != '') {
        xhr = $.ajax({
            url: uvdesk_ticket_controller,
            method: 'POST',
            dataType: 'json',
            data: {
                search_name: search_name,
                filterValue: filterValue,
                action: "searchFilterByName",
                ajax: true
            },
            success: function(result) {
                $('.wk-' + filterValue + '-list').show();
                $('.wk-icon-spin').remove();
                if (result != '0') {
                    var wkagentlist = $('.get-' + filterValue + '-list');
                    if (result != '') {
                        var filterHTML = '';
                        wkagentlist.html(getAllFilteredList(filterHTML, result, filterValue));
                    } else {
                        //$('.wk-' + filterValue + '-list').hide();
                        $('.wk-' + filterValue + '-list').css('min-height', 'auto');
                        $('.wk-' + filterValue + '-list').css('padding', '5px');
                        wkagentlist.html(no_result);
                    }
                } else {
                    alert(some_error);
                }
            }
        });
    } else {
        $('.wk-icon-spin').remove();
    }
}

//filter customer and other
function getAllFilteredList(filterHTML, allFilterDetails, filterValue) {
    filterHTML += '<div class="get-' + filterValue + '-list"><ul class="wk-uvdesk-dropdown-menu inner" role="listbox" aria-expanded="true">';
    if (allFilterDetails) {
        var extraParams = '';

        if (activeAgent != '') {
            extraParams += '&agent=' + activeAgent;
        }
        if (activeGroup != '') {
            extraParams += '&group=' + activeGroup;
        }
        if (activeTeam != '') {
            extraParams += '&team=' + activeTeam;
        }
        if (activePriority != '') {
            extraParams += '&priority=' + activePriority;
        }
        if (activeType != '') {
            extraParams += '&type=' + activeType;
        }

        $.each(allFilterDetails, function(index, filterDetail) {
            filterHTML += '<li><a href="' + wk_whole_url + extraParams + '&' + filterValue + '=' + filterDetail.id + '">';
            if (filterDetail.smallThumbnail) {
                filterHTML += '<span class="round-tabs two"><img src="' + filterDetail.smallThumbnail + '"></span>';
            } else {
                filterHTML += '<span class="round-tabs two"><img src="' + wk_uvdesk_user_img + '"></span>';
            }
            filterHTML += '<div class="name">' + filterDetail.name + '</div></a></li>';
        });
    }
    filterHTML += '</ul></div>';

    return filterHTML;
}

//display by ajax group, team, priority, type on page load
function loadFilterData(filterAction) {
    $.ajax({
        url: uvdesk_ticket_controller,
        method: 'POST',
        dataType: 'json',
        data: {
            filterAction: filterAction,
            action: "loadFilterData",
            ajax: true
        },
        success: function(filterResult) {
            var filterHTML = '';
            $.each(filterResult, function(findex, fvalue) {
                filterHTML += '<option data-action="' + filterAction + '" value="' + fvalue.id + '" ';
                $.each(jQuery.parseJSON(activeFilter), function(sindex, svalue) {
                    if (svalue == fvalue.id) {
                        filterHTML += 'selected="selected"';
                    }
                });
                filterHTML += '>' + fvalue.name + '</option>';
            });

            $('#filter-' + filterAction + '').append(filterHTML);
        }
    });
}