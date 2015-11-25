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
		<button type="button" class="btn btn-info">
			<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Reflect to machines...
			<!-- Modal with selectable days -->
		</button>
		<span class="btn-separator"></span>
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal"> 
			<span class="glyphicon glyphicon-plus" aria-hidden="true" ></span> Create a new supervisor
			<!-- Modal with create new supervisor information -->
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
    
    function saveData() {
        $.ajax({
            type: "POST",
            url: "timetable_save",
            success: function(data) {
                // return success
                if (data.length > 0) {
                    $('#calendar').fullCalendar('refetchEvents');
                    //alert("events fetched!");
                }
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
            "end": moment(event._end).format("YYYY-MM-DD HH:mm:ss")
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
                }
            }
        });
    }
    
    function restoreEvent(id) {
    	var event = $("#calendar").fullCalendar( 'clientEvents', id)[0];
        var post_data = {
            "id": event.id
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
                    event.title = "uid: " + event.assigned + " sid: " + event.id;
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
		var sDate = $("#startDate").datepicker( "getDate" );
		var eDate = $("#endDate").datepicker( "getDate" );
		var csDate = $("#copyStartDate").datepicker( "getDate" );
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
        sDate = moment($("#startDate").datepicker( "getDate" )).format("YYYY-MM-DD");
		eDate = moment($("#endDate").datepicker( "getDate" )).format("YYYY-MM-DD");
		csDate = moment($("#copyStartDate").datepicker( "getDate" )).format("YYYY-MM-DD");
    	var post_data = {
              "startDate" : sDate,
              "endDate" : eDate,
              "copyStartDate" : csDate
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
		var sDate = $("#remove_startDate").datepicker( "getDate" );
		var eDate = $("#remove_endDate").datepicker( "getDate" );
        if ( sDate === null || eDate === null) {
            alert("Dates cannot be empty.");
            return;
        }
        if ( sDate > eDate ) {
            alert("Start date must be earlier than end date");
            return;
        }
        sDate = moment($("#remove_startDate").datepicker( "getDate" )).format("YYYY-MM-DD");
		eDate = moment($("#remove_endDate").datepicker( "getDate" )).format("YYYY-MM-DD");
    	var post_data = {
              "startDate" : sDate,
              "endDate" : eDate
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
                    event.color = "#660000"; // Red color
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
            "end": $('#endpicker').data("DateTimePicker").date().format("YYYY-MM-DD HH:mm:ss")
        };
        $.ajax({
            type: "POST",
            url: "timetable_confirm_slot",
            data: post_data,
            success: function(data) {
                alert(data);
                var json = JSON.parse(data);
                // return success
                if (json.success) {
                    event.assigned = json.assigned;
                    event.group = json.group;
                    event.start = json.start;
                    event.end = json.end;
                    event.title = "uid: " + event.assigned + " sid: " + event.id;
                    event.color = json.color;
                    $('#calendar').fullCalendar('updateEvent', event);
                }
            }
        });
    }
	$(document).ready(function() {

		/* initialize the Datepickers
		-----------------------------------------------------------------*/
		$( ".modaldate" ).datepicker();

		/* initialize the external events
		-----------------------------------------------------------------*/

		$('#external-events .fc-event').each(function() {

			// store data so the calendar knows to render an event upon drop
			$(this).data('event', {
				title: $.trim($(this).text()), // use the element's text as the event title
                assigned: $.trim($(this).data( "assigned" )),
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
                    color: ttColors.saved
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
                    "start": moment(event.start).format("YYYY-MM-DD HH:mm:ss"),
                    "end": moment(event.end).format("YYYY-MM-DD HH:mm:ss")
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
            eventDrop: function(event, delta, revertFunc) {
                //move callback
                var post_data = {
                    "id": event.id,
                    "assigned": event.assigned,
                    "start": moment(event.start).format("YYYY-MM-DD HH:mm:ss"),
                    "end": moment(event.end).format("YYYY-MM-DD HH:mm:ss")
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
                    "assigned": event.assigned
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
	<!-- Create Supervisor Modal -->
	<div id="createModal" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Create a supervisor</h4>
	      </div>
	      <div class="modal-body">
	      	<form name="registration" method="post" action="<?php echo base_url();?>user/registration" onsubmit="return true;">
				<table>
					<tr>
						<td width="150px"><label for="username">First Name *</label></td>
						<td><input type="text" class="form-control" name="username" id="username"
							style="width: 200px;" value="" required="" autofocus="" placeholder="User name" /></td>
					</tr>
					<tr>
						<td><label for="password">Password *</label></td>
						<td><input type="password" class="form-control" name="password" id="password"
							style="width: 200px;" value="" required="" autofocus="" placeholder="Password" /></td>
					</tr>
					<tr>
						<td><label for="surname">Surname *</label></td>
						<td><input type="text" class="form-control" name="surname" id="surname"
							style="width: 200px;" value="" required="" autofocus="" placeholder="Surname" /></td>
					</tr>
		
					<tr>
						<td><label for="email">Email address *</label></td>
						<td><input type="email" class="form-control" name="email" id="email" style="width: 200px;"
							value="" required="" autofocus="" placeholder="Email address" /></td>
					</tr>
					<tr>
						<td><label for="phone_number">Phone number</label></td>
						<td><input type="text" class="form-control" name="phone_number" id="phone_number" style="width: 200px;"
							value="" required="" autofocus="" placeholder="Phone number" /></td>
					</tr>
					<tr>
						<td><label for="company">Company</label></td>
						<td><input type="text" class="form-control" name="company" id="company"
							style="width: 200px;" value="" autofocus="" placeholder="Company" /></td>
					</tr>
					<tr>
						<td><label for="address_street">Address</label></td>
						<td><input type="text" class="form-control" name="address_street" id="address_street"
							style="width: 200px;" value="" autofocus="" placeholder="Address" /></td>
					</tr>
					<tr>
						<td><label for="address_postal_code">Postal code</label></td>
						<td><input type="text" class="form-control" name="address_postal_code" id="address_postal_code"
							style="width: 200px;" value="" autofocus="" placeholder="Postal code" /></td>
					</tr>
					<tr>
						<td><label for="student_number">Student number</label></td>
						<td><input type="text" class="form-control" name="student_number" id="student_number"
							style="width: 200px;" value="" autofocus="" placeholder="Student number" /></td>
					</tr>
					<tr>
						<td><button type="submit" class='btn btn-primary' text="Register" >Register</button></td>
					</tr>
				</table>
			</form>
	      </div>
	      <div class="modal-footer">
		    <a type="button" class="btn btn-success" onclick="">Create</a>
	      	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	
	  </div>
	</div>
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
	        <p>Select start date: <input type="text" class="modaldate" id="startDate"></p>
	        <p>Select end date: <input type="text" class="modaldate" id="endDate"></p>
	        <p>Copy to date and forth: <input type="text" class="modaldate" id="copyStartDate"></p>
	        <p>Remember to save <b>before</b> copying!</p>
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
	        <p>Select start date: <input type="text" class="modaldate" id="remove_startDate"></p>
	        <p>Select end date: <input type="text" class="modaldate" id="remove_endDate"></p>
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
                <li class='fc-event list-group-item' id="<?php echo $row->id ?>" data-event='1' data-assigned='<?=$row->id?>'><?php echo $row->name; ?>(<?php echo$row->email ?>)</li>
            <?php }?>
            </ul>
        </div>
        <div class="row well hidden-xs hidden-sm">
            <h4>Legend</h4>
            <span class="label label-success">Saved</span>
            <span class="label label-info">Modified</span>
            <span class="label label-warning">Public</span>
            <span class="label label-public-saved">Saved public</span>
            <span class="label label-danger">Unsaved deletion</span>
        </div>
	</div>
	<div class="col-md-10" id='calendar'></div>
</div>
