<script type="text/javascript" src="<?=asset_url()?>js/bootstrap-notify.min.js"></script>
<script type="text/javascript" src="<?=asset_url()?>js/bootstrap-select.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?=asset_url()?>css/bootstrap-select.min.css"/>
<div class="container">
	<div class="btn-toolbar">
		<a id="save_button" type="button" class="btn btn-success">
			<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
		</a>
		<span class="btn-separator"></span>
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#copyModal">
			<span class="glyphicon glyphicon-copy" aria-hidden="true"></span> Copy schedule...
			<!-- Modal with two calendars -->
		</button>
		<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#removeModal">
			<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete schedules...
			<!-- Modal with selectable days -->
		</button>
	</div>
	<hr>
<script>
    // Colors used in events
    var ttColors = {
        "saved": "#5cb85c",
        "modified": "#5bc0de",
        "deleted": "#d9534f",
        "public": "#f0ad4e" 
    };
    function alerter(alert_type, alert_message) {
		// alerter function for on-screen alerts
		$.notify({
		// options
		message: alert_message 
		},{
			// settings
			type: alert_type,
			mouse_over: "pause",
			timer: 5000,
			animate: {
				enter: 'animated fadeInDown',
				exit: 'animated fadeOutUp'
			}
		});
	}
    function saveData() {
    	$('#save_button').addClass("disabled");
        $.ajax({
            type: "POST",
            data: {'csrf_test_name': csrf_token},
            url: "timetable_save",
            success: function(data) {
            	$('#save_button').removeClass("disabled");
                // return success
                if (data.length > 0) {
                    $('#calendar').fullCalendar('refetchEvents');
                    //alert("events fetched!");
                    var json = JSON.parse(data);
                    if(json.success)
                    {
                    	alerter("success", "Saving successful. Notifications were sent to emails:" + json.emails_sent);
                    }
                    
                }
            },
        	error: function(data) {
	        	$('#save_button').removeClass("disabled");
	        	alerter("danger", "Sorry, error happened.");
	        }
        });
    }
    
    function removeEvent(id) {
        var event = $("#calendar").fullCalendar( 'clientEvents', id)[0];
        var post_data = {
            "id": event.id,
            "assigned": event.assigned,
            "group": event.group,
            "start": moment(event._start).format("YYYY-MM-DD HH:mm:ss"),
            "end": moment(event._end).format("YYYY-MM-DD HH:mm:ss"),
            'csrf_test_name': csrf_token
        }
        $.ajax({
            type: "POST",
            url: "timetable_remove_slot",
            data: post_data,
            success: function(data) {
                // return success
                if (data.length > 0) {
                    event.color = ttColors.deleted;
                    $('#calendar').fullCalendar('updateEvent', event);
                    $('#eventModal').modal("hide");
                }
            }
        });
    }
    
    function restoreEvent(id) {
    	var event = $("#calendar").fullCalendar( 'clientEvents', id)[0];
        var post_data = {
            "id": event.id,
            'csrf_test_name': csrf_token
        };
        $.ajax({
            type: "POST",
            url: "timetable_restore_slot",
            data: post_data,
            success: function(data) {
                alert(data);
                var json = JSON.parse(data);
//                 console.log(json);
                // return success
                if (json.success) {
                    event.assigned = json.assigned;
                    event.group = json.group;
                    event.start = json.start;
                    event.end = json.end;
                    event.title = json.title;
                    event.color = json.color;
                    $('#calendar').fullCalendar('updateEvent', event);
                }
            }
        });
    }
    
    
    $('#save_button').click(function(){
		saveData();
	});

    //Schedule functions 
    function copySchedules() {
		var sDate = $("#startDate").data("DateTimePicker").date();
		var eDate = $("#endDate").data("DateTimePicker").date();
		var csDate = $("#copyStartDate").data("DateTimePicker").date();
        if ( sDate === null || eDate === null || csDate === null ) {
            alert("Dates cannot be empty.");
            return;
        }
        if ( sDate > eDate ) {
            alert("Start date must be earlier than end date");
            return;
        }
        if ( eDate >= csDate ) {
            alert("End date must be earlier Copy to date");
            return;
        }
        sDate = sDate.format("YYYY-MM-DD");
		eDate = eDate.format("YYYY-MM-DD");
		csDate = csDate.format("YYYY-MM-DD");
    	var post_data = {
              "startDate" : sDate,
              "endDate" : eDate,
              "copyStartDate" : csDate, 
              'csrf_test_name': csrf_token
        };
		$.ajax({
        	type: "POST",
            url: "schedule_copy",
            data: post_data,
            success: function(data) {
				var d = JSON.parse(data);
				$("#copyModal").modal("hide");
                if (!d.hasOwnProperty('Error') )
                {
                	alerter("success", "Schedule is <strong>copied! " + d.affected + "</strong> schedules were created.");
                	$('#calendar').fullCalendar('refetchEvents');
                }
                else 
                {
					alerter("warning",d.Error);
                }
                
            }
    	});
    }
    function removeSchedules() {
		var sDate = $("#remove_startDate").data("DateTimePicker").date();
		var eDate = $("#remove_endDate").data("DateTimePicker").date();

        if ( sDate === null || eDate === null) {
            alert("Dates cannot be empty.");
            return;
        }
        if ( sDate > eDate ) {
            alert("Start date must be earlier than end date");
            return;
        }
        sDate = sDate.format("YYYY-MM-DD");
		eDate = eDate.format("YYYY-MM-DD");
    	var post_data = {
              "startDate" : sDate,
              "endDate" : eDate, 
              'csrf_test_name': csrf_token
        };
		$.ajax({
        	type: "POST",
            url: "schedule_delete",
            data: post_data,
            success: function(data) 
            {
                $("#removeModal").modal("hide");
                var json = JSON.parse(data);
                var deleted_ids = json['deleted_ids'];
                for (var id in deleted_ids) {
                    var event = $('#calendar').fullCalendar( 'clientEvents', id)[0];
                    event.color = ttColors.deleted; // Red color
                    $('#calendar').fullCalendar('updateEvent', event);
                }
            }
    	});
    }
    
    function alerter(alert_type, alert_message) {
    	// alerter function for on-screen alerts
    	$.notify({
    	// options
    	message: alert_message 
    	},{
    		// settings
    		type: alert_type,
    		animate: {
    			enter: 'animated fadeInDown',
    			exit: 'animated fadeOutUp'
    		}
    	});
    }
    function confirmEvent(id) 
    {
    	var event = $("#calendar").fullCalendar( 'clientEvents', id)[0];
        var post_data = {
            "id": event.id,
            "assigned": $("#supervisionpicker").val(),
            "group": $("#targetpicker").val(),
            "start": $('#startpicker').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss"),
            "end": $('#endpicker').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss"),
            "title": event.title, 
            'csrf_test_name': csrf_token
        };
        $.ajax({
            type: "POST",
            url: "timetable_confirm_slot",
            data: post_data,
            success: function(data) {
                //alert(data);
                var json = JSON.parse(data);
                // return success
                if (json.success) {
                    event.assigned = json.assigned;
                    event.group = json.group;
                    event.start = json.start;
                    event.end = json.end;
                    event.title = json.title;
                    event.color = json.color;
                    $('#calendar').fullCalendar('updateEvent', event);
                }
            }
        });
    }
	$(document).ready(function() {

		/* initialize the Datepickers
		-----------------------------------------------------------------*/
		$( ".modaldate" ).datetimepicker({
			format: "LL",
			locale: "en-gb"
			});
		/* initialize the external events
		-----------------------------------------------------------------*/
		
		$('#external-events .fc-event').each(function() {

			// store data so the calendar knows to render an event upon drop
			$(this).data('event', {
				title: $.trim($(this).text()), // use the element's text as the event title
                assigned: $.trim($(this).data( "assigned" )),
                group: 3,
				stick: false // maintain when user navigates (see docs on the renderEvent method)
			});

			// make the event draggable using jQuery UI
			$(this).draggable({
				zIndex: 999,
				revert: true,      // will cause the event to go back to its
				revertDuration: 0  //  original position after the drag
			});

		});


		/* initialize the calendar
		-----------------------------------------------------------------*/

		$('#calendar').fullCalendar({
            eventSources: [
            // your event source
                {
                    url: 'timetable_fetch_supervision_sessions',
                },
                {
                    url: 'timetable_fetch_mod_and_new_sessions', 
                    color: ttColors.modified
                },
                {
                    url: 'timetable_fetch_deleted_sessions', 
                    color: ttColors.deleted
                }
            ],
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
            allDaySlot: false,
			allDayDefault: false,
			defaultTimedEventDuration: '08:00:00',
            forceEventDuration: true,
            timeFormat: 'HH:mm',
            slotLabelFormat: 'HH:mm',
			editable: true,
            firstDay: 1,
			droppable: true, // this allows things to be dropped onto the calendar
			schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
			eventClick: function(event, element) {
// 				console.log(event);
                $('#modalTitle').html(event.title);
                $('#modalBody').html(event.description);
                $('#event_remove_button').attr('href',"javascript:removeEvent(" + event.id + ")");
                $('#event_discard_changes').attr('onclick',"javascript:restoreEvent(" + event.id + ")");
                $('#event_confirm_changes').attr('onclick',"javascript:confirmEvent(" + event.id + ")");
                $('#eventUrl').attr('href',event.url);
                $('#eventModal').modal();
				//open modal/tooltip for deletion and manual data entry
				$('#calendar').fullCalendar('updateEvent', event);
				//Place event's data to modal.
				$('#startpicker').data("DateTimePicker").date(event.start);
				$('#endpicker').data("DateTimePicker").date(event.end);
                $('#supervisionpicker').selectpicker('val', event.assigned);
                $('#targetpicker').selectpicker('val', event.group);
			},
			editable: true,
            eventRender: function(event, element){
                var assigned = event.assigned;
            },
			eventResize: function(event, delta, revertFunc) {
                //Resize callback
                var post_data = {
                    "id": event.id,
                    "assigned": event.assigned,
                    "group": event.group,
                    "start": moment(event.start).format("YYYY-MM-DD HH:mm:ss"),
                    "end": moment(event.end).format("YYYY-MM-DD HH:mm:ss"), 
                    'csrf_test_name': csrf_token
                }
				$.ajax({
                    type: "POST",
                    url: "timetable_modify_slot",
                    data: post_data,
                    success: function(data) {
                        // return success
                        if (data.length > 0) {
                            var json = JSON.parse(data);
                            if(json.success) {
                                event.title = json.title;
                                event.color = ttColors.modified;
                                $('#calendar').fullCalendar('updateEvent', event);
                            }
                        }
                    }
                });
			},
            eventDrop: function(event, delta, revertFunc) {
                //move callback
                var post_data = {
                    "id": event.id,
                    "assigned": event.assigned,
                    "group": event.group,
                    "start": moment(event.start).format("YYYY-MM-DD HH:mm:ss"),
                    "end": moment(event.end).format("YYYY-MM-DD HH:mm:ss"), 
                    'csrf_test_name': csrf_token
                }
				$.ajax({
                    type: "POST",
                    url: "timetable_modify_slot",
                    data: post_data,
                    success: function(data) {
                        // return success
                        if (data.length > 0) {
                            event.color = ttColors.modified;
                            $('#calendar').fullCalendar('updateEvent', event);
                        }
                    }
                });
			},
            eventReceive: function(event){
                //external event drop callback
//                 console.log(event);
                var post_data = {
                    "start": moment(event.start).format("YYYY-MM-DD HH:mm:ss"),
                    "end": moment(event.end).format("YYYY-MM-DD HH:mm:ss"),
                    "group": event.group,
                    "assigned": event.assigned, 
                    'csrf_test_name': csrf_token
                }
				$.ajax({
                    type: "POST",
                    url: "timetable_new_slot",
                    data: post_data,
                    success: function(data) {
                        // return success
                        if (data.length > 0) {
                            response = $.parseJSON(data);
                            //used for coloring events
                            event._id = response.id;
                            event.id = response.id;
                        }
                    }
                });
			}
		});
		


	});

