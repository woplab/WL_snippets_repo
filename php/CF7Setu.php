<?php

class CF7Setup {

	/**
	 * @var null|CF7Setup
	 */
	protected static ?CF7Setup $instance = null;

	/**
	 * Return an instance of this class.
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {

	}


	static function get_form_emails($form_name) {
		if (empty($form_name)) {
			return false;
		}

		$form_id = self::get_form_by_title($form_name);

		if (empty($form_id) || !is_numeric($form_id)) {
			return false;
		}

		$form_data = self::get_emails($form_id);

		$emails = array();

		foreach ($form_data as $value) {
			$data = unserialize($value['data']);
			$emails[] = $data['email'] ?? '';
		}

		return array_unique(array_filter($emails));
	}

	static function get_form_by_title($searched_title) {

		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'wpcf7_contact_form' AND post_title = %s",
			$searched_title
		);

		$form_id = $wpdb->get_var($query);

		return intval($form_id);
	}

	static function get_emails($form_id) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'cf7db';

		$query = $wpdb->prepare( "SELECT data FROM $table_name WHERE form = %d", $form_id );


		$result = $wpdb->get_results( $query, ARRAY_A );

		return $result;
	}

	static function get_form_shortcode($form_id, $class = '') {
		if (empty($form_id)) {
			return '';
		}
		$html_class = $class ? "html_class='$class'" : '';
		$title = get_the_title($form_id);

		$id = get_post_meta($form_id, '_hash', true);
		$id = substr($id, 0, 7);

		$shortcode_template = '[contact-form-7 id="%s" title="%s" %s]';

		return sprintf($shortcode_template, $id, $title, $html_class);

	}

}

CF7Setup::instance();