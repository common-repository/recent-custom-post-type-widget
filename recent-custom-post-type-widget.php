<?php /*
Plugin Name: Recent custom post type widget
Description: This plugin used to display recent post of selected post type
Author: Infoseek Team
Author URI: http://infoseeksoftwaresystems.com/
Version: 1.0
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/ 

defined( 'ABSPATH' ) || exit;
	
	/**
	* Check exist class
	*/
	if( !class_exists( 'Recent_Custom_Post_Type_Widget' ) ) {
	
	/*
	* Create a class extening pre made widget class
	*/
	class Recent_Custom_Post_Type_Widget extends WP_Widget {
	
		/*
		* Initialize constructor
		*/
		function Recent_Custom_Post_Type_Widget()
		{
			parent::WP_Widget(false, $name = __('Recent Custom Posts', 'wp_widget_plugin') );
		}
	
		/*
		* Render widget content on front end
		*/
		public function widget($args, $instance )
		{
			/*
			* configure all widget options
			*/
			$rcpt_title = apply_filters( 'widget_title', $instance[ 'recent_title' ] );
			$rcpt_show_date = apply_filters( 'recent_show_date', $instance[ 'recent_show_date' ] );
			$rcpt_post_type = ! empty( $instance['posttype'] ) ? $instance['posttype'] :'post';
			$post_types = get_post_types( array( 'public' => true ), 'objects' );
			if ( !$number = (int) $instance['number'] )
				$number = 10;
			else if ( $number < 1 )
				$number = 1;
			else if ( $number > 15 )
				$number = 15;
			
			if ($post_types){
				$r = new WP_Query(array(
					'post_type' => $rcpt_post_type,
					'posts_per_page' => $number,
					'no_found_rows' => true,
					'post_status' => 'publish',
					'ignore_sticky_posts' => true,
					));
				if ($r->have_posts()):
					echo $args['before_widget'];
						if ($rcpt_title)
						{
							echo '<h2 class="widget-title">' . $args['before_recent_title'] . $rcpt_title . $args['after_recent_title'] . '</h2>';
						} 
							echo '<ul>';
								while($r->have_posts()):$r->the_post();?>
									<li>
									<a href="<?php the_permalink() ?>"><?php get_the_title() ? the_title():the_ID(); ?></a>
									<?php if ($rcpt_show_date) :
										echo '<span class="post-date">'. get_the_date(). '</span>';
										endif; ?>
									</li>
								<?php	
								endwhile;
							echo '</ul>';
					echo $args['after_widget'];
					wp_reset_postdata();
				endif;
			}
		}
	
		/*
		* widget form creation
		*/
		public function form($instance)
		{

			$rcpt_title     = ! empty( $instance['recent_title'] ) ? $instance['recent_title'] : '';
			$rcpt_show_date = isset( $instance['recent_show_date'] ) ? $instance['recent_show_date'] : false;
			$rcpt_post_type = isset( $instance['posttype'] ) ? $instance['posttype']: 'post';
			
			if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
				$number = 5;
			
				echo '<p><label for="' . $this->get_field_id('recent_title') . '">Title:</label>';
				echo '<input type="text" id="' . $this->get_field_id('recent_title') .'" name="' . $this->get_field_name( 'recent_title') .'" value="' . esc_attr( $rcpt_title ) . '" style="width: 100%;"/></p>';
				
				$post_types = get_post_types(array('public' =>true), 'objects');
				
				echo '<p><label>Post Type:</label>';
				printf('<select class="widefat" id="%1$s" name="%3$s">',
				$this->get_field_id( 'posttype' ),
				__( 'Post Type:', 'custom-post-type-widgets' ),
				$this->get_field_name( 'posttype' )
				);
				
					foreach($post_types as $post_type => $value){
						
						if ('attachment' === $post_type){
							continue;
						}
						printf('<option value="%s"%s>%s</option>',
						esc_attr($post_type),
						selected($post_type, $rcpt_post_type, false),
						__($value->label, 'custom-post-type-widgets')
						);
					}
				echo'</select></p>';
				
				echo '<label for="' . $this->get_field_id('number') . '">' . _e('Number of posts to show:') . '</label>
					<input id="' . $this->get_field_id("number") .'" class="tiny-text" name="' .$this->get_field_name("number") .'" type="number" value="' .$number . '" min="1" step="1" />';?>
					
				<p><input class="checkbox" type="checkbox" <?php checked( $rcpt_show_date );?> id="<?php echo $this->get_field_id( 'recent_show_date' ); ?>" name="<?php echo $this->get_field_name( 'recent_show_date' ); ?>" /><label for="<?php echo $this->get_field_id( 'recent_show_date' ); ?>"><?php  _e( 'Display post date?');?></label></p>
				<?php
		}

		/*
		* Update widget data
		*/
		public function update($new_instance, $old_instance){
			$instance = $old_instance;
			$instance['recent_title'] = strip_tags($new_instance['recent_title']);
			$instance['recent_show_date'] = (bool) $new_instance['recent_show_date'];
			$instance['posttype'] = strip_tags($new_instance['posttype']);
			$instance['number'] = (int) $new_instance['number'];
			return $instance;
		}
		
	}//class end
	
	// register widget
	add_action('widgets_init', create_function('', 'return register_widget("Recent_Custom_Post_Type_Widget");'));
}