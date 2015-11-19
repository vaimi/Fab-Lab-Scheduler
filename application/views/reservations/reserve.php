<link rel="stylesheet" type="text/css"  href="<?php echo asset_url() . "css/jquery.qtip.min.css"; ?>" />
<script src="<?php echo asset_url() . "js/jquery.qtip.min.js"; ?>"  ></script>
<script type="text/javascript" src="<?=asset_url()?>js/bootstrap-notify.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?=asset_url()?>css/animate.css"/>

<script>
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

	function reserve() {
		var start = moment($(".startInput").val(), "DD.MM.YYYY HH:mm");
		var end = moment($(".endInput").val(), "DD.MM.YYYY HH:mm");
		// Send form to controller 
		var post_data = {
			'mac_id': $(".reservation_form input[name='mac_id']").val(),
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
				var message = $.parseJSON(data);
					if (message.success == 1) {
						alerter("success", "Reservation successful");
						$('#calendar').fullCalendar('refetchEvents');
					} else {
						for(var error in message.errors) {
							alerter("warning", message.errors[error]); 
						}
					}
				}
			}
		}); 
	}

	var userQuota = 0;

	function getQuota() {
		$.ajax({
			type: "POST",
			url: "reserve_get_quota",
			success: function(data) {
				if (data.length > 0) {
					userQuota = data;
					$("#quotaMain").text(userQuota);
					$(".quotaReserve").text(userQuota);
				}
			}
		}); 
	}


    function costCalculation () {
    	end = moment($(".endInput").val(), "DD.MM.YYYY HH:mm");
    	start = moment($(".startInput").val(), "DD.MM.YYYY HH:mm");
    	if (end != null && start != null) {
        	var duration = moment.duration(end.diff(start));
        	var hours = duration.asHours();
        	$(".quotaCost").text(hours);
        	var left = userQuota - hours; 
        	if (left < 0) {
        		$(".quotaLeft").css("color", "red");
        		$(".quotaLeft").text(left);
        	} else {
        		$(".quotaLeft").removeAttr('style');
				$(".quotaLeft").text(left);
        	}
    	}
    }

	$(function() { // document ready
		getQuota();
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
				var url = '<?php echo base_url(); ?>';	
				var rModal="";
				rModal += "			<form class=\"reservation_form\" method=\"post\">";
				rModal += "			<input type=\"hidden\" name=\"mac_id\" value=\"" + e.resourceId + "\" />";
				rModal += "				<div class=\"row\">";
				rModal += "			        <div class=\"form-group col-md-12\">";
				rModal += "			        	<label>From (" + moment(e.start._i).format("DD.MM.YYYY, HH:mm") + "):<\/label>";
				rModal += "			            <div class=\"input-group date text-center\">";
				rModal += "			                <input onkeyup=\"costCalculation();\" type=\"text\" class=\"form-control startInput\" value=\"" + moment(e.start._i).format("DD.MM.YYYY, HH:mm") + "\" \/>";
				rModal += "			                <a class=\"input-group-addon startExp\">";
				rModal += "			                    <span class=\"glyphicon glyphicon-calendar\"><\/span>";
				rModal += "			                <\/a>";		
				rModal += "			            <\/div>";
				rModal += "						<div class=\"row\">";
				rModal += "			            	<div style=\"overflow:hidden;\" name='rStartTime' class=\"collapse startpicker\"></div>";
				rModal += "			            <\/div>";
				rModal += "			        <\/div>";
				rModal += "			    <\/div>";
				rModal += "			    <div class=\"row text-center col-md-12\">";
				rModal += "		    	<\/div>";
				rModal += "			    <div class=\"row\">";
				rModal += "			        <div class=\"form-group col-md-12\">";
				rModal += "			        	<label>To (" + moment(e.end._i).format("DD.MM.YYYY, HH:mm") + "):<\/label>";	
				rModal += "			            <div class=\"input-group date text-center\">";
				rModal += "			                <input onkeyup=\"costCalculation();\" type=\"text\" class=\"form-control endInput\" value=\"" + moment(e.end._i).format("DD.MM.YYYY, HH:mm") + "\" \/>";
				rModal += "			                <a class=\"input-group-addon endExp\">";
				rModal += "			                    <span class=\"glyphicon glyphicon-calendar\"><\/span>";
				rModal += "			                <\/a>";
				rModal += "			            <\/div>";
				rModal += "						<div class=\"row\">";
				rModal += "			            	<div style=\"overflow:hidden;\" name='rEndTime' class=\"collapse endpicker\"></div>";
				rModal += "			            <\/div>";
				rModal += "			        <\/div>";
				rModal += "			    <\/div>";
				rModal += "				<br>";
				rModal += "				<p>Quota: <span class=\"quotaReserve\"></span> hours</p>";
				rModal += "				<p>Cost: <span class=\"quotaCost\"></span> hours</p>";
				rModal += "				<p>After: <span class=\"quotaLeft\"></span> hours</p>";    
				rModal += "				<br>";
				rModal += "			    <div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
				rModal += "			    	<a class=\"btn btn-primary reserveButton\" >Reserve</a>";
				rModal += "			    <\/div>";
				rModal += "			</form>";

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
			        	target: 'mouse',
			        	adjust : {
				    		mouse: false
				    	}
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
							$.when(getQuota()).done(function() {
							    costCalculation()
							});

							$('.startpicker').datetimepicker({
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

					        $('.endpicker').datetimepicker({
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

	        				var eStart = moment(e.start._i).format("DD.MM.YYYY, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
							var eEnd = moment(e.end._i).format("DD.MM.YYYY, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
					        $('.startpicker').data("DateTimePicker").minDate(eStart);
					        $('.startpicker').data("DateTimePicker").maxDate(eEnd);
					        $('.endpicker').data("DateTimePicker").minDate(eStart);
					        $('.endpicker').data("DateTimePicker").maxDate(eEnd);
					   		$('.startpicker').data("DateTimePicker").date(eStart);
					        $('.endpicker').data("DateTimePicker").date(eEnd);

					        $(".startpicker").on("dp.change", function (e) {
					            $('.endpicker').data("DateTimePicker").minDate(e.date);

					        });
					        $(".endpicker").on("dp.change", function (e) {
					            $('.startpicker').data("DateTimePicker").maxDate(e.date);
					        });

					        $('.startExp').click(function(){ 
					        	$('.startpicker').collapse('toggle'); 
					        	return false; 
					        });

					        $('.endExp').click(function(){ 
					        	$('.endpicker').collapse('toggle'); 
					        	return false; 
					        });

					        $('.reserveButton').click(function(){ 
					        	reserve();
					        });

					        $('.startpicker').on('dp.change', function (e) {
					            var mDate = new moment(e.date);
					            $(".startInput").val(mDate.format('DD.MM.YYYY HH:mm'));
					            costCalculation();
					        });

					        $('.endpicker').on('dp.change', function (e) {
					            var mDate = new moment(e.date);
					            $(".endInput").val(mDate.format('DD.MM.YYYY HH:mm'));
					            costCalculation();
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
	<h4>Available quota: <span id="quotaMain"><?php echo $quota ?></span></h4>
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