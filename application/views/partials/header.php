<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Fab Lab Scheduler</title>
	
	<!-- Bootstrap -->
    <link href="<?php echo asset_url();?>css/bootstrap.min.css" rel="stylesheet" type="text/css"> 
	<link href="<?php echo asset_url();?>css/bootstrap-fablab.css" rel="stylesheet" type="text/css"> 
	<link href="<?php echo asset_url();?>css/jQueryGantt.css" rel="stylesheet" type="text/css"> 
	<link href="<?php echo asset_url();?>css/bootstrap-combobox.css" rel="stylesheet" type="text/css"> 
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="<?php echo asset_url();?>js/jquery-1.11.3.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo asset_url();?>js/bootstrap.min.js"></script>
    <script src="<?php echo asset_url();?>js/bootstrap-combobox.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	<style type="text/css">
      body {
        padding-top: 70px;
        padding-bottom: 20px;
      }
    </style>
	
	
	<link href="<?php echo asset_url();?>css/sortable-theme-bootstrap.css" rel="stylesheet" type="text/css">
	
	<!-- summernote editor -->
	<link href="<?php echo asset_url();?>css/summernote.css" rel="stylesheet">
	<link href="<?php echo asset_url();?>css/font-awesome.min.css" rel="stylesheet">
	<script src="<?php echo asset_url();?>js/summernote.min.js"></script>
	
	<!-- fullcalendar -->
	<link rel='stylesheet' href='<?php echo asset_url();?>css/fullcalendar.css' />
	<link rel='stylesheet' href='<?php echo asset_url();?>css/jquery-ui.min.css' />
	<script src="http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment-with-locales.min.js"></script>
	<script src='<?php echo asset_url();?>js/fullcalendar.js'></script>
	<script src='<?php echo asset_url();?>js/jquery-ui.min.js'></script>
	<!-- fullcalendar scheduler -->
	<link rel='stylesheet' href='<?php echo asset_url();?>css/scheduler.css' />
	<script src='<?php echo asset_url();?>js/scheduler.js'></script>
      
    <link rel="stylesheet" href="<?php echo asset_url();?>css/bootstrap-datetimepicker.min.css" />
    <script type="text/javascript" src="<?php echo asset_url();?>js/bootstrap-datetimepicker.min.js"></script>
      
  </head>
 
  <body>