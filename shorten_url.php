<?php
/**
 * PrivateBin YOURLS Seamless Integration Script
 * 
 * This script facilitates the integration of YOURLS (Your Own URL Shortener) with a PrivateBin instance,
 * enabling users to generate shortened URLs for their pastes directly within the PrivateBin interface.
 *
 * Setup Instructions:
 * 1. Save this file as `shorten_url.php` in the root directory of your PrivateBin instance.
 * 2. Update the `$yourls_api_url` and `$yourls_signature` variables below with your YOURLS API endpoint and signature token respectively.
 * 3. Open your PrivateBin configuration file (`cfg/conf.php`) and set the `urlshortener` option as follows:
 *    urlshortener = "${basepath}shorten_url.php?url="
 *
 * Usage:
 * Once set up, users can click the "Shorten URL" button in the PrivateBin interface to generate a shortened URL
 * via your YOURLS instance. The shortened URL will then be displayed for easy sharing.
 *
 * Note:
 * Ensure that your YOURLS instance is correctly configured and accessible from your PrivateBin server.
 *
 * @author Andy Saputra
 * @license MIT License
 * @version 1.0
 * @tested PrivateBin 1.6.0, YOURLS 1.9.2
 * @link https://saputra.org Official Website
 * @link https://github.com/ajsap/privatebin-yourls Official Repository
 * @link https://copas.me Website where this script is utilised for PrivateBin integration
 * @link https://cya.nz Website where this script is related for URL shortening via YOURLS
 *
 * Copyright (c) 2023 Andy Saputra
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

// Your YOURLS API endpoint and signature
$yourls_api_url = 'https://yourls-domain/yourls-api.php';
$yourls_signature = 'yourls-signature';

// Input validation
if (!isset($_GET['url']) || !filter_var($_GET['url'], FILTER_VALIDATE_URL)) {
    exit('Invalid URL');
}
$long_url = $_GET['url'];

// Get the user's IP address
$user_ip = $_SERVER['REMOTE_ADDR'];

// URL-encode the long URL so it can be included as a query parameter
$encoded_url = urlencode($long_url);

// Build the YOURLS API request URL
$api_request_url = "{$yourls_api_url}?signature={$yourls_signature}&action=shorturl&format=simple&url={$encoded_url}";

// Initialize cURL
$ch = curl_init($api_request_url);

// Set the cURL options to include the user's IP address in the HTTP headers
curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Forwarded-For: $user_ip"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Send the request to the YOURLS server and get the response
$response = curl_exec($ch);

// Error handling
if($response === false) {
    echo 'Curl error: ' . curl_error($ch);
} else {
    echo $response;
}

// Close the cURL handle
curl_close($ch);
?>
