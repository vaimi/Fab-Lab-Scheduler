<!-- TODO -->
<!-- $reservation_deadline contains deadline e.g 16:00 in format HH:mm. This should limit the reservation-->
<!-- $is_admin (bool) determines if $reservation_deadline limitation is needed. -->
<link rel="stylesheet" type="text/css"  href="<?php echo asset_url() . "css/jquery.qtip.min.css"; ?>" />
<script src="<?php echo asset_url() . "js/jquery.qtip.min.js"; ?>"  ></script>
<script type="text/javascript" src="<?=asset_url()?>js/bootstrap-notify.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?=asset_url()?>css/animate.css"/>
<script type="text/javascript" src="<?=asset_url()?>js/bootstrap-select.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?=asset_url()?>css/bootstrap-select.min.css"/>

<script>

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

	function disableForm(disable_bool, nightslot) {
		if (disable_bool) {
			if (nightslot == 0) {
				$(".startInput").attr('disabled', true);
				$(".endInput").attr('disabled', true);
			}
			$(".reserveButton").addClass('disabled');
		} else {
			if (nightslot == 0) {
				$(".startInput").removeAttr('disabled');
				$(".endInput").removeAttr('disabled');
			}
			$('.reserveButton').removeClass('disabled');
		}
	}

	function searchSlot() {
		var day = $("#searchInput").val();
		if (day != "") {
			day = moment(day, <?=$this->lang->line('fablab_moment_day_format');?>);
			var day_string = day.format("YYYY-MM-DD");
		} else {
			var day_string = "";
		}
		var post_data = {
			'mid': $("#selectMachine").val(),
			'day': day_string,
			'length': $("#selectLength").val(),
			'csrf_test_name': csrf_token
		};
		$("#results").html("<div class=\"loader\"></div>");
		$("#searchButton").addClass('disabled');
		$.ajax({
			type: "POST",
			url: "reserve_search_free_slots",
			data: post_data,
			success: function(data) {
				$("#searchButton").removeClass('disabled');
				if (data.length > 0) {
					var message = $.parseJSON(data);
					if (message.length > 0) {
						var resultText = "";
						resultText += "<div class=\"list-group\">";
						for (var result in message) {
							var start = moment(message[result].start, "<?=$this->lang->line('fablab_moment_full_format');?>")
							var end = moment(message[result].end, "<?=$this->lang->line('fablab_moment_full_format');?>")
							if (moment(start).isSame(end, 'day')){
								var time = start.format("<?=$this->lang->line('fablab_moment_full_format');?>") + " - " + end.format("<?=$this->lang->line('fablab_moment_time_format');?>");
							} else {
								var time = start.format("<?=$this->lang->line('fablab_moment_full_format');?>") + " - " + end.format("<?=$this->lang->line('fablab_moment_full_format');?>");
							}
							resultText += "<a href=\"javascript:void(0)\" class=\"list-group-item search_result\" data-next=\"" + message[result].next_start +  "\" data-nightslot=\"" + message[result].unsupervised + "\" data-machine=" + message[result].mid + " data-start=\"" + message[result].start + "\" data-end=\"" + message[result].end + "\">" + time + " : " + message[result].title + "</a>";
						}
						$("#results").html(resultText);
						resultText += "</div>";

						$(".search_result").each(function(index) {

							var machine = $(this).data("machine");
	        				var eStart = $(this).data("start");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
							var eEnd = $(this).data("end");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
							var nightslot = $(this).data("nightslot");
							var next_start = $(this).data("next");
							if (nightslot == 1) {
								$(this).click(function(){ 
					        		makeQtip_nightslot($(this), machine, eStart, eEnd, next_start);
					        	});
							}
							else {
								$(this).click(function(){ 
				        			makeQtip($(this), machine, eStart, eEnd);
				        		});
							}
						});
					} else {
						$("#results").html(<?=$this->lang->line('fablab_reservations_reserve_search_none');?>);
					}

				}
			}
		}); 
	}

	function makeQtip(elementId, machine, e_Start, e_End) {
		var sModal="";
		sModal += "			<form class=\"reservation_form\" method=\"post\">";
		sModal += "			<input type=\"hidden\" name=\"mac_id\" data-nightslot=\"0\" value=\"" + machine + "\" />";
		sModal += "				<div class=\"row\">";
		sModal += "			        <div class=\"form-group col-md-12\">";
		sModal += "			        	<label><?=$this->lang->line('fablab_reservations_reserve_qtip_from');?>(" + e_Start + "):<\/label>";
		sModal += "			            <div class=\"input-group date text-center\">";
		sModal += "			                <input onkeyup=\"lengthCalculation(false);\" type=\"text\" class=\"form-control startInput\" value=\"" + e_Start + "\" \/>";
		sModal += "			                <a class=\"input-group-addon startExp\">";
		sModal += "			                    <span class=\"glyphicon glyphicon-calendar\"><\/span>";
		sModal += "			                <\/a>";		
		sModal += "			            <\/div>";
		sModal += "						<div class=\"row\">";
		sModal += "			            	<div style=\"overflow:hidden;\" name='rStartTime' class=\"collapse startpicker\"></div>";
		sModal += "			            <\/div>";
		sModal += "			        <\/div>";
		sModal += "			    <\/div>";
		sModal += "			    <div class=\"row text-center col-md-12\">";
		sModal += "		    	<\/div>";
		sModal += "			    <div class=\"row\">";
		sModal += "			        <div class=\"form-group col-md-12\">";
		sModal += "			        	<label><?=$this->lang->line('fablab_reservations_reserve_qtip_to');?>(" + e_End + "):<\/label>";	
		sModal += "			            <div class=\"input-group date text-center\">";
		sModal += "			                <input onkeyup=\"lengthCalculation(false);\" type=\"text\" class=\"form-control endInput\" value=\"" + e_End + "\" \/>";
		sModal += "			                <a class=\"input-group-addon endExp\">";
		sModal += "			                    <span class=\"glyphicon glyphicon-calendar\"><\/span>";
		sModal += "			                <\/a>";
		sModal += "			            <\/div>";
		sModal += "						<div class=\"row\">";
		sModal += "			            	<div style=\"overflow:hidden;\" name='rEndTime' class=\"collapse endpicker\"></div>";
		sModal += "			            <\/div>";
		sModal += "			        <\/div>";
		sModal += "			    <\/div>";
		sModal += "				<p><?=$this->lang->line('fablab_reservations_reserve_qtip_length');?><span class=\"reservationLength\"></span></p>";
		sModal += "				<p><?=$this->lang->line('fablab_reservations_reserve_qtip_tokens');?><span class=\"quotaReserve\"></span></p>";    
		sModal += "				<br>";
		sModal += "			    <div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
		sModal += "			    	<a class=\"btn btn-primary reserveButton\" ><?=$this->lang->line('fablab_reservations_reserve_qtip_reserve');?></a>";
		sModal += "			    <\/div>";
		sModal += "			</form>";
		$(elementId).qtip({ // Grab some elements to apply the tooltip to
			show: { 
				effect: function() { $(this).slideDown(); },
				solo: true,
            	ready: true
	        },
	        hide: { 
	        	event: false
	        },
		    content: {
			    title: "<?=$this->lang->line('fablab_reservations_reserve_qtip_title');?>",
		        text: sModal,
		        button: true
		    },
		    style: {
		        classes: 'qtip-bootstrap qtip_width'
		        //width: 'auto',
			    //height: 'auto'
			},
		    position: {
				at: 'center center',
				my: 'left center',
				viewport: jQuery(window) // Keep the tooltip on-screen at all times
		    },
		    events: {
		    	hide: function (event, api) {
			        $(this).qtip('destroy');
		    	},
		    	show: function (event, api) {
					$.when(getQuota()).done(function() {
					    
					});
					lengthCalculation(false);

    				var eStart = moment(e_Start, "<?=$this->lang->line('fablab_moment_full_format');?>").format("YYYY/MM/DD, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
					var eEnd = moment(e_End, "<?=$this->lang->line('fablab_moment_full_format');?>").format("YYYY/MM/DD, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");

					$('.startpicker').datetimepicker({
						locale: 'en-gb',
						format: 'YYYY/MM/DD, HH:mm',
				    	widgetPositioning: {
							horizontal: "left",
							vertical: "top"
					    },
	                    inline: true,
        				sideBySide: false,
        				minDate: eStart,
        				maxDate: eEnd,
        				defaultDate: eStart
			        });

			        $('.endpicker').datetimepicker({
			        	locale: 'en-gb',
			        	format: 'YYYY/MM/DD, HH:mm',
				    	widgetPositioning: {
							horizontal: "left",
							vertical: "top"
					    },
					    inline: true,
        				sideBySide: false,
			            useCurrent: false, //Important! See issue #1075
        				minDate: eStart,
        				maxDate: eEnd,
        				defaultDate: eEnd
			        });

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

			        $('.reserveButton').unbind("click").click(function(){ 
			        	reserve(0);
			        });

			        $('.startpicker').on('dp.change', function (e) {
			            var mDate = new moment(e.date);
			            $(".startInput").val(mDate.format('<?=$this->lang->line('fablab_moment_full_format');?>'));
			            lengthCalculation(false);
			        });

			        $('.endpicker').on('dp.change', function (e) {
			            var mDate = new moment(e.date);
			            $(".endInput").val(mDate.format('<?=$this->lang->line('fablab_moment_full_format');?>'));
			            lengthCalculation(false);
			        });
		    	}
			}  
		});
	}
	
	function makeQtip_admin(elementId, machine, e_Start, e_End, firstname, surname, email, reservation_id) {
		var sModal="";
		sModal += "<p><?=$this->lang->line('fablab_fullcalendar_admin_info_start');?>" + e_Start + "</p>";
		sModal += "<p><?=$this->lang->line('fablab_fullcalendar_admin_info_end');?>" + e_End + "</p>";
		sModal += "<p><?=$this->lang->line('fablab_fullcalendar_admin_info_first_name');?>" + firstname + "</p>";
		sModal += "<p><?=$this->lang->line('fablab_fullcalendar_admin_info_last_name');?>" + surname + "</p>";
		sModal += "<p><?=$this->lang->line('fablab_fullcalendar_admin_info_email');?>" + email + "</p>";
		sModal += "";
		
		$(elementId).qtip({ // Grab some elements to apply the tooltip to
			show: { 
				effect: function() { $(this).slideDown(); },
				solo: true,
            	ready: true
	        },
	        hide: { 
	        	event: false
	        },
		    content: {
			    title: "<?=$this->lang->line('fablab_fullcalendar_admin_info_title');?>",
		        text: sModal,
		        button: true
		    },
		    style: {
		        classes: 'qtip-bootstrap qtip_width'
			},
		    position: {
				at: 'center center',
				my: 'left center',
				viewport: jQuery(window) // Keep the tooltip on-screen at all times
		    },
		    events: {
		    	hide: function (event, api) {
			        $(this).qtip('destroy');
		    	}
			}  
		});
	}

	function makeQtip_nightslot(elementId, machine, e_Start, e_End, e_Potential) {
		var sModal="";
		sModal += "			<form class=\"reservation_form\" method=\"post\">";
		sModal += "			<input type=\"hidden\" name=\"mac_id\" data-nightslot=\"1\" value=\"" + machine + "\" />";
		sModal += "				<div class=\"row\">";
		sModal += "			        <div class=\"form-group col-md-12\">";
		sModal += "			        	<label><?=$this->lang->line('fablab_reservations_reserve_night_qtip_from');?><\/label>";
		sModal += "			            <div class=\"input-group date text-center\">";
		sModal += "			                <input disabled type=\"text\" class=\"form-control startInput\" value=\"" + e_Start + "\" \/>";	
		sModal += "			            <\/div>";
		sModal += "			        <\/div>";
		sModal += "			    <\/div>";
		sModal += "			    <div class=\"row text-center col-md-12\">";
		sModal += "		    	<\/div>";
		sModal += "			    <div class=\"row\">";
		sModal += "			        <div class=\"form-group col-md-12\">";
		sModal += "			        	<label><?=$this->lang->line('fablab_reservations_reserve_night_qtip_from');?><\/label>";	
		sModal += "			            <div class=\"input-group date text-center\">";
		sModal += "			                <input disabled type=\"text\" class=\"form-control endInput\" value=\"" + e_End + "\" \/>";
		sModal += "			            <\/div>";
		sModal += "			        <\/div>";
		sModal += "			    <\/div>";
		sModal += "			    <div class=\"row\">";
		sModal += "			        <div class=\"form-group col-md-12\">";
		sModal += "			        	<label><?=$this->lang->line('fablab_reservations_reserve_night_qtip_next');?><\/label>";	
		sModal += "			            <div class=\"input-group date text-center\">";
		sModal += "			                <input disabled type=\"text\" class=\"form-control nextInput\" value=\"" + e_Potential + "\" \/>";
		sModal += "			            <\/div>";
		sModal += "			        <\/div>";
		sModal += "			    <\/div>";
		sModal += "				<p><?=$this->lang->line('fablab_reservations_reserve_night_qtip_length');?><span class=\"reservationLength\"></span></p>";
		sModal += "				<p><?=$this->lang->line('fablab_reservations_reserve_night_qtip_tokens');?><span class=\"quotaReserve\"></span></p>";
		sModal += "				<br>";
		sModal += "			    <div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
		sModal += "			    	<a class=\"btn btn-primary reserveButton\" ><?=$this->lang->line('fablab_reservations_reserve_night_qtip_reserve');?></a>";
		sModal += "			    <\/div>";
		sModal += "			</form>";
		
		$(elementId).qtip({ // Grab some elements to apply the tooltip to
			show: { 
				effect: function() { $(this).slideDown(); },
				solo: true,
            	ready: true
	        },
	        hide: { 
	        	event: false
	        },
		    content: {
			    title: "<?=$this->lang->line('fablab_reservations_reserve_night_qtip_title');?>",
		        text: sModal,
		        button: true
		    },
		    style: {
		        classes: 'qtip-bootstrap qtip_width'
			},
		    position: {
				at: 'center center',
				my: 'left center',
				viewport: jQuery(window) // Keep the tooltip on-screen at all times
		    },
		    events: {
		    	hide: function (event, api) {
			        $(this).qtip('destroy');
		    	},
		    	show: function (event, api) {
					$.when(getQuota()).done(function() {
					    
					});
					lengthCalculation(true);

					$('.reserveButton').unbind("click").click(function(){ 
			        	reserve(1);
			        });
				}
			}  
		});
	}

	
	function reserve(nightslot) {
		var start = moment($(".startInput").val(), "<?=$this->lang->line('fablab_moment_full_format');?>");
		var end = moment($(".endInput").val(), "<?=$this->lang->line('fablab_moment_full_format');?>");
		// Send form to controller 
		var post_data = {
			'nightslot': nightslot,
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
			'csrf_test_name': csrf_token
		};

		disableForm(true, nightslot);

		$.ajax({
			type: "POST",
			url: "reserve_time",
			data: post_data,
			success: function(data) {
				disableForm(false, $(".reservation_form").data("nightslot"));
				if (data.length > 0) {
				var message = $.parseJSON(data);
					if (message.success == 1) {
						alerter("success", "<?=$this->lang->line('fablab_reservations_reserve_succesful');?>");
						$(".qtip").qtip('hide');
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
			data: {'csrf_test_name': csrf_token},
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


    function lengthCalculation (nightslot) {
		if (nightslot == true) {
			end = moment($(".nextInput").val(), "<?=$this->lang->line('fablab_moment_full_format');?>");
		}
		else {
			end = moment($(".endInput").val(), "<?=$this->lang->line('fablab_moment_full_format');?>");
		}
    	start = moment($(".startInput").val(), "<?=$this->lang->line('fablab_moment_full_format');?>");
    	var ms = end.diff(start);
		var d = moment.duration(ms);
		var s = Math.floor(d.asHours()) + moment.utc(ms).format(":mm");
		$(".reservationLength").text(s).trigger('change');;
    }

	$(function() { // document ready
		$('.selectpicker').selectpicker();
		$('#searchPicker').datetimepicker({
			locale: 'en-gb',
			format: '<?=$this->lang->line('fablab_moment_day_format');?>',
			minDate: moment().format('MM/DD/YYYY'),
        });
        $('#searchPicker').data("DateTimePicker").clear()

        $('#searchButton').unbind("click").click(function(){ 
        	searchSlot();
        });

		getQuota();

		$('#calendar').fullCalendar({
			schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
			editable: false, // enable draggable events
			allDaySlot: false,
			firstDay: <?=$this->lang->line('fablab_fullcalendar_firstday');?>,
            timeFormat: <?=$this->lang->line('fablab_fullcalendar_timeFormat');?>,
            slotLabelFormat: <?=$this->lang->line('fablab_fullcalendar_slotLabelFormat');?>,
			aspectRatio: 2.5,
			scrollTime: '08:00', // undo default 6am scrollTime
			header: {
				left: 'today prev,next',
				center: 'title',
				right: 'timelineDay, timelineSevenDays, month'
			},
			views: {
        			timelineSevenDays: {
		            type: 'timeline',
		            duration: { days: 7 },
		            slotDuration: '02:00',
		            slotLabelFormat: [ <?=$this->lang->line('fablab_fullcalendar_slotLabelFormat');?>]
		        }
		    },
			resourceLabelText: 'Machines',
			defaultView: 'timelineDay',
			resources: { // you can also specify a plain string like 'json/resources.json'
				url: '<?php echo base_url('reservations/reserve_get_machines')?>',
				error: function() {
					$('#script-warning').show();
				}
			},
	        loading: function( isLoading, view ) {
	            if(isLoading) {
	                 $("#loader").show()
	            } else {
	                $("#loader").hide()
	            }
	        },
	        resourceGroupField: 'groupText',
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
				console.log(view);
// 				console.log(element);
 				//console.log(e);
				var machine = e.resourceId;
				var eStart = e.start.format("<?=$this->lang->line('fablab_moment_full_format');?>");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
				var eEnd = e.end.format("<?=$this->lang->line('fablab_moment_full_format');?>");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
				if (e.reserved == 1)
				{
					if(e.is_admin === true)
					{
						var firstname = e.first_name;
						var surname = e.surname;
						var email = e.email;
						$(element).click(function(){ 
					        makeQtip_admin($(element), machine, eStart, eEnd, firstname, surname, email, e.reservation_id);
					    });
					}
					else if (e.email == '<?php echo $this->session->userdata('email'); ?>')
					{
						var firstname = e.first_name;
						var surname = e.surname;
						var email = e.email;
						$(element).css({'background-color':'#0000ff'});
						$(element).click(function(){ 
							//$(element).color = '';
					        makeQtip_admin($(element), machine, eStart, eEnd, firstname, surname, email, e.reservation_id);
					        
					    });
					}
				}
				if (e.nightslot == 1 && e.reserved != 1)
				{
					var eNext = moment(e.next_start).format("<?=$this->lang->line('fablab_moment_full_format');?>");
					$(element).click(function(){ 
				        makeQtip_nightslot($(element), machine, eStart, eEnd, eNext);
				    });
				}
				else if (e.reserved != 1)
				{
					$(element).click(function(){ 
				        makeQtip($(element), machine, eStart, eEnd);
				    });
				}
			} //eventAfterRender
			
		});//fullcalendar
	});//$function

</script>
<style>
    .collapse {
        position: inherit;
    }
    .fc-timeline-event {
    	cursor: pointer;
    }
</style>
<div class="container">
	<h4><?=$this->lang->line('fablab_reservations_reserve_tokens');?><span id="quotaMain"><?php echo $quota; ?></span>.</h4>
	<article>
		<legend><?=$this->lang->line('fablab_reservations_reserve_search_title');?></legend>
		<form class="form-horizontal">
		<fieldset>
		<div class="form-group required">
		  <label class="col-md-4 control-label" for="selectmachine"><?=$this->lang->line('fablab_reservations_reserve_search_machine');?></label>
		  <div class="col-md-4">
			<select id="selectMachine" class="form-control selectpicker">
			<?php foreach ($machines as $machine): ?>
			<option value="<?php echo $machine->MachineID?>"><?php echo $machine->MachineID . " " . $machine->Manufacturer . " " . $machine->Model ?></option>
			<?php endforeach; ?>
			</select>
		  </div>
		</div>
		<div class="form-group">
		  <label class="col-md-4 control-label" for="searchInput"><?=$this->lang->line('fablab_reservations_reserve_search_day');?></label>
		  <div class="col-md-4">
            <div class='input-group date' id='searchPicker'>
                <input id="searchInput" type='text' placeholder="Select day" class="form-control">
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
		  </div>
		</div>
		<div class="form-group">
		  <label class="col-md-4 control-label" for="selectlenght"><?=$this->lang->line('fablab_reservations_reserve_search_length');?></label>
		  <div class="col-md-4">
			<select id="selectLength" class="form-control selectpicker">
			  <option value="0"><?=$this->lang->line('fablab_reservations_reserve_search_length_undefined');?></option>
			  <option value="1">1 <?=$this->lang->line('fablab_reservations_reserve_search_length_hour');?></option>
			  <?php
			  for($i=2;$i<11;$i++) {
			  	echo '<option value="' . $i . '">' . $i . $this->lang->line('fablab_reservations_reserve_search_length_hours') . '</option>';
			  }
			  ?>
			</select>
		  </div>
		</div>

		<div class="form-group">
		  <label class="col-md-4 control-label" for="searchbutton"></label>
		  <div class="col-md-4">
			<a id="searchButton" class="btn btn-primary">
				<span class="glyphicon glyphicon-search" aria-hidden="true"><?=$this->lang->line('fablab_reservations_reserve_search_button');?></span>
			</a>
		  </div>
		</div>
		
		<div class="form-group">
		  <label class="col-md-2 control-label" for="results"></label>
		  <div class="col-md-6">
			<div class="well" id="results"><?=$this->lang->line('fablab_reservations_reserve_search_default');?></div>
		  </div>
		</div>

		</fieldset>
		</form>
		
	</article>
	<article>
		<legend><?=$this->lang->line('fablab_reservations_reserve_calendar_title');?></legend>
		<div id="calendar" style="position:relative"><div id="loader" class="loader" style='position:absolute;display:none;margin:auto;left: 0;top: 0;right: 0;bottom: 0;'></div></div>
	</article>	
</div>