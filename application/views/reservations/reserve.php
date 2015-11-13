<script src="http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<link rel="stylesheet" type="text/css"  href="<?php echo asset_url() . "css/jquery.qtip.min.css"; ?>" />
<script src="<?php echo asset_url() . "js/jquery.qtip.min.js"; ?>"  ></script>

<div class="container">
	<article>
		<legend>Search by form</legend>
		<form class="form-horizontal">
		<fieldset>
		<!-- Select Basic -->
		<div class="form-group">
		  <label class="col-md-4 control-label" for="selectday">Day</label>
		  <div class="col-md-4">
			<div class="input-group">
			<input type="text" class="form-control" placeholder="Select...">
			<span class="input-group-btn">
				<button class="btn btn-default glyphicon glyphicon-calendar" type="button"></button>
			</span>
			</div><!-- /input-group -->
		</div>
		</div>
		<div class="form-group">
		  <label class="col-md-4 control-label" for="selectmachine">Machine</label>
		  <div class="col-md-4">
			<select id="selectmachine" name="selectmachine" class="form-control">
			<?php foreach ($machines as $machine): ?>
			<option value="<?php echo $machine->MachineID?>"><?php echo $machine->MachineID . " " . $machine->MachineName
			. " " . $machine->Manufacturer . " " . $machine->Model ?></option>
			<?php endforeach; ?>
			</select>
		  </div>
		</div>

		<div class="form-group">
		  <label class="col-md-4 control-label" for="selectlenght">Reservation lenght</label>
		  <div class="col-md-4">
			<select id="selectlenght" name="selectlenght" class="form-control">
			  <option value="1">Option one</option>
			  <option value="2">Option two</option>
			</select>
		  </div>
		</div>

		<div class="form-group">
		  <label class="col-md-4 control-label" for="searchbutton"></label>
		  <div class="col-md-4">
			<button id="searchbutton" name="searchbutton" class="btn btn-primary">Search</button>
		  </div>
		</div>
		
		<div class="form-group">
		  <label class="col-md-4 control-label" for="results"></label>
		  <div class="col-md-4">
			<div class="well" id="results" name="results">results</div>
		  </div>
		</div>

		</fieldset>
		</form>
		
	</article>
	<article>
		<legend>Search by calendar</legend>
		<div id="calendar"></div>
	</article>
	
	
</div>
<script>

	$(function() { // document ready
		$("#datepicker").datepicker();

		$("#dp_btn").click( function() {
			$( "#datepicker" ).datepicker( "show" );
		});

		$('#calendar').fullCalendar({
			now: '2015-10-26',
			editable: false, // enable draggable events
			allDaySlot: false,
			firstDay: 1,
			aspectRatio: 1.8,
			scrollTime: '08:00', // undo default 6am scrollTime
			header: {
				left: 'today prev,next',
				center: 'title',
				right: 'timelineDay, agendaWeek, month'
			},
			resourceLabelText: 'Machines',
			defaultView: 'timelineDay',
			resources: { // you can also specify a plain string like 'json/resources.json'
				url: '<?php echo base_url('reservations/reserve_get_machines')?>',
				error: function() {
					$('#script-warning').show();
				}
			},

			events: { // you can also specify a plain string like 'json/events.json'
				url: '<?php echo base_url('reservations/reserve_get_reserved_slots')?>',
				error: function() {
					$('#script-warning').show();
				}
			},
			eventAfterRender : function( e, element, view ) { 
// 				console.log(element);
				console.log(e);
// 				console.log(view);
				var sTime = moment(e.start._i).format("HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
				var eTime = moment(e.end._i).format("HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
				var rModal = '<div>'+
					'<h4>'+ e.id +'Available time: '+ sTime +' - '+ eTime +'</h4>'+
				'<p>Reserve time between:</p>' +
				'<div class="input-group bootstrap-timepicker timepicker">'+
		           ' <input type="text" class=" input-small" data-provide="timepicker" data-minute-step="30"> -' +
		        '</div>' +	
				'<div class="input-group bootstrap-timepicker timepicker">'+
		           ' <input type="text" class=" input-small" data-provide="timepicker" data-minute-step="30">' +
		        '</div>' +	
		        '<br>' +
				'<div class="btn-group" role="group" aria-label="...">' +
					'<button type="button" class="btn btn-primary">Reserve</button>' +
					'<button type="button" class="btn btn-default">Cancel</button>' +
				'</div>' +
			'</div>';
				var t = e.id + "<br>" + sTime  + "<br>" + eTime;
				$(element).qtip({ // Grab some elements to apply the tooltip to
					show: { 
						effect: function() { $(this).slideDown(); },
						solo: true,
						event: 'click'
			        },
			        hide: { 
			        	event: false
			        },
				    content: {
					    title: "Reservation",
				        text: rModal,
				        button: true
				    },
				    style: {
				        classes: 'qtip-bootstrap'
				    }
				});
			}
			
		});
		
	});

</script>