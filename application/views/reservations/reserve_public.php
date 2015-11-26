<link rel="stylesheet" type="text/css"  href="<?php echo asset_url() . "css/jquery.qtip.min.css"; ?>" />
<script src="<?php echo asset_url() . "js/jquery.qtip.min.js"; ?>"  ></script>
<script type="text/javascript" src="<?=asset_url()?>js/bootstrap-notify.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?=asset_url()?>css/animate.css"/>

<script>
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
                    url: 'reserve_get_supervision_slots'
                }
            ],
		});//fullcalendar
	});//$function

</script>
<div class="container">
	<article>
		<legend>Supervision sessions</legend>
		<div id="calendar" style="position:relative"><div id="loader" class="loader" style='position:absolute;display:none;margin:auto;left: 0;top: 0;right: 0;bottom: 0;'></div></div>
	</article>	
</div>