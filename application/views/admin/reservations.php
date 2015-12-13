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

	var userQuota = 0;

	$(function() { // document ready
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
            ]	
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
	<article>
		<div id="calendar" style="position:relative"><div id="loader" class="loader" style='position:absolute;display:none;margin:auto;left: 0;top: 0;right: 0;bottom: 0;'></div></div>
	</article>	
</div>