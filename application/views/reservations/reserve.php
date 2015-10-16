<script src="<?php echo asset_url();?>js/jquery-1.11.3.min.js"></script>
<script src="<?php echo asset_url();?>js/jquery.fn.gantt.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>

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
			  <option value="1">Option one</option>
			  <option value="2">Option two</option>
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
		<div class="gantt"></div>
	</article>
</div>
    <script>

        (function($) {

            "use strict";

            var today = moment();
			var andOneHours = moment().add(1, "hours");
            var andTwoHours = moment().add(2, "hours");

            var today_friendly = "/Date(" + today.valueOf() + ")/";
            var next_friendly = "/Date(" + andTwoHours.valueOf() + ")/";
			var next2_friendly = "/Date(" + andOneHours.valueOf() + ")/";

            $(".gantt").gantt({
                source: [{
                    name: "Testing",
                    desc: " ",
                    values: [{
                        from: today_friendly,
                        to: next_friendly,
                        label: "Test",
                        customClass: "ganttRed"
                    },{
                        from: today_friendly,
                        to: next2_friendly,
                        label: "Test2",
                        customClass: "ganttBlue"
                    }]
                },{
                    name: "Testing 2",
                    desc: " ",
                    values: [{
                        from: today_friendly,
                        to: next2_friendly,
                        label: "Test",
                        customClass: "ganttBlue"
                    }]
                }],
                scale: "hours",
                minScale: "hours",
                navigate: "scroll"
            });

        }) ( jQuery );

    </script>