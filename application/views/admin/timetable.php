<div class="container">
	<div class="btn-toolbar">
		<button type="button" class="btn btn-success">
			<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
			<!-- Modal with two calendars -->
		</button>
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

	$(document).ready(function() {


		/* initialize the external events
		-----------------------------------------------------------------*/

		$('#external-events .fc-event').each(function() {

			// store data so the calendar knows to render an event upon drop
			$(this).data('event', {
				title: $.trim($(this).text()), // use the element's text as the event title
				stick: true // maintain when user navigates (see docs on the renderEvent method)
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
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			allDayDefault: false,
			defaultTimedEventDuration: '08:00:00',
			editable: true,
			droppable: true // this allows things to be dropped onto the calendar
		});


	});

</script>
	<div class="col-md-2">
		<h4>Supervisors</h4>
		<ul class="list-group" id='external-events'>
			<li class='fc-event list-group-item'>Mikko Väisänen</li>
			<li class='fc-event list-group-item'>Thang Luu</li>
			<li class='fc-event list-group-item'>Markus Särkiniemi</li>
		</ul>
	</div>
	<div class="col-md-10" id='calendar'></div>
</div>
