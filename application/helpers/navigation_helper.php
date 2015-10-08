<?php
/**
 * Codeigniter Bootstrap
 * -------------------------------------------------------------------
 * Developed and maintained by Stijn Geselle <stijn.geselle@gmail.com>
 * Modified by fablabscheduler team
 * -------------------------------------------------------------------
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('set_active_nav')) {
    function set_active_nav($uri)
    {
        return is_current_uri($uri) ? ' class="active"' : '';
    }
}

if (!function_exists('echo_active_nav_parent')) {
    function echo_active_nav_parent($uri)
    {
        return is_parent_uri($uri) ? 'active' : '';
    }
}

if (!function_exists('is_current_uri')) {
    function is_current_uri($uri)
    {
        $CI =& get_instance();
        $curr_uri = $CI->uri->uri_string();

        return is_array($uri) && in_array($curr_uri, $uri) || $curr_uri == $uri;
    }
}

if (!function_exists('is_parent_uri')) {
	/* TODO: Might not work in every case*/
    function is_parent_uri($uri)
    {
        $CI =& get_instance();
        $curr_uri = $CI->uri->segment_array();
        return in_array($uri, $curr_uri);
    }
}

/* End of file navigation_helper.php */
/* Location: ./application/helpers/navigation_helper.php */
