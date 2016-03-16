<?php

function remove_dashboard_meta() {
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
        //remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
}
add_action( 'admin_init', 'remove_dashboard_meta' );

function add_jsapi_Scripts() {
	global $wpdb;
	$male_sex_count = $wpdb->get_results("
		SELECT * 
    		FROM $wpdb->posts 
    		INNER JOIN $wpdb->postmeta 
    		ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
    		WHERE   $wpdb->posts.post_type = 'patient' 
    		AND $wpdb->postmeta.meta_key = 'sex' 
    		AND $wpdb->postmeta.meta_value = 'Male' 
  	;"); 
   	$female_sex_count = $wpdb->get_results("
    		SELECT * 
    		FROM $wpdb->posts 
    		INNER JOIN $wpdb->postmeta 
    		ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
    		WHERE   $wpdb->posts.post_type = 'patient' 
    		AND $wpdb->postmeta.meta_key = 'sex' 
    		AND $wpdb->postmeta.meta_value = 'Female' 
  	;"); 
  
	wp_enqueue_script(
		'google-jsapi',
		'//www.google.com/jsapi',
		array(),
		0,
		true
	);

	wp_enqueue_script(
		'jsapi',
		plugin_dir_url( __FILE__ ).'assets/js/jsapi.js',
		array( 'google-jsapi', ),
		filemtime( plugin_dir_path( __FILE__ ).'assets/js/jsapi.js' ),
		true
	);
	
   	$male_sex_count = count($male_sex_count);
      	$female_sex_count = count($female_sex_count);
   
	//$male_sex_count = print $male_sex_count;
	wp_localize_script(
		'jsapi',
		'jsapi',
		array( 'exampleData' => array(
			array( 'Sex',    'Persentase pasien berdasarkan jenis kelamin ', ),
			array( 'Male',     $male_sex_count, ),
			array( 'Female',      $female_sex_count, ),
			//array( 'Commute',  2, ),
			//array( 'Watch TV', 2, ),
			//array( 'Sleep',    7, ),
		) )
	);
}
add_action('admin_print_scripts', 'add_jsapi_Scripts' );

add_action( 'wp_dashboard_setup', 'calendar_dashboard_setup_function' );
function calendar_dashboard_setup_function() {
	wp_add_dashboard_widget ( 'calendar_dashboard_widget', 'Appointment calendar', 'calendar_dashboard_widget_function' );
}
function calendar_dashboard_widget_function() {
	echo '<h2>Dentix calendar</h2>';
	appointment_get_posts(0);
	//fcal_get_future_posts(0);
}

function dentix_add_dashboard_widgets() {

	add_meta_box(
                'dentix_dashboard_widget',         // Widget slug.
                'Dentix Dashboard Widget',         // Title.
                'dentix_dashboard_widget_function', // Display function.
		'dashboard', 
		'side', 
		'high' 
        );	
}
add_action( 'wp_dashboard_setup', 'dentix_add_dashboard_widgets' );

function dentix_dashboard_widget_function() {

	echo '<h2>Dentix Statistics</h2>';
	global $wpdb;
	$sex_count = $wpdb->get_var("
    		SELECT COUNT(*) 
    		FROM $wpdb->posts 
    		INNER JOIN $wpdb->postmeta 
    		ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
    		WHERE   $wpdb->posts.post_type = 'patient' 
    		AND $wpdb->postmeta.meta_key = 'sex' 
  	;"); 
	echo '<p>Sex count is ' . $sex_count . '</p>'; 
}

add_action( 'wp_dashboard_setup', 'my_dashboard_setup_function' );
function my_dashboard_setup_function() {
	add_meta_box( 'my_dashboard_widget', 'My Widget Name', 'my_dashboard_widget_function', 'dashboard', 'side', 'high' );
}
function my_dashboard_widget_function($data) {
	echo '<h2>Dentix Metabox Statistics</h2>';
	?><div id="piechart"></div><?php
}

function fcal_javascript(){
	echo '
	<script type="text/javascript">
		function fcal_set_date(day,month,year){

				if(day > 0 && month <= 12 && month >= 0 && year > 0){
				document.getElementById("jj").value = day;
				document.getElementById("aa").value = year;
				document.getElementsByName("mm")[0].selectedIndex = month;
				}

		}
	</script>
	';
	echo '
	<style type="text/css">
	#wp-calendar {width: 100%; }
	#wp-calendar caption { text-align: right; color: #333; font-size: 12px; margin-top: 10px; margin-bottom: 15px; }
	#wp-calendar thead { font-size: 10px; }
	#wp-calendar thead th { padding-bottom: 10px; }
	#wp-calendar tbody { color: #aaa; }
	#wp-calendar tbody td { background: #f5f5f5; border: 1px solid #fff; text-align: center; padding:8px;}
	#wp-calendar tbody td:hover { background: #fff; }
	#wp-calendar tbody .pad { background: none; }
	#wp-calendar tfoot #next { font-size: 10px; text-transform: uppercase; text-align: right; }
	#wp-calendar tfoot #prev { font-size: 10px; text-transform: uppercase; padding-top: 10px; }
	';
}
add_action('admin_head', 'fcal_javascript');

function appointment_get_posts($onclick = 1){
global $wpdb, $wp_locale;

	$thisyear = gmdate('Y', current_time('timestamp'));
	$thismonth = gmdate('m', current_time('timestamp'));

	// Quick check. If we have no posts at all, abort!
	if ( !$posts ) {
		$gotsome = $wpdb->get_var("SELECT ID from $wpdb->posts WHERE post_type = 'appointment' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
		if ( !$gotsome ){
			get_future_calendar($thismonth,$thisyear,$onclick);
			return;
		}
	}

	get_future_calendar($thismonth,$thisyear,$onclick);

	//Technically thismonth is really nextmonth, but no reason to be technical about it
	//But if thismonth is 12 then we need to reset it, and add a year otherwise we will be checking
	// out the 13th month of this year.
	if($thismonth == 12){
		$thismonth = 0;
		$thisyear +=1;
	}
	// Get months this year and next with at least one post
	$future = $wpdb->get_results("
		SELECT 
		DISTINCT MONTH(meta_value) AS month, YEAR(meta_value) AS year
    		FROM $wpdb->posts 
    		INNER JOIN $wpdb->postmeta 
    		ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
		WHERE $wpdb->postmeta.meta_value >'$thisyear-".($thismonth+1)."-01'
    		AND $wpdb->posts.post_type = 'appointment' 
		AND $wpdb->posts.post_status = 'publish' 
    		AND $wpdb->postmeta.meta_key = 'appointment_date' 
		ORDER BY $wpdb->posts.post_date ASC
  	;"); 


	foreach($future as $now){
		get_future_calendar($now->month,$now->year);
	}
}


// Calendar Output...
function get_future_calendar( $thismonth ='', $thisyear='', $onclick=1, $initial=true ) {
	global $wpdb, $timedifference, $wp_locale;
	$unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);

	// week_begins = 0 stands for Sunday
	$week_begins = intval(get_option('start_of_week'));
	$add_hours = intval(get_option('gmt_offset'));
	$add_minutes = intval(60 * (get_option('gmt_offset') - $add_hours));

	echo '<table id="wp-calendar">
	<caption><em>' . $wp_locale->get_month($thismonth) . ' ' . $thisyear . '</em></caption>
	<thead>
	<tr>';

	$myweek = array();

	for ( $wdcount=0; $wdcount<=6; $wdcount++ ) {
		$myweek[] = $wp_locale->get_weekday(($wdcount+$week_begins)%7);
	}

	foreach ( $myweek as $wd ) {
		$day_name = (true == $initial) ? $wp_locale->get_weekday_initial($wd) : $wp_locale->get_weekday_abbrev($wd);
		echo "\n\t\t<th abbr=\"$wd\" scope=\"col\" title=\"$wd\">$day_name</th>";
	}

	echo '
	</tr>
	</thead>
	<tbody>
	<tr>';

	// Get days with posts
	$dayswithposts = $wpdb->get_results("
		SELECT 
		DISTINCT DAYOFMONTH(meta_value)
		FROM $wpdb->posts 
    		INNER JOIN $wpdb->postmeta 
		WHERE MONTH($wpdb->postmeta.meta_value) = '$thismonth'
		AND YEAR($wpdb->postmeta.meta_value) = '$thisyear'
		AND $wpdb->posts.post_type = 'appointment' 
		AND $wpdb->posts.post_status = 'publish'
		AND $wpdb->postmeta.meta_value > '" . current_time('mysql') . '\'', ARRAY_N);
	if ( $dayswithposts ) {
		foreach ( $dayswithposts as $daywith ) {
			$daywithpost[] = $daywith[0];
		}
	} else {
		$daywithpost = array();
	}



	if ( strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'camino') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'safari') )
		$ak_title_separator = "\n";
	else
		$ak_title_separator = ', ';

	$ak_titles_for_day = array();
    //sets the Density Thermometer
	$ak_posts_for_day = array();

	$ak_post_titles = $wpdb->get_results("
		SELECT post_title, DAYOFMONTH(meta_value) as dom 
		FROM $wpdb->posts 
    		INNER JOIN $wpdb->postmeta 
		WHERE YEAR($wpdb->postmeta.meta_value) = '$thisyear' 
		AND MONTH($wpdb->postmeta.meta_value) = '$thismonth' 
		AND $wpdb->postmeta.meta_value > '" . current_time('mysql'). "' 
		AND $wpdb->posts.post_type = 'appointment' AND $wpdb->posts.post_status = 'publish'"
	);
	if ( $ak_post_titles ) {
		foreach ( $ak_post_titles as $ak_post_title ) {
				if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
					$ak_titles_for_day['day_'.$ak_post_title->dom] = '';
				if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
					$ak_titles_for_day["$ak_post_title->dom"] = str_replace('"', '&quot;', wptexturize($ak_post_title->post_title));
				else
					$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . str_replace('"', '&quot;', wptexturize($ak_post_title->post_title));

                $ak_posts_for_day["$ak_post_title->dom"] +=1;

		}
	}


	// See how much we should pad in the beginning
	$pad = calendar_week_mod(date('w', $unixmonth)-$week_begins);
	if ( 0 != $pad ) { echo "\n\t\t".'<td colspan="'.$pad.'" class="pad">&nbsp;</td>'; }

	    //Determines the Density Thermometer colors
	    $thermo = Array( "#BDFFBE", "#7AFFDE", "#2FEEFF", "#108BFF", "#0E72FF" );


	$daysinmonth = intval(date('t', $unixmonth));
	for ( $day = 1; $day <= $daysinmonth; ++$day ) {
		if ( isset($newrow) && $newrow )
			echo "\n\t</tr>\n\t<tr>\n\t\t";
		$newrow = false;

		if ( $day == gmdate('j', (time() + (get_option('gmt_offset') * 3600))) && $thismonth == gmdate('m', time()+(get_option('gmt_offset') * 3600)) && $thisyear == gmdate('Y', time()+(get_option('gmt_offset') * 3600)) )
			echo '<td style="font-weight:bold;">';
		else
			echo '<td>';

		if($onclick == 1){
			$onclick1 = 'onclick="fcal_set_date('.$day.','.($thismonth-1).','.$thisyear.')"';
		}

        // any posts on that day?
		if ( in_array($day, $daywithpost) ) {
            //Outputs the Density Thermometer along with the day...
			echo '<span style="padding:5px;background-color:'.($ak_posts_for_day[$day]<=Count($thermo) ? $thermo[$ak_posts_for_day[$day]-1] : $thermo[Count($thermo)-1]).';" title="'.$ak_titles_for_day[$day].' '.$onclick1.'">'.$day.'</span>';

		} else {
			echo '<span '.$onclick1.' >'.$day.'</span>';
        }
		echo '</td>';

		if ( 6 == calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins) )
			$newrow = true;
	}

	$pad = 7 - calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins);
	if ( $pad != 0 && $pad != 7 )
		echo "\n\t\t".'<td class="pad" colspan="'.$pad.'">&nbsp;</td>';

	echo "\n\t</tr>\n\t</tbody>\n\t</table>";
}

 
