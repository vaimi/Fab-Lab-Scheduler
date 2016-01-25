<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Menu
$lang['fablab_menu_title'] = "Oulu's FabLab";
$lang['fablab_menu_home_title'] = 'Home';
$lang['fablab_menu_info_title'] = 'Info';
$lang['fablab_menu_info_machines'] = 'Machines';
$lang['fablab_menu_info_people'] = 'People';
$lang['fablab_menu_reservations_title'] = 'Reservations';
$lang['fablab_menu_reservations_basic'] = 'Basic schedule';
$lang['fablab_menu_reservations_active'] = 'Your active reservations';
$lang['fablab_menu_reservations_reserve'] = 'Reserve';
$lang['fablab_menu_contact_title'] = "Contact us";
$lang['fablab_menu_admin_title'] = "Admin";
$lang['fablab_menu_admin_general'] = 'General settings';
$lang['fablab_menu_admin_machines'] = 'Machines';
$lang['fablab_menu_admin_timetables'] = 'Timetables';
$lang['fablab_menu_admin_reservations'] = 'Reservations';
$lang['fablab_menu_admin_users'] = 'Users';
$lang['fablab_menu_admin_groups'] = 'Groups';
$lang['fablab_menu_admin_email'] = 'Send email';
$lang['fablab_menu_register'] = 'Register';
$lang['fablab_menu_login'] = 'Login';
$lang['fablab_menu_logout'] = 'Log out';

// Index page
$lang['fablab_homepage_title'] = 'Hello fabricator!';
$lang['fablab_homepage_content'] = 'Lorem ipsum dolor siut amet, consectetuer adipiscing elit. Sed posuere interdum sem. Quisque ligula eros ullamcorper quis, lacinia quis facilisis sed sapien. Mauris varius diam vitae arcu. Sed arcu lectus auctor vitae, consectetuer et venenatis eget velit. Sed augue orci, lacinia eu tincidunt et eleifend nec lacus. Donec ultricies nisl ut felis, suspendisse potenti. Lorem ipsum ligula ut hendrerit mollis, ipsum erat vehicula risus, eu suscipit sem libero nec erat. Aliquam erat volutpat. Sed congue augue vitae neque. Nulla consectetuer porttitor pede. Fusce purus morbi tortor magna condimentum vel, placerat id blandit sit amet tortor.';

// User machine list: /info/machines
$lang['fablab_info_machines_title'] = 'Machine groups';
$lang['fablab_info_machines_content'] = '';

$lang['fablab_info_machines_table_header'] = "View detail";
$lang['fablab_info_machines_table_column_1'] = "MID";
$lang['fablab_info_machines_table_column_2'] = "Manufacturer & Model";
$lang['fablab_info_machines_details_manufacturer'] = "Manufacturer: ";
$lang['fablab_info_machines_details_model'] = "Model: ";
$lang['fablab_info_machines_details_supervision'] = "Need supervision: ";

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
$lang['fablab_reservations_active_alert_cancel'] = "Cancellation succeeded.";
$lang['fablab_reservations_active_table_id'] = "ID";
$lang['fablab_reservations_active_table_machine'] = "Machine";
$lang['fablab_reservations_active_table_for'] = "Reserved for";
$lang['fablab_reservations_active_table_actions'] = "Actions";
$lang['fablab_reservations_active_table_cancel'] = "Cancel";
// Reserve: /reservations/reserve
$lang['fablab_reservations_reserve_title'] = 'Reserve a time';
$lang['fablab_reservations_reserve_content'] = "Remember that to be able to make a reservation for tomorrow, you have to reserve before {DEADLINE}".
		" today. Also you can make a reservation only {reservation_timespan} {interval} forward.";

$lang['fablab_reservations_reserve_tokens'] = "Available tokens: ";

