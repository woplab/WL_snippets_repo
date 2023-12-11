<?php

use Dompdf\Options;
use Dompdf\Dompdf;

if ( ! defined( 'THEME_API_KEY' ) ) {
	define( 'THEME_API_KEY', '2hdshHD762ghgHSFHjhjssda6' );
}

add_action( 'rest_api_init', function () {
	$namespace = 'THEME/v1';

	$rout = '/pdf/';

	$rout_params = [
		'methods'             => "POST",
		'callback'            => 'create_pdf',
		'permission_callback' => 'permission_callback'
	];

	register_rest_route( $namespace, $rout, $rout_params );
} );
/**
 * Checks if the request has a valid token.
 *
 * @param WP_REST_Request $request The request object.
 *
 * @return bool Returns true if the request has a valid token, otherwise false.
 * @throws None
 */
function permission_callback( WP_REST_Request $request ) {
	if ( ! empty( $request->get_header( 'Token' ) ) && password_verify( THEME_API_KEY, $request->get_header( 'Token' ) ) ) {
		return true;
	}

	return false;
}

/**
 * Generates a PDF from HTML content.
 *
 * @param WP_REST_Request $request The REST request object.
 *
 * @return array The status and PDF content.
 * @throws None
 */
function create_pdf( WP_REST_Request $request ) {
	include_once __DIR__ . '/dompdf/autoload.inc.php';

	$params = $request->get_json_params();
	if ( $request->get_method() == 'POST' && isset( $params['html'] ) ) {

		$html = $params['html'];

		$options = new Options();
		$options->set( 'defaultMediaType', 'all' );
		$options->set( 'isFontSubsettingEnabled', true );
		$options->set( 'isRemoteEnabled', true );
		$dompdf = new Dompdf();

		$dompdf->loadHtml( $html );
		$dompdf->setPaper( 'A4', 'portrait' );
		$dompdf->render();
		$pdf = $dompdf->output();

		if ( ! empty( $pdf ) ) {
			return [
				'status' => 'ok',
				'pdf'    => base64_encode( $pdf )
			];
		}
	}

	return [ 'status' => 'error' ];
}
