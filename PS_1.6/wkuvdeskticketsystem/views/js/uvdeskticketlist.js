/**
* 2010-2017 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
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
*/

$(document).ready(function() {
	// on change of all checkbox
    $(document).on('change', '#wk_uvdeskticket_list_all', function() {
        $('input[name="wk_uvdeskticket_list[]"]').prop('checked', $(this).prop("checked"));
    });    
    
    // on change of a particular checkbox
    $('input[name="wk_uvdeskticket_list[]"]').change(function(){ 
        if(false == $(this).prop("checked")) {
            $("#wk_uvdeskticket_list_all").prop('checked', false);
        }
    });
    
    if (typeof(backend_controller) != 'undefined') {
	    //Tinymce editor
	    tinySetup({
	        editor_selector: "wk_tinymce",
	        width: 720
	    });
    }

    $(document).on('click', '.attach-file', function () {
        var child = $(this).next('.uploader').children('.fileUpload');
	    child.trigger('click');
	});

	$('#addFile').on('click', function () {
	    var attachHTML = '<div class="labelWidget"><i class="icon-trash remove-file" onclick="$(this).parent().remove();"></i><label class="attach-file pointer"></label><div class="uploader" style="display:none;"><input type="file" name="attachment[]" class="fileUpload"></div></div>';
	    $(this).before(attachHTML);
	});

    $(document).on('change', 'input[name="attachment[]"]', function () {
        var size = this.files[0].size/1000;
        var limit = 1;
        var max = 10;
        var maxsize = 300000;
        if(this.type == 'file') {
          fileName = this.value;
          var file_extension = fileName.split('.').pop(); 
          if(size < maxsize) {
            var getImagePath = URL.createObjectURL(this.files[0]);
            $(this).parent().prev().css('background-image', 'url(' + getImagePath + ')');
            $(this).parent().prev().css('background-size', 'cover');
            limit++;
            return true; 
          }
        }
        if(limit > max) {
          alert(max_file+max);
        } else {
          alert(invalid_file);
          this.value = "";
          return false;
        }
    });

    //get All Agent
    $(document).on('click', '.getAllAgent', function () {
        var allAgentDetails = allAgentList;
        var wkagentlist = $(this).next('.wk-agent-list');
        if(wkagentlist.is(':hidden')) {
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
        if(wkagentlist.is(':hidden')) {
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
        if ($(this).val() != '') {
            $(this).next('.wk-agent-list').html('<div class="get-agent-list"></div>');
            $(this).before('<i class="icon-spinner icon-spin"></i>');
            $(this).next('.wk-agent-list').show();
            searchTicketAgent($(this).val(), 1);
        } else {
            $(this).next('.wk-agent-list').hide();
        }
    });

    //Filter customers
    $("#filter-customer").keyup(function() {
        $('.icon-spin').remove();
        if ($(this).val() != '') {
            $(this).next('.wk-customer-list').html('<div class="get-customer-list"></div>');
            $(this).before('<i class="icon-spinner icon-spin"></i>');
            filterTickets($(this).val(), 'customer');
        } else {
            $('.wk-customer-list').hide();
        }
    });

    if (typeof(backend_controller) != 'undefined' && typeof(ticketId) == 'undefined') { //at admin list page only
        loadFilterData('group'); //Call group on page load
        loadFilterData('team'); //Call team on page load
        loadFilterData('priority'); //Call priority on page load
        loadFilterData('type'); //Call type on page load
    }

    //Filter group, team, priority, type
    $(document).on('change', '.wk-filter-ticket', function () {
        var filter_id = $(this).val();
        if (filter_id != '') {
            var filteraction = $(this).find(':selected').data('action');
            window.location.href = uvdesk_ticket_controller+'&'+filteraction+'='+filter_id;
        } else {
            window.location.href = uvdesk_ticket_controller;
        }
    });

    $(document).on('click', '.assignAgentBtn', function () {
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
                        agent_container.siblings('.getAllAgent').html('<span class="badge badge-sm badge-primary"><i class="icon-pencil"></i></span> '+member_firstname);
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
                                    $('#wk_ticket_row_'+tvalue).remove();
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
                        ticketId : ticketId,
                        collaborator_id : collaborator_id,
                        action: "deleteCollaborator",
                        ajax: true
                    },
                    success: function(result) {
                        $('#wk-loading-overlay').hide();
                        if (result != '0') {
                            $('#coll-div-'+collaborator_id).remove();
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

    if (typeof(ticketId) != 'undefined') { //at view ticket page only
        loadTicketThreads(ticketId, 1);

        $(document).on('click', '.show-more-thread', function() {
            var threadPage = $('#threadPage').val();
            loadTicketThreads(ticketId, threadPage);
        });
    }
});

function loadTicketThreads(ticketId, threadPage)
{
    if (ticketId != '') {
        $('#button-load').removeClass('show-more-thread');
        $('#button-load').html('<i class="icon-spin icon-spinner"></i>');

        $.ajax({
            url: uvdesk_ticket_controller,
            method: 'POST',
            dataType: 'json',
            data: {
                ticketId : ticketId,
                threadPage : threadPage,
                action: "getTicketThreads",
                ajax: true
            },
            success: function(resultThreads) {
                if (resultThreads != '0') {
                    var threadHTML = '';
                    $.each(resultThreads.threads, function(tindex, thread) {
                        threadHTML += '<div class="thread"><div class="col-sm-12 thread-created-info text-center"><span class="info"><span id="thread'+thread.user.id+'" class="copy-thread-link">#'+thread.user.id+'</span> '+thread.fullname+' '+replied+'</span><span class="text-right date pull-right">'+thread.formatedCreatedAt+'</span></div><div class="col-sm-12"><div class=""><div class="pull-left"><span class="round-tabs">';
                        if (thread.user.smallThumbnail) {
                            threadHTML += '<img src="'+thread.user.smallThumbnail+'">';
                        } else {
                            threadHTML += '<img src="https://cdn.uvdesk.com/uvdesk/images/d94332c.png">';
                        }
                        threadHTML += '</span></div><div class="thread-info"><div class="thread-info-row first"><span class="cust-name"><strong>'+thread.fullname+'</strong></span>';
                        if (thread.userType) {
                            threadHTML += '<label class="user-type customer label label-info">'+thread.userType+'</label>';
                        }
                        threadHTML += '</div><div class="thread-info-row"></div></div><div class="clearfix"></div></div><div class="thread-body"><div class="reply"><div class="main-reply">'+thread.reply+'</div></div>';
                        if (thread.attachments) {
                            threadHTML += '<div class="attachments">';
                            $.each(thread.attachments, function(aindex, attachment) {
                                threadHTML += '<a href="'+uvdesk_ticket_controller+'&attach='+attachment.id+'"><i class="icon-download wk-attachment" data-attachment-id="'+attachment.id+'" title="'+attachment.name+'"></i></a>';
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
                        $('html,body').animate({scrollTop: parseInt($('#wk-ticket-reply-section').offset().top) - 100}, 2000);
                    }
                } else {
                    $('#button-load').html(all_expended);
                }
            }
        });
    }
}

function getAllAgentList(agentHTML, allAgentDetails, filterAgent)
{
    agentHTML += '<div class="get-agent-list"><ul class="wk-uvdesk-dropdown-menu inner" role="listbox" aria-expanded="true">';
    if (allAgentDetails) {
        $.each(allAgentDetails, function(index, agentDetail) {
            if (filterAgent) {//if search by filter
                agentHTML += '<li><a href="'+uvdesk_ticket_controller+'&agent='+agentDetail.id+'">';
            } else {
                agentHTML += '<li class="assignAgentBtn" data-member-id="'+agentDetail.id+'" data-name="'+agentDetail.name+'"><a>';
            }
            if (agentDetail.smallThumbnail) {
                agentHTML += '<span class="round-tabs two"><img src="'+agentDetail.smallThumbnail+'"></span>';
            } else {
                agentHTML += '<span class="round-tabs two"><img src="https://cdn.uvdesk.com/uvdesk/images/e09dabf.png"></span>';
            }
            agentHTML += '<div class="name">'+agentDetail.name+'</div>';
            agentHTML += '</a></li>';
        });
    }
    agentHTML += '</ul></div>';

    return agentHTML;
}

function searchTicketAgent(search_member_name, filterAgent)
{
    if(typeof xhr !== 'undefined' && xhr.readyState != 4){
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
                    $('.icon-spin').remove();
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
        $('.icon-spin').remove();
    }
}

function filterTickets(search_name, filterValue)
{
    if(typeof xhr !== 'undefined' && xhr.readyState != 4){
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
                    $('.wk-'+filterValue+'-list').show();
                    $('.icon-spin').remove();
                    if (result != '0') {
                        if (result != '') {
                            var wkagentlist = $('.get-'+filterValue+'-list');
                            var filterHTML = '';
                            wkagentlist.html(getAllFilteredList(filterHTML, result, filterValue));
                        } else {
                            $('.wk-'+filterValue+'-list').hide();
                        }
                    } else {
                        alert(some_error);
                    }
                }
            });
    } else {
        $('.icon-spin').remove();
    }
}

function getAllFilteredList(filterHTML, allFilterDetails, filterValue)
{
    filterHTML += '<div class="get-'+filterValue+'-list"><ul class="wk-uvdesk-dropdown-menu inner" role="listbox" aria-expanded="true">';
    if (allFilterDetails) {
        $.each(allFilterDetails, function(index, filterDetail) {
            filterHTML += '<li><a href="'+uvdesk_ticket_controller+'&'+filterValue+'='+filterDetail.id+'">';
            if (filterDetail.smallThumbnail) {
                filterHTML += '<span class="round-tabs two"><img src="'+filterDetail.smallThumbnail+'"></span>';
            } else {
                filterHTML += '<span class="round-tabs two"><img src="https://cdn.uvdesk.com/uvdesk/images/e09dabf.png"></span>';
            }
            filterHTML += '<div class="name">'+filterDetail.name+'</div></a></li>';
        });
    }
    filterHTML += '</ul></div>';

    return filterHTML;
}

//display by ajax group, team, priority, type on page load
function loadFilterData(filterAction)
{
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
                filterHTML += '<option data-action="'+filterAction+'" value="'+fvalue.id+'" ';
                if (activeFilter == fvalue.id) {
                    filterHTML += 'selected="selected"';
                }
                filterHTML += '>'+fvalue.name+'</option>';
            });

            $('#filter-'+filterAction+'').append(filterHTML);
        }
    });
}