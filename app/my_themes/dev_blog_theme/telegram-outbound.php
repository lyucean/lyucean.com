<?php
/**
 * Исходящие запросы к api.telegram.org: только cURL, без wp_remote_*.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @param mixed $value .
 * @return string
 */
function dev_blog_sanitize_telegram_http_proxy( $value ) {
    if ( ! is_string( $value ) ) {
        return '';
    }
    $value = trim( $value );
    return $value === '' ? '' : sanitize_text_field( $value );
}

/**
 * Прокси: опция telegram_http_proxy, иначе TELEGRAM_HTTP_PROXY.
 *
 * @return string
 */
function dev_blog_telegram_http_proxy_url() {
    $p = trim( (string) get_option( 'telegram_http_proxy', '' ) );
    if ( $p !== '' ) {
        return $p;
    }
    $env = getenv( 'TELEGRAM_HTTP_PROXY' );
    return ( false !== $env ) ? trim( (string) $env ) : '';
}

/**
 * POST на URL Bot API (application/x-www-form-urlencoded).
 *
 * @param string               $url         Полный URL, например https://api.telegram.org/botTOKEN/sendMessage .
 * @param array<string, mixed> $post_fields Поля формы.
 * @return array{http_code:int, body:string, curl_errno:int, curl_error:string}
 */
function dev_blog_telegram_curl_post( $url, array $post_fields ) {
    $result = array(
        'http_code'  => 0,
        'body'       => '',
        'curl_errno' => 0,
        'curl_error' => '',
    );

    if ( ! is_string( $url ) || $url === '' ) {
        $result['curl_error'] = 'Пустой URL';
        return $result;
    }

    if ( ! function_exists( 'curl_init' ) ) {
        $result['curl_error'] = 'Расширение cURL не загружено';
        return $result;
    }

    $proxy = dev_blog_telegram_http_proxy_url();

    $ch = curl_init( $url );
    if ( false === $ch ) {
        $result['curl_error'] = 'curl_init не удался';
        return $result;
    }

    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $post_fields, '', '&' ) );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/x-www-form-urlencoded' ) );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );

    if ( defined( 'CURLOPT_CONNECTTIMEOUT_MS' ) ) {
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT_MS, 45000 );
    } else {
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 45 );
    }
    if ( defined( 'CURLOPT_TIMEOUT_MS' ) ) {
        curl_setopt( $ch, CURLOPT_TIMEOUT_MS, 70000 );
    } else {
        curl_setopt( $ch, CURLOPT_TIMEOUT, 70 );
    }

    if ( $proxy !== '' ) {
        curl_setopt( $ch, CURLOPT_PROXY, $proxy );
        $socks = ( 0 === stripos( $proxy, 'socks5://' ) || 0 === stripos( $proxy, 'socks5h://' ) );
        if ( $socks && defined( 'CURLPROXY_SOCKS5_HOSTNAME' ) ) {
            curl_setopt( $ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME );
        } elseif ( $socks && defined( 'CURLPROXY_SOCKS5' ) ) {
            curl_setopt( $ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5 );
        } elseif ( defined( 'CURLPROXY_HTTP' ) ) {
            curl_setopt( $ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP );
        }
        curl_setopt( $ch, CURLOPT_HTTPPROXYTUNNEL, true );
    }

    if ( defined( 'ABSPATH' ) && defined( 'WPINC' ) ) {
        $ca = ABSPATH . WPINC . '/certificates/ca-bundle.crt';
        if ( is_readable( $ca ) ) {
            curl_setopt( $ch, CURLOPT_CAINFO, $ca );
        }
    }

    $body                 = curl_exec( $ch );
    $result['curl_errno'] = curl_errno( $ch );
    $result['curl_error'] = curl_error( $ch );
    $result['http_code']  = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );
    curl_close( $ch );

    $result['body'] = ( false === $body ) ? '' : (string) $body;

    return $result;
}
