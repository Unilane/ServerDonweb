<?php

namespace Madkting;

use GuzzleHttp\Client;

function reload_static_data()
{
    manifest(null, true);
    fields(true);
}

/**
 * Retrieves data for a service from the SDK's service manifest file.
 *
 * Manifest data is stored statically, so it does not need to be loaded more
 * than once per process. The JSON data is also cached in opcache.
 *
 * @param string $service Case-insensitive namespace or endpoint prefix of the
 *                        service for which you are retrieving manifest data.
 *
 * @return array
 * @throws \InvalidArgumentException if the service is not supported.
 */
function manifest($service = null, $reload = false)
{
    // Load the manifest and create aliases for lowercased namespaces
    static $manifest = array();
    static $aliases = array();
    if (empty($manifest) || $reload == true) {
        $manifest = load_compiled_json(__DIR__ . '/data/manifest.json');
        foreach ($manifest as $endpoint => $info) {
            $alias = strtolower($info['namespace']);
            if ($alias !== $endpoint) {
                $aliases[$alias] = $endpoint;
            }
        }
    }

    // If no service specified, then return the whole manifest.
    if ($service === null) {
        return $manifest;
    }

    // Look up the service's info in the manifest data.
    $service = strtolower($service);
    if (isset($manifest[$service])) {
        #return $manifest[$service] + array('endpoint' => $service);
        return $manifest[$service];
    } elseif (isset($aliases[$service])) {
        return manifest($aliases[$service]);
    } else {
        throw new \InvalidArgumentException(
            "The service \"{$service}\" is not provided by the Madkting SDK for PHP."
        );
    }
}

/**
 * Loads a compiled JSON file from a PHP file.
 *
 * If the JSON file has not been cached to disk as a PHP file, it will be loaded
 * from the JSON source file and returned.
 *
 * @param string $path Path to the JSON file on disk
 *
 * @return mixed Returns the JSON decoded data. Note that JSON objects are
 *     decoded as associative arrays.
 */
function load_compiled_json($path)
{
    if ($compiled = @include("$path.php")) {
        return $compiled;
    }

    if (!file_exists($path)) {
        throw new \InvalidArgumentException(
            sprintf("File not found: %s", $path)
        );
    }

    return json_decode(file_get_contents($path), true);
}

/**
 *
 * @param string $name
 * @return array
 * @throws \InvalidArgumentException if name is nor supported
 */
function statuses($name = null)
{
    $statuses = array();
    $statuses = load_compiled_json(__DIR__ . '/data/status.json');

    if ($name === null) {
        return $statuses;
    }

    // Look up the service's info in the manifest data.
    $name = strtolower($name);
    if (isset($statuses[$name])) {
        return $statuses[$name];
    } else {
        throw new \InvalidArgumentException(
            " \"{$name}\" is not provided by the Madkting SDK for PHP."
        );
    }
}

/**
 *
 * @return array
 */
function fields($reload = false)
{
    static $fields = array();
    if (empty($fields) || $reload) {
        //Get fields by http petition
        $client = new Client();

        $inlineJson = $client->request('GET', 'https://software.madkting.com/api/sales/fields', [
            'headers' => [
                'Accept-Language' => 'es',
            ]
        ]);
        $statusCode = $inlineJson->getStatusCode();

        if ($statusCode == 200) {
            $fields = json_decode($inlineJson->getBody()->getContents(), true);
        } else {
            $fields = load_compiled_json(__DIR__ . '/data/fields.json');
        }
    }
    return $fields;
}

/**
 *
 * @return array
 */
function marketplaces($reload = false)
{
    static $marketplaces = array();
    if (empty($marketplaces) || $reload) {
        $marketplaces = load_compiled_json(__DIR__ . '/data/marketplaces.json');
    }
    return $marketplaces;
}

/**
 *
 * @return array
 */
function categories($reload = false)
{
    static $categories = array();
    if (empty($categories) || $reload) {
        $categories = load_compiled_json(__DIR__ . '/data/categories.json');
    }
    return $categories;
}