$lang['fablab_reservations_reserve_search_title'] = "Search by form";
$lang['fablab_reservations_reserve_search_machine'] = "Machine";
$lang['fablab_reservations_reserve_search_day'] = "Day";
$lang['fablab_reservations_reserve_search_length'] = "Reservation length";
$lang['fablab_reservations_reserve_search_length_undefined'] = "Not defined";
$lang['fablab_reservations_reserve_search_length_hour'] = "hour";
$lang['fablab_reservations_reserve_search_length_hours'] = " hours";
$lang['fablab_reservations_reserve_search_none'] = '"No results."';
$lang['fablab_reservations_reserve_search_button'] = "Search";
$lang['fablab_reservations_reserve_search_default'] = "Search something first!";

$lang['fablab_reservations_reserve_calendar_title'] = "Search by calendar";

$lang['fablab_reservations_reserve_qtip_title'] = "Reservation";// (time)
$lang['fablab_reservations_reserve_qtip_from'] = "From ";// (time)
$lang['fablab_reservations_reserve_qtip_to'] = "To ";// (time)
$lang['fablab_reservations_reserve_qtip_length'] = "Length: ";// (time)
$lang['fablab_reservations_reserve_qtip_tokens'] = "Tokens left: ";// (time)
$lang['fablab_reservations_reserve_qtip_reserve'] = "Reserve";// (time)

$lang['fablab_reservations_reserve_night_qtip_title'] = "Reservation";// (time)
$lang['fablab_reservations_reserve_night_qtip_from'] = "Preparation time from:";// (time)
$lang['fablab_reservations_reserve_night_qtip_to'] = "Preparation time to:";// (time)
$lang['fablab_reservations_reserve_night_qtip_next'] = "Next supervision start:";
$lang['fablab_reservations_reserve_night_qtip_length'] = "Potential length: ";// (time)
$lang['fablab_reservations_reserve_night_qtip_tokens'] = "Tokens left: ";// (time)
$lang['fablab_reservations_reserve_night_qtip_reserve'] = "Reserve";// (time)

$lang['fablab_reservations_reserve_succesful'] = "Reservation successful";

// Contact us: /contact
$lang['fablab_contact_title'] = 'Some questions?';
$lang['fablab_contact_content'] = "Something on your mind? Please contact one of the administrators.<br/>Email: ";
$lang['fablab_contact_body'] = "Email: ";

$lang['fablab_register_title'] = "REGISTRATION";
$lang['fablab_register_username_label'] = "User name";
$lang['fablab_register_username_placeholder'] = "User name";
$lang['fablab_register_username_help'] = "Length between 5 and 100 characters.";
$lang['fablab_register_email_label'] = "Email address";
$lang['fablab_register_email_placeholder'] = "Email address";
$lang['fablab_register_email_help'] = "Members of the university should use email provided by the university";
$lang['fablab_register_password_label'] = "Password";
$lang['fablab_register_password_placeholder'] = "Password";
$lang['fablab_register_password_help'] = "Length between 5 and 20 characters.";
$lang['fablab_register_password_confirm_placeholder'] = "Confirm password";
$lang['fablab_register_password_confirm_error'] = "Whoops, these aren't the same";
$lang['fablab_register_name_label'] = "Name";
$lang['fablab_register_firstname_placeholder'] = "First name";
$lang['fablab_register_surname_placeholder'] = "Last name";
$lang['fablab_register_phone_label'] = "Phone number";
$lang['fablab_register_phone_placeholder'] = "Phone number";
$lang['fablab_register_address_label'] = "Address";
$lang['fablab_register_address_placeholder'] = "Address";
$lang['fablab_register_zip_placeholder'] = "Postal code";
$lang['fablab_register_company_label'] = "Company";
$lang['fablab_register_company_placeholder'] = "Company";
$lang['fablab_register_studentid_label'] = "Student number";
$lang['fablab_register_studentid_placeholder'] = "Student number";
$lang['fablab_register_button_register'] = "Register";
$lang['fablab_register_button_close'] = "Close";

