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
	
	function reserve(nightslot) {
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

	$(function() { // document ready
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
            	$(element).click(function(){ 
            		makeReservationQtip(element, e);
				});
            }
		});//fullcalendar
	});//$function

	function makeReservationQtip(elementId, e) {
		var machine = e.resourceId;
		var eStart = e.start.format("DD.MM.YYYY, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
		var eEnd = e.end.format("DD.MM.YYYY, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");

		var sModal="<p>Reservation id: "+ e.reservation_id + "</p>";
		sModal += "<p>Start time: " + e.start.format("DD.MM.YYYY, HH:mm") + "</p>";
		sModal += "<p>End time: " + e.end.format("DD.MM.YYYY, HH:mm") + "</p><br>";
		sModal += "<p>User id: " + e.user_id + "</p>";
		sModal += "<p>Level: " + e.user_level + "</p>";		
		sModal += "<p>Name: " + e.surname + "</p>";
		sModal += "<p>Email: " + e.email + "</p>";
		sModal += "			    <div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
		sModal += "			    	<a data-id=" + e.reservation_id + "  class=\"btn btn-danger cancelButton\" >Cancel reservation</a>";
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

	function makeAdminQtip(jsEvent, machine, e_Start, e_End) {
		var sModal="";
		sModal += "			<form class=\"reservation_form\" method=\"post\">";
		sModal += "			<input type=\"hidden\" name=\"mac_id\" data-nightslot=\"0\" value=\"" + machine + "\" />";
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
		sModal += "  					<div class=\"input-group\">";
		sModal += "							<select id=\"selectMachine\" data-size=\"5\" multiple data-live-search=\"true\" data-selected-text-format=\"count\" class=\"form-control selectpicker\">";
		<?php foreach ($machines as $machine) {
		echo "sModal += \"	<option value='" . $machine->MachineID . "'>" . $machine->MachineID . " " . $machine->Manufacturer . " " . $machine->Model . "</option>\";\n";
		}?>
		sModal += "							<\/select>";
		sModal += "			                <a class=\"input-group-addon machineAll\">";
		sModal += "			                    <span class=\"glyphicon glyphicon-check\"><\/span>";
		sModal += "			                <\/a>";
		sModal += "			                <a class=\"input-group-addon machineNone\">";
		sModal += "			                    <span class=\"glyphicon glyphicon-unchecked\"><\/span>";
		sModal += "			                <\/a>";
		sModal += "  					<\/div>";
		sModal += "			    	<\/div>";
		sModal += "			    <\/div>";
		sModal += "			    <div class=\"row\">";
		sModal += "			        <div class=\"form-group col-md-12\">";	
		sModal += "		  				<label>User</label>";
		sModal += "  					<div>";
		sModal += "							<select id=\"selectUser\" data-size=\"5\" data-live-search=\"true\" class=\"form-control selectpicker\">";
		<?php foreach ($users as $user) {
		echo "sModal += \"	<option value='" . $user->id . "'>" . $user->id . " " . $user->surname . "</option>\";\n";
		}?>
		sModal += "							<\/select>";
		sModal += "  					<\/div>";
		sModal += "			    	<\/div>";
		sModal += "			    <\/div>";
		sModal += "				<br>";
		sModal += "			    <div class=\"btn-group\" role=\"group\" aria-label=\"...\">";
		sModal += "			    	<a class=\"btn btn-primary reserveButton\" >Reserve</a>";
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
					var eEnd = moment(e_End, "DD.MM.YYYY HH:mm").format("YYYY/MM/DD, HH:mm");//.format("dddd, MMMM Do YYYY, h:mm:ss a");
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
			            var mDate = new moment(e.date);
			            $(".startInput").val(mDate.format('DD.MM.YYYY HH:mm'));
			        });

			        $('.endpicker').on('dp.change', function (e) {
			            var mDate = new moment(e.date);
			            $(".endInput").val(mDate.format('DD.MM.YYYY HH:mm'));
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
		<div id="calendar" style="position:relative"><div id="loader" class="loader" style='position:absolute;display:none;margin:auto;left: 0;top: 0;right: 0;bottom: 0;'></div></div>
	</article>	
</div>