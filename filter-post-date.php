<?php
/*
Plugin Name: Filer Post By Date
Plugin URI: https://itsmeit.co/
Description: Filter posts or Media by day or month in Admin management page.
Version: 1.0.0
Author: itsmeit.co
Author URI: https://itsmeit.co/
Network: true
Text Domain: filter-post-date

Copyright 2023 itsmeit.co (email: buivanloi.2010@gmail.com)
*/

class FilterPostByDate {

    function __construct() {
        add_filter('months_dropdown_results', '__return_empty_array');
        add_action('admin_enqueue_scripts', array($this, 'jqueryui'));
        add_action('restrict_manage_posts', array($this, 'form'));
        add_action('pre_get_posts', array($this, 'filterquery'));
    }

    function jqueryui() {
        wp_enqueue_style('jquery-ui', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css');
        wp_enqueue_script('jquery-ui-datepicker');
    }

    function form(){

        $from = (isset($_GET['itsmeitDateFrom']) && $_GET['itsmeitDateFrom']) ? $_GET['itsmeitDateFrom'] : '';
        $to = (isset($_GET['itsmeitDateTo']) && $_GET['itsmeitDateTo']) ? $_GET['itsmeitDateTo'] : '';

        echo '<style>
		input[name="itsmeitDateFrom"], input[name="itsmeitDateTo"]{
			line-height: 28px;
			height: 28px;
			margin: 0;
			width:125px;
		}
		</style>
		
		<input type="text" name="itsmeitDateFrom" placeholder="Date From" value="' . esc_attr($from) . '" />
		<input type="text" name="itsmeitDateTo" placeholder="Date To" value="' . esc_attr($to) . '" />
	
		<script>
		jQuery( function($) {
			var from = $(\'input[name="itsmeitDateFrom"]\'),
			    to = $(\'input[name="itsmeitDateTo"]\');

			$( \'input[name="itsmeitDateFrom"], input[name="itsmeitDateTo"]\' ).datepicker( {dateFormat : "yy-mm-dd"} );
    			from.on( \'change\', function() {
				to.datepicker( \'option\', \'minDate\', from.val() );
			});
				
			to.on( \'change\', function() {
				from.datepicker( \'option\', \'maxDate\', to.val() );
			});
			
		});
		</script>';

    }

    function filterquery($admin_query){
        global $pagenow;
        if (
            is_admin()
            && $admin_query->is_main_query()
            && in_array($pagenow, array('edit.php', 'upload.php'))
            && (!empty($_GET['itsmeitDateFrom']) || !empty($_GET['itsmeitDateTo']))
        ) {
            $admin_query->set(
                'date_query',
                array(
                    'after' => sanitize_text_field($_GET['itsmeitDateFrom']), // any strtotime()-acceptable format!
                    'before' => sanitize_text_field($_GET['itsmeitDateTo']),
                    'inclusive' => true,
                    'column' => 'post_date'
                )
            );

        }
        return $admin_query;
    }
}

new FilterPostByDate();
