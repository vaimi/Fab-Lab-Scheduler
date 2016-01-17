<!-- TODO -->
<!-- $reservation_deadline contains deadline e.g 16:00 in format HH:mm. This should limit the reservation-->
<!-- $is_admin (bool) determines if $reservation_deadline limitation is needed. -->
<link rel="stylesheet" type="text/css"  href="<?php echo asset_url() . "css/jquery.qtip.min.css"; ?>" />
<script src="<?php echo asset_url() . "js/jquery.qtip.min.js"; ?>"  ></script>
<script type="text/javascript" src="<?=asset_url()?>js/bootstrap-notify.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?=asset_url()?>css/animate.css"/>
<script type="text/javascript" src="<?=asset_url()?>js/bootstrap-select.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?=asset_url()?>css/bootstrap-select.min.css"/>
<link href="<?=asset_url()?>css/bootstrap-switch.css" rel="stylesheet">
<script src="<?=asset_url()?>js/bootstrap-switch.min.js"></script>

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
			$(".startInput").attr('disabled', true);
			$(".endInput").attr('disabled', true);
			$(".formButton").addClass('disabled');
			$('.selectpicker').prop('disabled', true);
  			$('.selectpicker').selectpicker('refresh');
		} else {
			$(".startInput").removeAttr('disabled');
			$(".endInput").removeAttr('disabled');
			$('.formButton').removeClass('disabled');
			$('.selectpicker').prop('disabled', false);
  			$('.selectpicker').selectpicker('refresh');
		}
	}

	function restore_slot() {
		// Send form to controller 
		var post_data = {
			'id': $(".restoreButton").data("id"), 
			'csrf_test_name': csrf_token
		};

		disableForm(true);

		$.ajax({
			type: "POST",
			url: "reservations_restore",
			data: post_data,
			success: function(data) {
				disableForm(false);
				if (data.length > 0) {
				var message = $.parseJSON(data);
					if (message.success == 1) {
						alerter("success", "Restore successful");
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

	function cancel(restore) {
		// Send form to controller 
		var post_data = {
			'id': $(".cancelButton").data("id"),
			'restore': restore, 
			'csrf_test_name': csrf_token
		};

		disableForm(true);

		$.ajax({
			type: "POST",
			url: "reservations_cancel",
			data: post_data,
			success: function(data) {
				disableForm(false);
				if (data.length > 0) {
				var message = $.parseJSON(data);
					if (message.success == 1) {
						alerter("success", "Cancellation successful");
						$(".qtip").qtip('hide');
						$('#calendar').fullCalendar('refetchEvents');
					} else {
						if (message.errors[0] == "restoreable") {
							makeRestoreQtip($(".cancelButton"));
						} else {
							for(var error in message.errors) {
								alerter("warning", message.errors[error]); 
							}
						}
					}
				}
			}
		}); 
	}

	function state() {
		var post_data = {
			'1': $("#state_1").is(':checked'),
			'2': $("#state_2").is(':checked'),
			'3': $("#state_3").is(':checked'),
			'4': $("#state_4").is(':checked'),
			'5': $("#state_5").is(':checked'), 
			'csrf_test_name': csrf_token
		};
		$(".state-switch").bootstrapSwitch('disabled', true);
		$("#refreshButton").addClass('disabled');
		$.ajax({
			type: "POST",
			url: "reservations_set_state_filtration",
			data: post_data,
			success: function(data) {
				if (data.length > 0) {
				$(".state-switch").bootstrapSwitch('disabled', false);
				$("#refreshButton").removeClass('disabled');
				var message = $.parseJSON(data);
					if (message.success == 1) {
						alerter("success", "Update successful");
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
	
	function reserve(force) {
		var start = moment($(".startInput").val(), "DD.MM.YYYY, HH:mm");
		var end = moment($(".endInput").val(), "DD.MM.YYYY, HH:mm");
		// Send form to controller 
		var post_data = {
			'mac_id': $(".reservation_form input[name='mac_id']").val(),
			'start': start.format('YYYY-MM-DD HH:mm'),
			'end': end.format('YYYY-MM-DD HH:mm'),
			'machines': $("#selectMachine").val(),
			'user': $("#selectUser").val(),
			'force': force,
			'repair': $(".repairCheck").is(':checked'), 
			'csrf_test_name': csrf_token
		};

		disableForm(true);

		$.ajax({
			type: "POST",
			url: "reservations_reserve",
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
						if (message.errors[0] == "Overlapping") {
							makeOverlapQtip($(".reserveButton"));
						} else {
							for(var error in message.errors) {
								alerter("warning", message.errors[error]); 
							}
						}
					}
				}
			}
		}); 
	}

	$(function() { // document ready
		$(".state-switch").bootstrapSwitch();
		$('#refreshButton').unbind("click").click(function(){ 
        	state();
		});
		$('#calendar').fullCalendar({
			schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
			snapDuration: '00:01',
			selectable:true,
			unselectAuto:false,
			//selectHelper:true,
			editable: false, // enable draggable events
			allDaySlot: false,
			firstDay: 1,
            timeFormat: 'HH:mm',
            slotLabelFormat: 'HH:mm',
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
		            slotLabelFormat: [
					    'MMM DD', // top level of text
					    'HH:mm'        // lower level of text
					]
		        }
		    },
			resourceLabelText: 'Machines',
			defaultView: 'timelineDay',
			resourceGroupField: 'groupText',
			resources: { // you can also specify a plain string like 'json/resources.json'
				url: '<?php echo base_url('admin/reservations_get_machines')?>',
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
                    url: "<?=base_url('reservations/reserve_get_reserved_slots')?>"
                },
                {
                    url: "<?=base_url('reservations/reserve_get_supervision_slots')?>",
                    rendering: 'background'
                }
            ],
            select: function( start, end, jsEvent, view, resource )	{
            	if (resource == null) {
            		var resource = {
            			id:"mac_-1"
            		}
            	}
            	var machine = resource.id;
            	var machineSplit = machine.split("_");
            	if (machineSplit[0] == "mac") {
            		var eStart = start.format("DD.MM.YYYY, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
					var eEnd = end.format("DD.MM.YYYY, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
					makeAdminQtip(jsEvent, machine, eStart, eEnd);
            	} else {
					$("#calendar").fullCalendar('unselect');
				}
            },
            eventAfterRender : function( e, element, view ) {
            	if (e.reserved==1) {
	            	$(element).click(function(){ 
	            		makeReservationQtip(element, e);
					});
				}
				if (e.reserved==2) {
	            	$(element).click(function(){ 
	            		makeReservationQtip(element, e, true);
					});
				}
            }
		});//fullcalendar
	});//$function

	function makeRestoreQtip(elementId) {
		var sModal="<p>What you want to do?</p>";
		sModal += "	<div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
		sModal += "		<a class=\"btn btn-success formButton undoButton\" >Undo</a>";
		sModal += "	<\/div>";
		sModal += "	<div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
		sModal += "		<a class=\"btn btn-warning formButton restoreButton\" >Restore</a>";
		sModal += "		<a class=\"btn btn-danger formButton ignoreButton\" >Ignore</a>";
		sModal += "	<\/div>";
		
		$(elementId).qtip({ // Grab some elements to apply the tooltip to
			show: { 
				effect: function() { $(this).slideDown(); },
				solo: false,
            	ready: true
	        },
	        hide: { 
	        	event: false
	        },
		    content: {
			    title: "Restoreable reservations",
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
		    		api.focus();
		    		$('.undoButton').unbind("click").click(function(){ 
						api.toggle(false);
			        });
			        $('.restoreButton').unbind("click").click(function(){ 
			        	cancel(1);
			        });
			        $('.ignoreButton').unbind("click").click(function(){ 
			        	cancel(2);
			        });
		    	}
			}  
		});
	}

	function makeOverlapQtip(elementId) {
		var sModal="<p>What you want to do?</p>";
		sModal += "	<div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
		sModal += "		<a class=\"btn btn-success formButton undoButton\" >Undo</a>";
		sModal += "	<\/div>";
		sModal += "	<div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
		sModal += "		<a class=\"btn btn-warning formButton overlapButton\" >Overlap</a>";
		sModal += "		<a class=\"btn btn-danger formButton deleteButton\" >Cancel</a>";
		sModal += "	<\/div>";
		
		$(elementId).qtip({ // Grab some elements to apply the tooltip to
			show: { 
				effect: function() { $(this).slideDown(); },
				solo: false,
            	ready: true
	        },
	        hide: { 
	        	event: false
	        },
		    content: {
			    title: "Reservation is overlapping",
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
		    		api.focus();
		    		$('.undoButton').unbind("click").click(function(){ 
						api.toggle(false);
			        });
			        $('.overlapButton').unbind("click").click(function(){ 
			        	reserve(1);
			        });
			        $('.deleteButton').unbind("click").click(function(){ 
			        	reserve(2);
			        });
		    	}
			}  
		});
	}

	function makeReservationQtip(elementId, e, restore) {
		var machine = e.resourceId;
		var eStart = e.start.format("DD.MM.YYYY, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
		var eEnd = e.end.format("DD.MM.YYYY, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
		if (restore == true) {
			var text="Restore";
			var task="restoreButton";
		} else {
			var text="Cancel";
			var task="cancelButton";
		}
		var sModal="<p>Reservation id: "+ e.reservation_id + "</p>";
		sModal += "<p>Start time: " + e.start.format("DD.MM.YYYY, HH:mm") + "</p>";
		sModal += "<p>End time: " + e.end.format("DD.MM.YYYY, HH:mm") + "</p><br>";
		sModal += "<p>User id: " + e.user_id + "</p>";
		sModal += "<p>Level: " + e.user_level + "</p>";		
		sModal += "<p>Name: " + e.first_name + " " + e.surname + "</p>";
		sModal += "<p>Email: " + e.email + "</p>";
		sModal += "			    <div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
		sModal += "			    	<a data-id=" + e.reservation_id + "  class=\"btn btn-danger formButton " + task + "\" >" + text + " reservation</a>";
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
		    	},
		    	show: function (event, api) {
		    		$('.cancelButton').unbind("click").click(function(){ 
			        	cancel(0);
			        });
			        $('.restoreButton').unbind("click").click(function(){ 
			        	restore_slot();
			        });
		    	}

			}  
		});
	}

	function makeAdminQtip(jsEvent, machine, e_Start, e_End) {
		var sModal="";
		sModal += "			<form class=\"reservation_form\" method=\"post\">";
		sModal += "			<input type=\"hidden\" name=\"mac_id\" value=\"" + machine + "\" />";
		sModal += "				<div class=\"row\">";
		sModal += "			        <div class=\"form-group col-md-12\">";
		sModal += "			        	<label>From:<\/label>";
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
		sModal += "			        	<label>To:<\/label>";	
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
		sModal += "			    <div class=\"row\">";
		sModal += "			        <div class=\"form-group col-md-12\">";	
		sModal += "		  				<label>Machine</label>";
		sModal += "  					<div>";
		sModal += "							<select id=\"selectMachine\" data-actions-box=\"true\" data-size=\"5\" multiple data-live-search=\"true\" data-selected-text-format=\"count\" class=\"form-control selectpicker\">";
		<?php foreach ($machines as $machine) {
		echo "sModal += \"	<option value='" . $machine->MachineID . "'>" . $machine->MachineID . " " . $machine->Manufacturer . " " . $machine->Model . "</option>\";\n";
		}?>
		sModal += "							<\/select>";
		sModal += "  					<\/div>";
		sModal += "			    	<\/div>";
		sModal += "			    <\/div>";
		sModal += "			    <div class=\"row\">";
		sModal += "			        <div class=\"form-group col-md-12\">";	
		sModal += "		  				<label>User</label>";
		sModal += "  					<div>";
		sModal += "							<select id=\"selectUser\" data-size=\"5\" data-live-search=\"true\" class=\"form-control selectpicker\">";
		<?php foreach ($users as $user) {
		echo "sModal += \"	<option value='" . $user->id . "'>" . $user->id . " " . $user->first_name . " " . $user->surname . "</option>\";\n";
		}?>
		sModal += "							<\/select>";
		sModal += "  					<\/div>";
		sModal += "			    	<\/div>";
		sModal += "			    <\/div>";
		sModal += "			    <div class=\"row\">";
		sModal += "			  		<div class=\"form-group col-md-12 checkbox\">";
		sModal += "    					<label>";
		sModal += "    						<input class=\"repairCheck\" type=\"checkbox\"> Repair?";
		sModal += "    					</label>";
  		sModal += "			    	<\/div>";
		sModal += "			    <\/div>";
		sModal += "				<br>";
		sModal += "			    <div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
		sModal += "			    	<a class=\"btn btn-primary formButton reserveButton\" >Reserve</a>";
		sModal += "			    <\/div>";
		sModal += "			</form>";
		$(jsEvent.target).qtip({ // Grab some elements to apply the tooltip to
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
		    		$("#calendar").fullCalendar('unselect');
			        $(this).qtip('destroy');
		    	},
		    	show: function (event, api) {
    				var eStart = moment(e_Start, "DD.MM.YYYY, HH:mm").format("YYYY/MM/DD, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
					var eEnd = moment(e_End, "DD.MM.YYYY, HH:mm").format("YYYY/MM/DD, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
					var machineSplit = machine.split("_");
					$('.selectpicker').selectpicker();
					$('#selectMachine').selectpicker('val', machineSplit[1]);
					$('#selectUser').selectpicker('val', <?=$this->session->id?>);
					$('.startpicker').datetimepicker({
						locale: 'en-gb',
						format: 'YYYY/MM/DD, HH:mm',
				    	widgetPositioning: {
							horizontal: "left",
							vertical: "top"
					    },
	                    inline: true,
        				sideBySide: false,
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
        				defaultDate: eEnd
			        });

			        $('.startExp').click(function(){ 
			        	$('.startpicker').collapse('toggle'); 
			        	return false; 
			        });

			        $('.endExp').click(function(){ 
			        	$('.endpicker').collapse('toggle'); 
			        	return false; 
			        });

			        $('.machineAll').click(function(){ 
			        	$('#selectMachine').selectpicker('selectAll');
			        	return false; 
			        });

			     	$('.machineNone').click(function(){ 
			        	$('#selectMachine').selectpicker('deselectAll'); 
			        	return false; 
			        });

			        $('.reserveButton').unbind("click").click(function(){ 
			        	reserve(0);
			        });

			        $('.startpicker').on('dp.change', function (e) {
			            var mDate = new moment(e.date, "YYYY/MM/DD, HH:mm");
			            $(".startInput").val(mDate.format('DD.MM.YYYY, HH:mm'));
			        });

			        $('.endpicker').on('dp.change', function (e) {
			            var mDate = new moment(e.date, "YYYY/MM/DD, HH:mm");
			            $(".endInput").val(mDate.format('DD.MM.YYYY, HH:mm'));
			        });
		    	}
			}  
		});
	}
</script>
<style>
    .collapse {
        position: inherit;
    }
    .fc-timeline-event {
    	cursor: pointer;
    }
    .qtip-content
	{
	    overflow: visible;
	}
</style>
<div class="container">

	<article>
		<p>HINT: Unlike in user calendar, you can make reservation just by dragging over the area you want to make reservation.</p>
		<div class="btn-toolbar well well-sm" role="toolbar" aria-label="...">
			<label>Show slots:</label>
			<input data-label-text="active" class="state-switch" type="checkbox" id="state_1" <?php if (in_array(1, $states)) echo "checked";?>>
			<input data-label-text="user cancel" class="state-switch" type="checkbox" id="state_2" <?php if (in_array(2, $states)) echo "checked";?>>
			<input data-label-text="admin cancel" class="state-switch" type="checkbox" id="state_3" <?php if (in_array(3, $states)) echo "checked";?>>
			<input data-label-text="repair" class="state-switch" type="checkbox" id="state_4" <?php if (in_array(4, $states)) echo "checked";?>>
			<input data-label-text="repair cancel" class="state-switch" type="checkbox" id="state_5" <?php if (in_array(5, $states)) echo "checked";?>>			
			<a type="button" id="refreshButton" class="btn btn-primary pull-right">
			  <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Refresh
			</a>
		</div>
		<div id="calendar" style="position:relative"><div id="loader" class="loader" style='position:absolute;display:none;margin:auto;left: 0;top: 0;right: 0;bottom: 0;'></div></div>
	</article>
</div>