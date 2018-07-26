<?php
use ArtOfWP\WP\Testing\Exceptions\WPAjaxDieContinueException;
use ArtOfWP\WP\Testing\Exceptions\WPAjaxDieStopException;
/**
 * Class WC_Ajax.
 * @package WooCommerce\Tests\Ajax
 */
class WC_Tests_Ajax extends WC_Unit_Test_Case {

	/**
	 * Holds the last ajax response
	 *
	 * @var string
	 */
	protected $_last_ajax_response = '';

	/**
	 * Saved error reporting level.
	 *
	 * @var integer
	 */
	protected $_error_level = 0;

	/**
	 * Setup specific for wc_ajax requests
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		// Handle ajax die calls
		add_filter( 'wp_die_ajax_handler', array( $this, 'get_ajax_die_handler' ), 1, 1 );
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}
		set_current_screen( 'ajax' );
		add_action( 'clear_auth_cookie', array( $this, 'logout' ) );
		$this->_error_level = error_reporting();
		error_reporting( $this->_error_level & ~E_WARNING );
	}

	/**
	 * Return the die handler for ajax requests
	 *
	 * @return void
	 */
	public function get_ajax_die_handler() {
		return array( $this, 'ajax_die_handler' );
	}

	/**
	 * Handle ajax die calls
	 *
	 * @param string $message
	 * @return void
	 */
	public function ajax_die_handler( $message ) {
		$this->_last_ajax_response .= ob_get_clean();
		if ( '' !== $this->_last_ajax_response ) {
			throw new Exception( $message );
		}
	}

	/**
	 * Make sure we test this with an unauthenticated user.
	 *
	 * @return void
	 */
	public function logout() {
		unset( $GLOBALS['current_user'] );
		$cookies = array( AUTH_COOKIE, SECURE_AUTH_COOKIE, LOGGED_IN_COOKIE, USER_COOKIE, PASS_COOKIE );
		foreach ( $cookies as $c ) {
			unset( $_COOKIE[ $c ] );
		}
	}

	/**
	 * Make calls to ajax actions
	 *
	 * @param [type] $action
	 * @return void
	 */
	protected function _wc_ajax_request( $action ) {
		// Start output buffering
		ini_set( 'implicit_flush', false );
		ob_start();
		// Build the request
		$_POST['action'] = $action;
		$_GET['action']  = $action;
		$_REQUEST        = array_merge( $_POST, $_GET ); //phpcs:ignore
		// Call the hooks
		do_action( 'admin_init' );
		do_action( 'wc_ajax_' . $_REQUEST['action'], null ); //phpcs:ignore
		// Save the output
		$buffer = ob_get_clean();
		if ( ! empty( $buffer ) ) {
			$this->_last_ajax_response = $buffer;
		}
	}

	/**
	 * Test init function
	 */
	public function test_init() {
		$this->assertEquals( 0, has_action( 'init', array( 'WC_AJAX', 'define_ajax' ) ) );
		$this->assertEquals( 0, has_action( 'template_redirect', array( 'WC_AJAX', 'do_wc_ajax' ) ) );
	}

	/**
	 * Test WC_AJAX::get_endpoint
	 *
	 * @return void
	 */
	public function test_get_endpoint() {
		$this->assertEquals( '/?wc-ajax', WC_AJAX::get_endpoint() );
	}

	/**
	 * Test the get_refreshed_fragments ajax request
	 *
	 * @return void
	 */
	public function test_get_refreshed_fragments() {
		try {
			$this->_wc_ajax_request( 'get_refreshed_fragments' );
		} catch ( Exception $e ) { //phpcs:ignore
			// We expected this, do nothing.
		}
		$this->assertEquals( 1, did_action( 'wc_ajax_get_refreshed_fragments' ) );
	}

	/**
	 * Clean up after these tests.
	 *
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();
		$_POST = array();
		$_GET = array();
		remove_filter( 'wp_die_ajax_handler', array( $this, 'get_ajax_die_handler' ), 1 );
		remove_action( 'clear_auth_cookie', array( $this, 'logout' ) );
		error_reporting( $this->_error_level );
		set_current_screen( 'front' );
	}
}
