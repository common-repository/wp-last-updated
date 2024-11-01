<?php
/*
Plugin Name: WordPress Last Updated Plugin
Plugin URI: http://www.epicplugins.com
Description: Allows you to add a "Website last updated [date]" to your website
Version: 0.1
Author: mikemayhem3030
Author URI: http://epicplugins.com/
*/


function WPlastupdated( $atts ) {
	extract( shortcode_atts( array(
		'text' => '',
	), $atts ) );
	
	global $wp_query,$paged,$post,$wp,$wpdb;
    ob_start();
    $content = null;
	
	$args = array(
		'post_type' => 'any',
		'posts_per_page' => 1,
		'orderby' => 'modified',
		'order' => 'DESC'
	);

	$the_query = new WP_Query( $args );

// The Loop
	while ( $the_query->have_posts() ) :
		$the_query->the_post();
		?>
		<span class = 'epic-last-mod'><?php echo $text; ?><?php the_modified_date(); ?> at <?php the_modified_time(); ?></span>
		
	<?php
	endwhile;

/* Restore original Post Data 
 * NB: Because we are using new WP_Query we aren't stomping on the 
 * original $wp_query and it does not need to be reset.
*/
	wp_reset_postdata();



    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}
add_shortcode("WPlastupdated", "WPlastupdated");


#} Widgets
class epic_lastupdated_widget extends WP_Widget {
    function epic_lastupdated_widget() {
        parent::WP_Widget(false, $name = 'Epic Last Updated Widget', array(
            'description' => 'Display when your site was last updated.'
        ));
    }
    function widget($args, $instance) {
    	 global $wp_query,$paged,$post,$wp,$wpdb;
    	 $wpdb->dil_ip   = $wpdb->prefix . 'dil_ip';
	
        extract($args);$mode = (is_numeric($instance['mode']) ? (int)$instance['mode'] : 5);if (!empty($instance['target'])) $targetStr = ' target="'.$instance['target'].'"'; else $targetStr = '';
		if (!empty($instance['title'])) $title = $instance['title']; else $title = '';		

		
		$title = apply_filters( 'widget_title', $instance['item'] );
		?>
	    <h2 class = "widgettitle"><?php echo $title;?></h2>		
		<?php
		echo $before_widget;

	    echo do_shortcode('[WPlastupdated]');
		
		echo $after_widget;
		
    }
    function update($new_instance, $old_instance) {
        return $new_instance;
    }
    function form($instance) {
		if (isset($instance['item'])) $item = esc_attr($instance['item']); else $item = '';
		?>
        	

        	<p>

				
                <label for="<?php echo $this->get_field_id('item'); ?>">
                    Title:
                </label>
                <input id="<?php echo $this->get_field_id('item'); ?>" class="widefat" name="<?php echo $this->get_field_name('item'); ?>" value = "<?php echo $item; ?>"  />
                
                
            </p>
        	<?php
		
    }
}

add_action('widgets_init', 'epic_WPlastupdated_states_widget');
function epic_WPlastupdated_states_widget() {
    register_widget('epic_lastupdated_widget');
}