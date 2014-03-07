<?
error_reporting(E_ALL);
/*
Plugin Name: Lectionary
Plugin URI: http://www.iantearle.com/
Description: Gives the Lectionary Sunday of the current week
Version: 1.0
Author: Ian Tearle
Author URL: http://www.iantearle.com/
*/
require_once('Lectionary.class.php');
require_once('cal.php');

/*
##################################################
#
# Filename..........: $RCSfile: lectionary.php,v $
# Original Author...: Anthony L. Awtrey
# Version...........: $Revision: 0.1 $
# Last Modified By..: $Author: aawtrey $
# Last Modified.....: $Date: 2006/09/19 04:17:55 $
#
# Copyright 2006 Anthony Awtrey
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
*/
$year = date('Y');
$month = date('m');
$day = date('d');

$lectionary = new Lectionary(2013);
$l = $lectionary->get_calendar_day(gmmktime(0,0,0,$month,$day,$year));

if(!$l) {
	$sat = 7; //saturday = end of week
	$current_day=date('N');
	$days_remaining_until_sat = $sat - $current_day;

	$ts_start = strtotime("-$current_day days");
	$ts_end = strtotime("+$days_remaining_until_sat days");

	$next_sunday = strtotime(date('d-m-Y',$ts_end));

	$year = gmstrftime('%Y',$next_sunday);
	$month = gmstrftime('%m',$next_sunday);
	$day = gmstrftime('%d',$next_sunday);

	$l = $lectionary->get_calendar_day(gmmktime(0,0,0,$month,$day,$year));
}

if(!$year) {
	$year = gmstrftime('%Y');
}

$page = '';

if($year && $month && $day) {
	foreach($l as $key => $val) {
		$page .= '<div class="title"><h3>'.$lectionary->get_title($key, $val).' (Year: '.$lectionary->get_cycle($key).")</h3></div>";
		$page .= '<div class="date"><p><sup>'.date('D jS', strtotime($lectionary->get_long_date($key))).'</sup><br /><span class="month">'.date('M', strtotime($lectionary->get_long_date($key))).'</span></p></div>';
		$page .= '<div class="descr"><p>';
		$page .= '<b>Old Testament&#58;</b> '. $lectionary->get_scripture($key,$val,'old')    . "<br />";//urlencode($lectionary->get_scripture($key,$val,'old'))
		$page .= '<b>Psalms&#58;</b> '. $lectionary->get_scripture($key,$val,'psalms') . "<br />"; //urlencode($lectionary->get_scripture($key,$val,'psalms'))
		$page .= '<b>Epistle&#58;</b> '. $lectionary->get_scripture($key,$val,'new')    . "<br />"; //urlencode($lectionary->get_scripture($key,$val,'new'))
		$page .= '<b>Gospel&#58;</b> '. $lectionary->get_scripture($key,$val,'gospel') . "<br />"; //urlencode($lectionary->get_scripture($key,$val,'gospel'))
		$page .= "</p></div>";
	}
} else {
	require_once("cal.php");
	$c = new Calendar();
	$lectionary = new Lectionary($year);
	$l = $lectionary->get_calendar();
	foreach($l as $key => $val) {
		$year  = gmstrftime('%Y',$key);
		$month = gmstrftime('%m',$key);
		$day   = gmstrftime('%d',$key);
		$c->set_appt($year,$month,$day,'?year='.$year.'&month='.$month.'&day='.$day);
	}
	$page->content .= $c->render($year);
}

//$page->display();



/*This loads the Lectionary.class and makes it available
$lectionary = new Lectionary();

// Returns an associative array containing the key as the date in seconds
// since January 1, 1970 and the value as the day's Lectionary index number.
$l = $lectionary->get_calendar();

// Loop through each date and print it and the day's title and scripture
$page->content .= "<h2> Lectionary for ".gmstrftime("%Y", gmmktime())."</h2>";
foreach ( $l as $key => $val) {
  $page->content .= '<h3>'.$lectionary->get_title($key,$val).' (Year '.$lectionary->get_cycle($key).")</h3>";
  $page->content .= "</p>";
  $page->content .= '<b>Date:</b> '.$lectionary->get_long_date($key)."<br />";
  $page->content .= '<b>Old Testament:</b> <a href="../bible/?verse=' .
                    urlencode($lectionary->get_scripture($key,$val,'old')) . '">' .
                    $lectionary->get_scripture($key,$val,'old')    . "</a><br />";
  $page->content .= '<b>Psalms:</b>        <a href="../bible/?verse=' .
                    urlencode($lectionary->get_scripture($key,$val,'psalms')) .'">'.
                    $lectionary->get_scripture($key,$val,'psalms') . "</a><br />";
  $page->content .= '<b>New Testament:</b> <a href="../bible/?verse=' .
                    urlencode($lectionary->get_scripture($key,$val,'new'))    .'">'.
                    $lectionary->get_scripture($key,$val,'new')    . "</a><br />";
  $page->content .= '<b>Gospel:</b>        <a href="../bible/?verse=' .
                    urlencode($lectionary->get_scripture($key,$val,'gospel')) .'">'.
                    $lectionary->get_scripture($key,$val,'gospel') . "</a><br />";
  $page->content .= "</p>";
}
*/


//print_r($page->content);

if(function_exists('add_variable')){
	add_variable('lectionary:'.(string) safe_tpl($page));
}
?>