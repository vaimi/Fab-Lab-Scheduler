<div class="container">
	<div class="btn-toolbar">
		<a id="save_button" type="button" class="btn btn-success">
			<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
		</a>
		<span class="btn-separator"></span>
		<button type="button" class="btn btn-primary">
			<span class="glyphicon glyphicon-copy" aria-hidden="true"></span> Copy schedule...
			<!-- Modal with two calendars -->
		</button>
		<button type="button" class="btn btn-danger">
			<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete schedules...
			<!-- Modal with selectable days -->
		</button>
		<button type="button" class="btn btn-info">
			<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Reflect to machines...
			<!-- Modal with selectable days -->
		</button>
	</div>
	<hr>
<script>
    function saveData() {

        $.ajax({
            type: "POST",
            url: "timetable_save",
            success: function(data) {
                // return success
                if (data.length > 0) {
                    $('#calendar').fullCalendar('refetchEvents');
                    alert("hello!");
                }
            }
        });
    }
    
    
    $('#save_button').click(function(){
		saveData();
	});
    
	$(document).ready(function() {


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
                    url: 'timetable_fetch_supervision_sessions', // use the `url` property
                    color: '#006600'  // an option!
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
            axisFormat: 'HH:mm',
			editable: true,
            firstDay: 1,
			droppable: true, // this allows things to be dropped onto the calendar
			schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
			eventClick: function(event, element) {
				event.title = "CLICKED!";
				//open modal/tooltip for deletion and manual data entry
				$('#calendar').fullCalendar('updateEvent', event);
			},
			editable: true,
            eventRender: function(event){
                var assigned = event.assigned;
            },
			eventResize: function(event, delta, revertFunc) {
                //Resize callback
                var post_data = {
                    "id": event.id,
                    "assigned": event.assigned,
                    "start": moment(event.start).format("DD-MM-YYYY HH:mm"),
                    "end": moment(event.end).format("DD-MM-YYYY HH:mm")
                }
				$.ajax({
                    type: "POST",
                    url: "timetable_modify_slot",
                    data: post_data,
                    success: function(data) {
                        // return success
                        if (data.length > 0) {
                            event.color = "#000066";
                            $('#calendar').fullCalendar('renderEvent', event);
                        }
                    }
                });
			},
            eventDrop: function(event, delta, revertFunc) {
                //move callback
                var post_data = {
                    "id": event.id,
                    "assigned": event.assigned,
                    "start": moment(event.start).format("DD-MM-YYYY HH:mm"),
                    "end": moment(event.end).format("DD-MM-YYYY HH:mm")
                }
				$.ajax({
                    type: "POST",
                    url: "timetable_modify_slot",
                    data: post_data,
                    success: function(data) {
                        // return success
                        if (data.length > 0) {
                            event.color = "#000066";
                            $('#calendar').fullCalendar('renderEvent', event);
                        }
                    }
                });
			},
            eventReceive: function(event){
                //external event drop callback
                console.log(event);
                var post_data = {
                    "start": moment(event.start).format("DD-MM-YYYY HH:mm"),
                    "end": moment(event.end).format("DD-MM-YYYY HH:mm"),
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
                            event.id = response.id;
                        }
                    }
                });
			}
		});
		


	});

</script>
	<div class="col-md-2">
		<h4>Supervisors</h4>
		<ul class="list-group" id='external-events'>
		<?php foreach ($admins as $row ) {?>
			<li class='fc-event list-group-item' id="<?php echo $row->id ?>" data-event='1' data-assigned='<?=$row->id?>'><?php echo $row->name; ?>(<?php echo$row->email ?>)</li>
		<?php }?>
		</ul>
	</div>
	<div class="col-md-10" id='calendar'></div>
</div>
