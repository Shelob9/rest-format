<?php
/**
 * Base class for formatting PHP WordPress objects like REST API responses.
 *
 * @package   shelob9\rest_like
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace shelob9\rest_like;

/**
 * Class rest_like
 *
 * @package shelob9\rest_like
 */
abstract class rest_like {

	/**
	 * Array of keys in response array requiring additional formatting
	 *
	 * @var array
	 */
	protected static $clean = array();

	/**
	 * @var \WP_REST_Server
	 */
	private static $rest_server;

	/**
	 * Get an instance of WP_REST_Server class
	 *
	 * @return \WP_REST_Server
	 */
	protected static  function get_server() {

		if ( ! is_a( self::$rest_server, '\\WP_REST_Server' ) ) {
			self::$rest_server = new \WP_REST_Server();
		}

		return self::$rest_server;
	}

	/**
	 * Must override in sub class!
	 */
	protected static function get_data( $data, $return_json ) {
		new \WP_Error( '_doing_it_wrong' );
		return self::prepare_data( $data, $return_json );
	}

	/**
	 * Format data using REST API.
	 *
	 * @param array $data Data to format
	 * @param bool $return_json Whether to return as an array or JSON.
	 *
	 * @return array|JSON|string
	 */
	protected  static  function prepare_data( $data, $return_json ) {
		$server = self::get_server();
		$data = $server->response_to_data( $data, true );
		if ( ! empty( self::$clean ) ) {
			foreach ( self::$clean as $key ) {
				if ( isset( $data[ $key ]['rendered'] ) ) {
					$data[ $key ]['rendered'] = self::strip_script( $data[ $key ]['rendered'] );
				}
			}
		}

		if ( ! $return_json ) {
			$data = json_decode( json_encode( $data ), true );
		}else{
			$data = wp_json_encode( $data );
		}

		return $data;

	}

	/**
	 * Remove script tags from HTML
	 *
	 * @param $html
	 *
	 * @return string
	 */
	protected static function strip_script( $html ) {
		return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
	}

}
