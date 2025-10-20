<?php
// Helpers mínimos para JWT y para enviar tokens a microservicios
// Implementación simple, sin dependencias externas.

/**
 * Base64 url-safe encoding
 */
function base64url_encode($data)
{
	return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Base64 url-safe decoding
 */
function base64url_decode($data)
{
	$remainder = strlen($data) % 4;
	if ($remainder) {
		$padlen = 4 - $remainder;
		$data .= str_repeat('=', $padlen);
	}
	return base64_decode(strtr($data, '-_', '+/'));
}

/**
 * Genera un JWT simple (alg: HS256)
 * payload: array
 */
function jwt_encode(array $payload, $secret = JWT_SECRET, $expirySeconds = JWT_EXPIRY_SECONDS)
{
	$header = ['alg' => 'HS256', 'typ' => 'JWT'];
	$now = time();
	$payload['iat'] = $now;
	$payload['exp'] = $now + (int)$expirySeconds;

	$base64Header = base64url_encode(json_encode($header));
	$base64Payload = base64url_encode(json_encode($payload));

	$signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);
	$base64Signature = base64url_encode($signature);

	return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
}

/**
 * Decodifica y valida un JWT.
 * Retorna payload en caso de éxito, false en caso contrario.
 */
function jwt_decode($token, $secret = JWT_SECRET)
{
	$parts = explode('.', $token);
	if (count($parts) !== 3) return false;

	list($b64Header, $b64Payload, $b64Signature) = $parts;

	$headerJson = base64url_decode($b64Header);
	$payloadJson = base64url_decode($b64Payload);
	$signature = base64url_decode($b64Signature);

	if ($headerJson === false || $payloadJson === false || $signature === false) return false;

	$header = json_decode($headerJson, true);
	$payload = json_decode($payloadJson, true);
	if (!is_array($header) || !is_array($payload)) return false;

	// Verify alg
	if (!isset($header['alg']) || $header['alg'] !== 'HS256') return false;

	// Verify signature
	$expectedSig = hash_hmac('sha256', $b64Header . '.' . $b64Payload, $secret, true);
	if (!hash_equals($expectedSig, $signature)) return false;

	// Verify exp
	if (isset($payload['exp']) && time() > $payload['exp']) return false;

	return $payload;
}

/**
 * Verifica que la petición HTTP tenga un header Authorization Bearer válido.
 * Devuelve el payload o false.
 */
function jwt_validate_request()
{
	$headers = null;
	if (function_exists('getallheaders')) {
		$headers = getallheaders();
	} else {
		// Fallback para servidores que no soportan getallheaders
		$headers = [];
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
	}

	$auth = $headers['Authorization'] ?? ($headers['authorization'] ?? null);
	if (empty($auth)) return false;

	if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
		$token = trim($matches[1]);
		return jwt_decode($token);
	}
	return false;
}

/**
 * Realiza una petición HTTP simple (POST o GET) enviando el token JWT en Authorization header.
 * Retorna array con ['status'=>int, 'body'=>string].
 */
function http_request_with_jwt(string $url, string $method = 'GET', $data = null, $token = null)
{
	$opts = [
		'http' => [
			'method' => strtoupper($method),
			'header' => "Accept: application/json\r\n",
			'ignore_errors' => true,
		]
	];

	if (!empty($token)) {
		$opts['http']['header'] .= "Authorization: Bearer " . $token . "\r\n";
	}

	if ($data !== null) {
		$payload = is_string($data) ? $data : json_encode($data);
		$opts['http']['header'] .= "Content-Type: application/json\r\n";
		$opts['http']['header'] .= "Content-Length: " . strlen($payload) . "\r\n";
		$opts['http']['content'] = $payload;
	}

	$context = stream_context_create($opts);
	$result = @file_get_contents($url, false, $context);
	$status = 0;
	if (isset($http_response_header) && is_array($http_response_header)) {
		if (preg_match('#HTTP/[0-9\.]+\s+(\d+)#', $http_response_header[0], $m)) {
			$status = (int)$m[1];
		}
	}

	return ['status' => $status, 'body' => $result];
}

?>