$lang['fablab_login_title'] = "Log in to System";
$lang['fablab_login_email_label'] = "Email:";
$lang['fablab_login_email_placeholder'] = "Email address";
$lang['fablab_login_password_label'] = "Password";
$lang['fablab_login_password_placeholder'] = "Password";
$lang['fablab_login_remember'] = "Remember";
$lang['fablab_login_button_login'] = "Log in";
$lang['fablab_login_button_reset'] = "Reset it";
$lang['fablab_login_reset'] = "Forgot your password?";
$lang['fablab_login_button_close'] = "Close";

$lang['fablab_profile_user_title'] = "Profile";
$lang['fablab_profile_user_email'] = "Email:";
$lang['fablab_profile_user_email_placeholder'] = "Email";
$lang['fablab_profile_user_password'] = "Password:";
$lang['fablab_profile_user_password_placeholder'] = "Password";
$lang['fablab_profile_user_password_confirm'] = "Confirm password:";
$lang['fablab_profile_user_password_confirm_placeholder'] = "Confirm password";
$lang['fablab_profile_user_username'] = "User name:";
$lang['fablab_profile_user_username_placeholder'] = "User name";
$lang['fablab_profile_user_firstname'] = "First name:";
$lang['fablab_profile_user_firstname_placeholder'] = "First name";
$lang['fablab_profile_user_surname'] = "Last name:";
$lang['fablab_profile_user_surname_placeholder'] = "Last name";
$lang['fablab_profile_user_phone'] = "Phone:";
$lang['fablab_profile_user_phone_placeholder'] = "Phone number";
$lang['fablab_profile_user_address'] = "Address:";
$lang['fablab_profile_user_address_placeholder'] = "Postal address";
$lang['fablab_profile_user_postal_placeholder'] = "Zip code";
$lang['fablab_profile_user_studentid'] = "Student ID:";
$lang['fablab_profile_user_studentid_placeholder'] = "Student ID";
$lang['fablab_profile_user_save'] = "Save";

$lang['fablab_profile_levels_title'] = "Machine skill levels";
$lang['fablab_profile_levels_table_mid'] = "MID";
$lang['fablab_profile_levels_table_name'] = "Machine name";
$lang['fablab_profile_levels_table_manufacturer'] = "Manufacturer";
$lang['fablab_profile_levels_table_model'] = "Model";
$lang['fablab_profile_levels_table_level'] = "Skill level";

$lang['fablab_profile_reservations_title'] = "Reservations history";
$lang['fablab_profile_reservations_table_mid'] = "MID";
$lang['fablab_profile_reservations_table_name'] = "Machine name";
$lang['fablab_profile_reservations_table_start'] = "Reservation start";
$lang['fablab_profile_reservations_table_end'] = "Reservation end";

$lang['fablab_fullcalendar_firstday'] = "1";
$lang['fablab_fullcalendar_timeFormat'] = "'HH:mm'";
$lang['fablab_fullcalendar_slotLabelFormat'] = "'HH:mm'";
$lang['fablab_fullcalendar_views_slotLabelFormat'] = "'MMM DD','HH:mm'";

$lang['fablab_fullcalendar_admin_info_title'] = "Reservation";
$lang['fablab_fullcalendar_admin_info_start'] = "Start time: ";
$lang['fablab_fullcalendar_admin_info_end'] = "End time: ";
$lang['fablab_fullcalendar_admin_info_first_name'] = "First name: ";
$lang['fablab_fullcalendar_admin_info_last_name'] = "Surname: ";
$lang['fablab_fullcalendar_admin_info_email'] = "Email: ";
$lang['fablab_fullcalendar_admin_info_time_format'] = "DD.MM.YYYY, HH:mm";

$lang['fablab_moment_full_format'] = "DD.MM.YYYY, HH:mm";
$lang['fablab_moment_day_format'] = "DD.MM.YYYY";
$lang['fablab_moment_time_format'] = "HH:mm";


