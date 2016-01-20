<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Index page
$lang['fablab_homepage_title'] = 'Hello fabricator!';
$lang['fablab_homepage_content'] = 'Lorem ipsum dolor siut amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu. Sed arcu lectus auctor vitae, consectetuer et venenatis eget velit. Sed augue orci, lacinia eu tincidunt et eleifend nec lacus. Donec ultricies nisl ut felis, suspendisse potenti. Lorem ipsum ligula ut hendrerit mollis, ipsum erat vehicula risus, eu suscipit sem libero nec erat. Aliquam erat volutpat. Sed congue augue vitae neque. Nulla consectetuer porttitor pede. Fusce purus morbi tortor magna condimentum vel, placerat id blandit sit amet tortor.';

// User machine list: /info/machines
$lang['fablab_info_machines_title'] = 'Machine groups';
$lang['fablab_info_machines_content'] = '';
// People: /info/people
$lang['fablab_info_people_title'] = 'Fablab supervisors';
$lang['fablab_info_people_content'] = 'The people responsible for running the fabric lab';

// Reservation
// Basic schedule: /reservations/basic_schedule
$lang['fablab_reservations_basic_schedule_title'] = 'Basic schedule';
$lang['fablab_reservations_basic_schedule_content'] = "Here you can see assigned supervisors and active reservations. If you want to do reservation go to " . anchor("reservations/reserve", "reserve page");
// Active: /reservations/active
$lang['fablab_reservations_active_title'] = 'Active reservations';
$lang['fablab_reservations_active_content'] = 'List of all your active reservations. Please note that you can\'t cancel already running session.';
// Reserve: /reservations/reserve
$lang['fablab_reservations_reserve_title'] = 'Reserve a time';
$lang['fablab_reservations_reserve_content'] = "Remember that to be able to make a reservation for tomorrow, you have to reserve before {DEADLINE}".
		" today. Also you can make a reservation only {reservation_timespan} {interval} forward.";

// Contact us: /contact
$lang['fablab_contact_title'] = 'Some questions?';
$lang['fablab_contact_content'] = "Something on your mind? Please contact one of the administrators.<br/>Email: ***REMOVED***";
$lang['fablab_contact_body'] = "Email: ***REMOVED***";