</script>
	<!-- Copy Schedule Modal -->
	<div id="copyModal" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Copy selected schedules</h4>
	      </div>
	      <div class="modal-body">
		      <form class="form-horizontal">
		      	<div class="form-group">
		        <label class="control-label col-md-4" for="startDate">Select start date:</label> 
		        <div class="col-md-8">
		        	<input type="text" class="modaldate" id="startDate">
		        </div>
		        </div>
		        <div class="form-group">
			        <label class="control-label col-md-4" for="endDate">Select end date:</label>
			        <div class="col-md-8">
						<input type="text" class="modaldate" id="endDate">
					</div>
			        
		        </div>
		        <div class="form-group">
			        <label class="control-label col-md-4" for="copyStartDate">Copy to date and forth:</label> 
			        <div class="col-md-8">
						<input type="text" class="modaldate" id="copyStartDate">
					</div>
			    </div>
		        <p>Remember to save <b>before</b> copying!</p>
		      </form>
	      </div>
	      <div class="modal-footer">
		    <a type="button" class="btn btn-success" onclick="copySchedules();">Copy</a>
	      	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	
	  </div>
	</div>
	<!-- Remove Schedule Modal -->
	<div id="removeModal" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Remove selected schedules</h4>
	      </div>
	      <div class="modal-body">
	      	<form class="form-horizontal">
		      	<div class="form-group">
		        <label class="control-label col-md-4" for="remove_startDate">Select start date:</label> 
		        <div class="col-md-8">
		        	<input type="text" class="modaldate" id="remove_startDate">
		        </div>
		        </div>
		        <div class="form-group">
			        <label class="control-label col-md-4" for="remove_endDate">Select end date:</label>
			        <div class="col-md-8">
						<input type="text" class="modaldate" id="remove_endDate">
					</div>
		        </div>
		   </form>
	      </div>
	      <div class="modal-footer">
	      	<a type="button" class="btn btn-danger" onclick="removeSchedules();">
            	<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Remove
            </a>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	      </div>
	    </div>
	
	  </div>
	</div>
    <!-- Event Modal -->
    <div id="eventModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" id="event_header">Modify event</h4>
          </div>
          <div class="modal-body">
            <div class="row">
                <div class='col-sm-6'>
                    <div class="form-group">
                        <label for="startpicker">Start time:</label>
                        <div class='input-group date' id='startpicker'>
                            <input type='text' readonly="readonly" class="form-control" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class='col-sm-6'>
                    <script type="text/javascript">
                        $(function () {
                            $('#startpicker').datetimepicker({
                                locale: 'en-gb',
                                ignoreReadonly:true
                            });
                        });
                    </script>
                    <div class="form-group">
                        <label for="endpicker">End time:</label>
                        <div class='input-group date' id='endpicker'>
                            <input type='text' readonly="readonly" class="form-control" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class='col-sm-6'>
                    <script type="text/javascript">
                        $(function () {
                            $('#endpicker').datetimepicker({
                                locale: 'en-gb',
                                ignoreReadonly:true
                            });
                        });
                    </script>
                    <div class="form-group">
                        <label for="supervisionpicker">Supervisor:</label>
                        <select class="form-control selectpicker" id="supervisionpicker">
                            <?php foreach ($admins as $row ) {?>
                                <option value="<?=$row->id ?>"><?=$row->name; ?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
                <div class='col-sm-6'>
                    <div class="form-group">
                        <label for="targetpicker">Target group:</label>
                        <select class="form-control selectpicker" id="targetpicker">
                            <?php foreach ($groups as $row ) {?>
                                <option value="<?=$row->id ?>"><?=$row->name; ?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
            </div>

          </div>
          <div class="modal-footer">
          	<button id="event_confirm_changes" type="button" class="btn btn-success" data-dismiss="modal">Confirm changes</button>
          	<button id="event_discard_changes" type="button" class="btn btn-info" data-dismiss="modal">Discard unsaved changes</button>
            <a id="event_remove_button" type="button" class="btn btn-danger">
            	<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Remove
            </a>
            <a type="button" class="btn btn-default" data-dismiss="modal">Close</a>
          </div>
        </div>

      </div>
    </div>
	<div class="col-md-2">
        <div class="row">
            <h4>Supervisors</h4>
            <ul class="list-group" id='external-events'>
            <?php foreach ($admins as $row ) {?>
                <li class='fc-event list-group-item' id="<?php echo $row->id ?>" data-event='1'
                 data-assigned='<?=$row->id?>' style="cursor:pointer;">
                 <?php echo $row->id; ?> <?php echo $row->first_name . " " . $row->surname;?></li>
            <?php }?>
            </ul>
        </div>
        <div class="row well hidden-xs hidden-sm">
            <h4>Legend</h4>
            <span class="label label-success">Saved</span>
            <span class="label label-info">Modified</span>
            <span class="label label-warning">Saved Public</span>
            <span class="label label-danger">Unsaved deletion</span>
        </div>
	</div>
	<div class="col-md-10" id='calendar'></div>
</div>
