<?php
function cc_whmcs_sidebar_init() {
	add_action('widgets_init', create_function('', 'return register_widget("cc_whmcs_sidebar_main");'));
	add_action('widgets_init', create_function('', 'return register_widget("cc_whmcs_sidebarAcInf_main");'));
	add_action('widgets_init', create_function('', 'return register_widget("cc_whmcs_sidebarAcSta_main");'));
	add_action('widgets_init', create_function('', 'return register_widget("cc_whmcs_topNav_main");'));
	add_action('widgets_init', create_function('', 'return register_widget("cc_whmcs_welcomebox_main");'));
	add_action('widgets_init', create_function('', 'return register_widget("cc_whmcs_sidebarNav_main");'));
}

class cc_whmcs_sidebar_main extends WP_Widget {
	/** constructor */
	function cc_whmcs_sidebar_main() {
		parent::WP_Widget(false, $name = 'WHMCS Main');
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $cc_whmcs_bridge_content;
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( !$title ) $title='WHMCS main';
		echo $before_title . $title . $after_title;
		echo $cc_whmcs_bridge_content['sidebarNav'];
		echo $cc_whmcs_bridge_content['sidebarAcInf'];
		echo $cc_whmcs_bridge_content['sidebarAcSta'];
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr($instance['title']);
		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
		echo '</p>';
	}
}

class cc_whmcs_sidebarAcInf_main extends WP_Widget {
	/** constructor */
	function cc_whmcs_sidebarAcInf_main() {
		parent::WP_Widget(false, $name = 'WHMCS Account Info');
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $cc_whmcs_bridge_content;
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( !$title ) $title=$cc_whmcs_bridge_content['mode'][1];
		echo $before_title . $title . $after_title;
		echo $cc_whmcs_bridge_content['sidebarAcInf'];
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr($instance['title']);
		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
		echo '</p>';
	}
}

class cc_whmcs_sidebarAcSta_main extends WP_Widget {
	/** constructor */
	function cc_whmcs_sidebarAcSta_main() {
		parent::WP_Widget(false, $name = 'WHMCS Account Stats');
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $cc_whmcs_bridge_content;
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( !$title ) $title=$cc_whmcs_bridge_content['mode'][2];
		echo $before_title . $title . $after_title;
		echo '<!--start-->';
		echo $cc_whmcs_bridge_content['sidebarAcSta'];
		echo '<!--end-->';
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr($instance['title']);
		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
		echo '</p>';
	}
}

class cc_whmcs_topNav_main extends WP_Widget {
	/** constructor */
	function cc_whmcs_topNav_main() {
		parent::WP_Widget(false, $name = 'WHMCS Top Nav');
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $cc_whmcs_bridge_content;
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( !$title ) $title=$cc_whmcs_bridge_content['mode'][0];
		echo $before_title . $title . $after_title;
		echo $cc_whmcs_bridge_content['topNav'];
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr($instance['title']);
		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
		echo '</p>';
	}
}

class cc_whmcs_welcomebox_main extends WP_Widget {
	/** constructor */
	function cc_whmcs_welcomebox_main() {
		parent::WP_Widget(false, $name = 'WHMCS Welcome Box');
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $cc_whmcs_bridge_content;
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		echo $cc_whmcs_bridge_content['welcomebox'];
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr($instance['title']);
		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
		echo '</p>';
	}
}


class cc_whmcs_sidebarNav_main extends WP_Widget {
	/** constructor */
	function cc_whmcs_sidebarNav_main() {
		parent::WP_Widget(false, $name = 'WHMCS Quick Navigation');
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $cc_whmcs_bridge_content;
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( !$title ) $title=$cc_whmcs_bridge_content['mode'][0];
		echo $before_title . $title . $after_title;
		echo $cc_whmcs_bridge_content['sidebarNav'];
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr($instance['title']);
		echo '<p>';
		echo '<label for="'.$this->get_field_id('title').'"'._e('Title:').'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>';
		echo '</p>';
	}
}
