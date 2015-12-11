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

	function disableForm(disable_bool) {
		if (disable_bool) {
			$(".startInput").attr('disabled', true);
			$(".endInput").attr('disabled', true);
			$(".reserveButton").addClass('disabled');
		} else {
			$(".startInput").removeAttr('disabled');
			$(".endInput").removeAttr('disabled');
			$('.reserveButton').removeClass('disabled');
		}
	}

	function searchSlot() {
		var day = $("#searchInput").val();
		if (day != "") {
			day = moment(day, "DD.MM.YYYY");
			var day_string = day.format("YYYY-MM-DD");
		} else {
			var day_string = "";
		}
		var post_data = {
			'mid': $("#selectMachine").val(),
			'day': day_string,
			'length': $("#selectLength").val()
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
							resultText += "<a href=\"javascript:void(0)\" class=\"list-group-item search_result\" data-nightslot=\"" + message[result].unsupervised + "\" data-machine=" + message[result].mid + " data-start=\"" + message[result].start + "\" data-end=\"" + message[result].end + "\">" + message[result].start + " - " + message[result].end + " : " + message[result].title + "</a>";
						}
						$("#results").html(resultText);
						resultText += "</div>";

						$(".search_result").each(function(index) {

							var machine = $(this).data("machine");
	        				var eStart = $(this).data("start");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
							var eEnd = $(this).data("end");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
							var nightslot = $(this).data("nightslot");
							if (nightslot == 1) {
								$(this).click(function(){ 
					        		makeQtip_nightslot($(this), machine, eStart, eEnd);
					        	});
							}
							else {
								$(this).click(function(){ 
				        			makeQtip($(this), machine, eStart, eEnd);
				        		});
							}
						});
					} else {
						$("#results").html("No results.");
					}

				}
			}
		}); 
	}

	function makeQtip(elementId, machine, e_Start, e_End) {
		var sModal="";
		sModal += "			<form class=\"reservation_form\" method=\"post\">";
		sModal += "			<input type=\"hidden\" name=\"mac_id\" value=\"" + machine + "\" />";
		sModal += "				<div class=\"row\">";
		sModal += "			        <div class=\"form-group col-md-12\">";
		sModal += "			        	<label>From (" + e_Start + "):<\/label>";
		sModal += "			            <div class=\"input-group date text-center\">";
		sModal += "			                <input onkeyup=\"costCalculation();\" type=\"text\" class=\"form-control startInput\" value=\"" + e_Start + "\" \/>";
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
		sModal += "			        	<label>To (" + e_End + "):<\/label>";	
		sModal += "			            <div class=\"input-group date text-center\">";
		sModal += "			                <input onkeyup=\"costCalculation();\" type=\"text\" class=\"form-control endInput\" value=\"" + e_End + "\" \/>";
		sModal += "			                <a class=\"input-group-addon endExp\">";
		sModal += "			                    <span class=\"glyphicon glyphicon-calendar\"><\/span>";
		sModal += "			                <\/a>";
		sModal += "			            <\/div>";
		sModal += "						<div class=\"row\">";
		sModal += "			            	<div style=\"overflow:hidden;\" name='rEndTime' class=\"collapse endpicker\"></div>";
		sModal += "			            <\/div>";
		sModal += "			        <\/div>";
		sModal += "			    <\/div>";
		sModal += "				<br>";
		sModal += "				<p>Quota: <span class=\"quotaReserve\"></span> hours</p>";
		sModal += "				<p>Cost: <span class=\"quotaCost\"></span> hours</p>";
		sModal += "				<p>After: <span class=\"quotaLeft\"></span> hours</p>";    
		sModal += "				<br>";
		sModal += "			    <div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
		sModal += "			    	<a class=\"btn btn-primary reserveButton\" >Reserve</a>";
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
			    title: "Reservation",
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
					    costCalculation()
					});

    				var eStart = moment(e_Start, "DD.MM.YYYY, HH:mm").format("YYYY/MM/DD, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
					var eEnd = moment(e_End, "DD.MM.YYYY HH:mm").format("YYYY/MM/DD, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");

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
		    	}
			}  
		});
	}
	
	function makeQtip_admin(elementId, machine, e_Start, e_End, surname, email) {
		var sModal="";
		sModal += "<p>Start time: " + e_Start + "</p>";
		sModal += "<p>End time: " + e_End + "</p>";
		sModal += "<p>Surname (Should be fullname) : " + surname + "</p>";
		sModal += "<p>Email: " + email + "</p>";
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
			    title: "Reservation",
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

	function makeQtip_nightslot(elementId, machine, e_Start, e_End) {
		var sModal="";
		sModal += "<p>Start time: " + e_Start + "</p>";
		sModal += "<p>End time: " + e_End + "</p>";
		sModal += "<br>";
		sModal += "			    <div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
		sModal += "			    	<a class=\"btn btn-primary reserveButton\" >Reserve</a>";
		sModal += "			    <\/div>";
		
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
			    title: "Reservation",
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

		disableForm(true);

		$.ajax({
			type: "POST",
			url: "reserve_time",
			data: post_data,
			success: function(data) {
				disableForm(false);
				if (data.length > 0) {
				var message = $.parseJSON(data);
					if (message.success == 1) {
						alerter("success", "Reservation successful");
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
    	end = moment($(".endInput").val(), "DD.MM.YYYY, HH:mm");
    	start = moment($(".startInput").val(), "DD.MM.YYYY, HH:mm");
    	if (end != null && start != null) {
        	var duration = moment.duration(end.diff(start));
        	var hours = duration.asHours();
        	var left = userQuota - hours; 
        	if (left < 0) {
        		$(".quotaLeft").css("color", "red");
        		$(".quotaLeft").text(left.toFixed(2));
        	} else {
        		$(".quotaLeft").removeAttr('style');
				$(".quotaLeft").text(left.toFixed(2));
        	}
        	if (hours < 0) {
        		$(".quotaLeft").text("-");
        		$(".quotaCost").text("-");
        	} else {
        		$(".quotaCost").text(hours.toFixed(2));
        	}
    	}
    }

	$(function() { // document ready
		$('.selectpicker').selectpicker();
		$('#searchPicker').datetimepicker({
			locale: 'en-gb',
			format: 'DD.MM.YYYY',
			minDate: moment().format('MM/DD/YYYY'),
        });
        $('#searchPicker').data("DateTimePicker").clear()

        $('#searchButton').unbind("click").click(function(){ 
        	searchSlot();
        });

		getQuota();

		/*$("#datepicker").datepicker();
        
		$("#dp_btn").click( function() {
			$( "#datepicker" ).datepicker( "show" );
		});*/

		$('#calendar').fullCalendar({
			schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
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
	        loading: function( isLoading, view ) {
	            if(isLoading) {
	                 $("#loader").show()
	            } else {
	                $("#loader").hide()
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
				var machine = e.resourceId;
				var eStart = e.start.format("DD.MM.YYYY, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
				var eEnd = e.end.format("DD.MM.YYYY, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
				if (e.reserved == 1)
				{
					if(e.is_admin === true) 
					{
						var surname = e.surname;
						var email = e.email;
						$(element).click(function(){ 
					        makeQtip_admin($(element), machine, eStart, eEnd, surname, email);
					    });
					}
					return; 
				}
				if (e.unsupervised == 1)
				{
					$(element).click(function(){ 
				        makeQtip_nightslot($(element), machine, eStart, eEnd);
				    });
				}
				else
				{
					$(element).click(function(){ 
				        makeQtip($(element), machine, eStart, eEnd);
				    });
				}

				/*var rModal="";
				rModal += "			<form class=\"reservation_form\" method=\"post\">";
				rModal += "			<input type=\"hidden\" name=\"mac_id\" value=\"" + e.resourceId + "\" />";
				rModal += "				<div class=\"row\">";
				rModal += "			        <div class=\"form-group col-md-12\">";
				rModal += "			        	<label>From (" + moment(e.start._i).format("DD.MM.YYYY, HH:mm") + "):<\/label>";
				rModal += "			            <div class=\"input-group date text-center\">";
				rModal += "			                <input onkeydown=\"costCalculation();\" type=\"text\" class=\"form-control startInput\" value=\"" + moment(e.start._i).format("DD.MM.YYYY, HH:mm") + "\" \/>";
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
				rModal += "			                <input onkeydown=\"costCalculation();\" type=\"text\" class=\"form-control endInput\" value=\"" + moment(e.end._i).format("DD.MM.YYYY, HH:mm") + "\" \/>";
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
				    	hide: function (event, api) {
					        $('.startpicker').data("DateTimePicker").minDate(false);
					        $('.startpicker').data("DateTimePicker").maxDate(false);
					        $('.endpicker').data("DateTimePicker").minDate(false);
					        $('.endpicker').data("DateTimePicker").maxDate(false);
					        $(this).qtip('destroy')
				    	},
				    	show: function (event, api) {
							$.when(getQuota()).done(function() {
							    costCalculation()
							});


	        				var eStart = moment(e.start._i).format("YYYY/MM/DD, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
							var eEnd = moment(e.end._i).format("YYYY/MM/DD, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");

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

								//$('.startpicker').data("DateTimePicker").minDate(eStart);
						        //$('.startpicker').data("DateTimePicker").maxDate(eEnd);
						        //$('.endpicker').data("DateTimePicker").minDate(eStart);
						        //$('.endpicker').data("DateTimePicker").maxDate(eEnd);
						   		//$('.startpicker').data("DateTimePicker").date(eStart);
						        //$('.endpicker').data("DateTimePicker").date(eEnd);

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
					        	reserve();
					        });

					        $('.startpicker').on('dp.change', function (e) {
					            var mDate = new moment(e.date);
					            $(".startInput").val(mDate.format('DD.MM.YYYY, HH:mm'));
					            costCalculation();
					        });

					        $('.endpicker').on('dp.change', function (e) {
					            var mDate = new moment(e.date);
					            $(".endInput").val(mDate.format('DD.MM.YYYY, HH:mm'));
					            costCalculation();
					        });					    	
				    	}
					}  
				});*/
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
	<h4>Available quota: <span id="quotaMain"><?php echo $quota; ?></span> hours.</h4>
	<article>
		<legend>Search by form</legend>
		<form class="form-horizontal">
		<fieldset>
		<div class="form-group required">
		  <label class="col-md-4 control-label" for="selectmachine">Machine</label>
		  <div class="col-md-4">
			<select id="selectMachine" class="form-control selectpicker">
			<?php foreach ($machines as $machine): ?>
			<option value="<?php echo $machine->MachineID?>"><?php echo $machine->MachineID . " " . $machine->MachineName
			. " " . $machine->Manufacturer . " " . $machine->Model ?></option>
			<?php endforeach; ?>
			</select>
		  </div>
		</div>
		<div class="form-group">
		  <label class="col-md-4 control-label" for="searchInput">Day</label>
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
		  <label class="col-md-4 control-label" for="selectlenght">Reservation length</label>
		  <div class="col-md-4">
			<select id="selectLength" class="form-control selectpicker">
			  <option value="0">Not defined</option>
			  <option value="1">1 hour</option>
			  <option value="2">2 hours</option>
			  <option value="3">3 hours</option>
			  <option value="4">4 hours</option>
			  <option value="5">5 hours</option>
			  <option value="6">6 hours</option>
			  <option value="7">7 hours</option>
			  <option value="8">8 hours</option>
			  <option value="9">9 hours</option>
			  <option value="10">10 hours</option>
			</select>
		  </div>
		</div>

		<div class="form-group">
		  <label class="col-md-4 control-label" for="searchbutton"></label>
		  <div class="col-md-4">
			<a id="searchButton" class="btn btn-primary">
				<span class="glyphicon glyphicon-search" aria-hidden="true"></span> Search
			</a>
		  </div>
		</div>
		
		<div class="form-group">
		  <label class="col-md-2 control-label" for="results"></label>
		  <div class="col-md-6">
			<div class="well" id="results">Search something first!</div>
		  </div>
		</div>

		</fieldset>
		</form>
		
	</article>
	<article>
		<legend>Search by calendar</legend>
		<div id="calendar" style="position:relative"><div id="loader" class="loader" style='position:absolute;display:none;margin:auto;left: 0;top: 0;right: 0;bottom: 0;'></div></div>
	</article>	
</div>