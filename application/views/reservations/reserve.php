<link rel="stylesheet" type="text/css"  href="<?php echo asset_url() . "css/jquery.qtip.min.css"; ?>" />
<script src="<?php echo asset_url() . "js/jquery.qtip.min.js"; ?>"  ></script>
<script>

	function reserve() {
		var start = moment($("#startInput").val(), "DD.MM.YYYY HH:mm");
		var end = moment($("#endInput").val(), "DD.MM.YYYY HH:mm");
		// Send form to controller 
		var post_data = {
			'mac_id': $("#reservation_form input[name='mac_id']").val(),
			'syear': start.format('YYYY'),
			'smonth': start.format('MM'),
			'sday': start.format('DD'),
			'shour': start.format('HH'),
			'smin': start.format('mm'),
			'eyear': end.format('YYYY'),
			'emonth': end.format('MM'),
			'eday': end.format('DD'),
			'ehour': end.format('HH'),
			'emin': end.format('mm'),
		};

		$.ajax({
			type: "POST",
			url: "reserve_time",
			data: post_data,
			success: function(data) {
				if (data.length > 0) {
					//It would be nice to inform user
				}
			}
		}); 
	}

	$(function() { // document ready
		$("#datepicker").datepicker();
        
		$("#dp_btn").click( function() {
			$( "#datepicker" ).datepicker( "show" );
		});

		$('#calendar').fullCalendar({
			schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
			now: '2015-10-26',
			editable: false, // enable draggable events
			allDaySlot: false,
			firstDay: 1,
            timeFormat: 'HH:mm',
            slotLabelFormat: 'HH:mm',
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

            eventSources: [
            // your event source
                {
                    url: 'reserve_get_reserved_slots',
                    color: "#f0ad4e"
                },
                {
                    url: 'reserve_get_free_slots', 
                    color: "#5cb85c"
                }
            ],
			eventAfterRender : function( e, element, view ) { 
// 				console.log(element);
// 				console.log(e);
				if (e.reserved == 1) return; 
// 				console.log(view);
				var sTime = moment(e.start._i).format("HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
				var eTime = moment(e.end._i).format("HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
				var url = '<?php echo base_url(); ?>';	
				var rModal="";
				rModal += "			<form id=reservation_form class=\"\" method=\"post\">";
				rModal += "			<input type=\"hidden\" name=\"mac_id\" value=\"" + e.resourceId + "\" />";
				rModal += "				<div class=\"row\">";
				rModal += "			        <div class=\"form-group col-md-12\">";
				rModal += "			        	<label>From (" + moment(e.start._i).format("DD.MM.YYYY, HH:mm") + "):<\/label>";
				rModal += "			            <div class=\"input-group date text-center\">";
				rModal += "			                <input id=\"startInput\" type=\"text\" class=\"form-control\" \/>";
				rModal += "			                <a id=\"startExp\" class=\"input-group-addon\">";
				rModal += "			                    <span class=\"glyphicon glyphicon-calendar\"><\/span>";
				rModal += "			                <\/a>";		
				rModal += "			            <\/div>";
				rModal += "						<div class=\"row\">";
				rModal += "			            	<div style=\"overflow:hidden;\" name='rStartTime' class=\"collapse\" id=\"startpicker\"></div>";
				rModal += "			            <\/div>";
				rModal += "			        <\/div>";
				rModal += "			    <\/div>";
				rModal += "			    <div class=\"row text-center col-md-12\">";
				rModal += "		    		-";
				rModal += "		    	<\/div>";
				rModal += "			    <div class=\"row\">";
				rModal += "			        <div class=\"form-group col-md-12\">";
				rModal += "			        	<label>To (" + moment(e.end._i).format("DD.MM.YYYY, HH:mm") + "):<\/label>";	
				rModal += "			            <div class=\"input-group date text-center\">";
				rModal += "			                <input id=\"endInput\" type=\"text\" class=\"form-control\" \/>";
				rModal += "			                <a id=\"endExp\" class=\"input-group-addon\">";
				rModal += "			                    <span class=\"glyphicon glyphicon-calendar\"><\/span>";
				rModal += "			                <\/a>";
				rModal += "			            <\/div>";
				rModal += "						<div class=\"row\">";
				rModal += "			            	<div style=\"overflow:hidden;\" name='rEndTime' class=\"collapse\" id=\"endpicker\"></div>";
				rModal += "			            <\/div>";
				rModal += "			        <\/div>";
				rModal += "			    <\/div>";
				rModal += "			    <div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
				rModal += "			    	<a id=\"reserveButton\" class=\"btn btn-primary\" >Reserve</a>";
				rModal += "			    <\/div>";
				rModal += "			</form>";



				var rModale = '<div>'+
				//	'<h4>'+ e.resourceId +'Available time: '+ sTime +' - '+ eTime +'</h4>'+
				'<h4>Available time: '+ sTime +' - '+ eTime +'</h4>'+
				'<h4>Available quota: ' + <?php echo $quota ?> + '</h4>'+
				'<p>Reserve time between (HH:MM):</p>' +
				'<form class="" method="post" action="' + url + 'reservations/reserve_time">' +
					'<div>' + 
						'<input type="hidden" name="mac_id" value="' + e.resourceId + '" />' +
						//'<input type="hidden" name="sDate" value="' + moment(e.start._i).format("YYYY-MM-DD") + '" />' +
						//'<input type="hidden" name="eDate" value="' + moment(e.start._i).format("YYYY-MM-DD") + '" />' +
						//'<input type="text" class="form-control" name="rStartTime" id=\'' + e.resourceId + '_start\' /> -' +
						//'<input type="text" class="form-control" name="rEndTime" id=\'' + e.resourceId + '_end\' />' +
					//	'<h4>Left quota after reservation: ' + <?php //echo $quota ?> + '</h4>' +
					'</div>' +
					'<br>' +
					'<div class="btn-group" role="group" aria-label="...">' +
						'<a type="submit" id="reserveButton" action="" class="btn btn-primary" >Reserve</a>' +
					//	'<button type="button" class="btn btn-default">Cancel</button>' +
					'</div>'
				'</form>';

				$(element).qtip({ // Grab some elements to apply the tooltip to
					show: { 
						effect: function() { $(this).slideDown(); },
						solo: true,
						event: 'click'
			        },
			        hide: { 
			        	event: false
			        },
			        position : {
			        	at: 'center center'
				    },
				    content: {
					    title: "Reservation",
				        text: rModal,
				        button: true
				    },
				    style: {
				        classes: 'qtip-bootstrap qtip_width'
				        //width: 'auto',
					    //height: 'auto'
				        
				    },
				    events: {
				    	render: function (event, api) {
				    		
				    	},
				    	visible: function (event, api) {
							// get input texts in the qtip.

							$('#startpicker').datetimepicker({
								locale: 'en-gb',
								format: 'DD/MM/YYYY HH:mm',
				        		stepping : 30,
						    	widgetPositioning: {
									horizontal: "left",
									vertical: "top"
							    },
			                    inline: true,
                				sideBySide: false
					        });

					        $('#endpicker').datetimepicker({
					        	locale: 'en-gb',
					        	format: 'DD/MM/YYYY HH:mm',
				        		stepping : 30,
						    	widgetPositioning: {
									horizontal: "left",
									vertical: "top"
							    },
							    inline: true,
                				sideBySide: false,
					            useCurrent: false //Important! See issue #1075
					        });

					        $("#startpicker").on("dp.change", function (e) {
					            $('#endpicker').data("DateTimePicker").minDate(e.date);
					        });
					        $("#endpicker").on("dp.change", function (e) {
					            $('#startpicker').data("DateTimePicker").maxDate(e.date);
					        });

					        $('#startExp').click(function(){ 
					        	$('#startpicker').collapse('toggle'); 
					        	return false; 
					        });

					        $('#endExp').click(function(){ 
					        	$('#endpicker').collapse('toggle'); 
					        	return false; 
					        });

					        $('#reserveButton').click(function(){ 
					        	reserve();
					        });

					        $('#startpicker').on('dp.change', function (e) {
					            var mDate = new moment(e.date);
					            $("#startInput").attr('value',mDate.format('DD.MM.YYYY HH:mm'));
					        });

					        $('#endpicker').on('dp.change', function (e) {
					            var mDate = new moment(e.date);
					            $("#endInput").attr('value',mDate.format('DD.MM.YYYY HH:mm'));
					        });

					        /*$('#startInput').change(function(){
							    $('#z').datetimepicker('setDate', $(this).val());
							});*/
					    	
				    	}
					}  
				});
			} //eventAfterRender
			
		});//fullcalendar
	});//$function

</script>
<style>
    .bootstrap-datetimepicker-widget{ z-index:100151 !important; position: inherit; }
    .collapse {
        position: inherit;
    }
</style>
<div class="container">
	<h4>Available quota: <?php echo $quota;?></h4>
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