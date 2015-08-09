<?php
/**
 * Base class for formatting PHP WordPress objects like REST API responses.
 *
 * @package   shelob9\rest_like\format
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace shelob9\rest_like\format;


use shelob9\rest_like\rest_like;

/**
 * Class post
 *
 * @package shelob9\rest_like\format
 */
class post extends rest_like {


	protected static $clean = array(
		'content',
		'title',
		'excerpt'
	);

	/**
	 * Convert post objects to formatted objects.
	 * @param array $data Array of post objects. You can pass get_posts() results here
	 * @param bool $return_json Optional. Whether to return as JSON, the default, or not.
	 *
	 * @return array
	 */
	public static function get_data( $data, $return_json = true ) {
		$posts = $data;
		$data = array();
		$request = new \WP_REST_Request();
		$request['context'] = 'view';
		foreach ( $posts as $post ) {
			if ( ! is_object( $post ) ) {
				if( 0 < absint( $post ) ) {
					$post = get_post( $post );
				}else{
					continue;
				}

			}

			if ( ! is_a( $post, '\\WP_Post' ) ) {
				continue;
			}

			$controller = new \WP_REST_Posts_Controller( $post->post_type );
			$data[] = self::prepare_data( $controller->prepare_item_for_response( $post, $request ), $return_json );

		}

		return $data;
	}


}
