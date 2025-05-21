<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 *
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 */

namespace KlarnaPayment\Module\Api\Helper;

use KlarnaPayment\Module\Api\Exception\CouldNotHandleApiRequest;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ApiHelper
{
    /**
     * Replaces template parameters in the given url
     *
     * @param string $url The query string builder to replace the template parameters
     * @param array $parameters The parameters to replace in the url
     * @param bool $encode Should parameters be URL-encoded?
     *
     * @return string The processed url
     */
    public static function appendUrlWithTemplateParameters(string $url, array $parameters, bool $encode = true): string
    {
        foreach ($parameters as $key => $value) {
            if (is_null($value)) {
                $replaceValue = '';
            } elseif (is_array($value)) {
                $val = array_map('strval', $value);
                $val = $encode ? array_map('urlencode', $val) : $val;
                $replaceValue = implode('/', $val);
            } else {
                $val = (string) $value;
                $replaceValue = $encode ? urlencode($val) : $val;
            }

            $url = str_replace('{' . (string) $key . '}', $replaceValue, $url);
        }

        return $url;
    }

    /**
     * Appends the given set of parameters to the given query string
     *
     * @param string $queryBuilder The query url string to append the parameters
     * @param array $parameters The parameters to append
     */
    public static function appendUrlWithQueryParameters(string &$queryBuilder, array $parameters): void
    {
        // perform parameter validation
        if (is_null($queryBuilder) || !is_string($queryBuilder)) {
            throw new \InvalidArgumentException('Given value for parameter "queryBuilder" is invalid.');
        }
        if (is_null($parameters)) {
            return;
        }
        // does the query string already has parameters
        $hasParams = (strrpos($queryBuilder, '?') > 0);

        // if already has parameters, use the &amp; to append new parameters
        $queryBuilder .= (($hasParams) ? '&' : '?');

        // append parameters
        $queryBuilder .= http_build_query($parameters);
    }

    /**
     * Validates and processes the given Url
     *
     * @param string $url The given Url to process
     *
     * @return string Pre-processed Url as string
     */
    public static function cleanUrl(string $url): string
    {
        // perform parameter validation
        if (is_null($url) || !is_string($url)) {
            throw new \InvalidArgumentException('Invalid Url.');
        }
        // ensure that the urls are absolute
        $matchCount = preg_match('#^(https?://[^/]+)#', $url, $matches);
        if ($matchCount == 0) {
            throw new \InvalidArgumentException('Invalid Url format.');
        }
        // get the http protocol match
        $protocol = $matches[1];

        // remove redundant forward slashes
        $query = substr($url, strlen($protocol));
        $query = preg_replace('#//+#', '/', $query);

        // return process url
        return $protocol . $query;
    }

    /**
     * Merge headers
     *
     * Header names are compared using case-insensitive comparison. This method
     * preserves the original header name. If the $newHeaders overrides an existing
     * header, then the new header name (with its casing) is used.
     */
    public static function mergeHeaders(array $headers, array $newHeaders): array
    {
        $headerKeys = [];

        // Create a map of lower-cased-header-name to original-header-names
        foreach ($headers as $headerName => $val) {
            $headerKeys[\strtolower($headerName)] = $headerName;
        }

        // Override headers with new values
        foreach ($newHeaders as $headerName => $headerValue) {
            $lowerCasedName = \strtolower($headerName);
            if (isset($headerKeys[$lowerCasedName])) {
                unset($headers[$headerKeys[$lowerCasedName]]);
            }
            $headerKeys[$lowerCasedName] = $headerName;
            $headers[$headerName] = $headerValue;
        }

        return $headers;
    }

    /**
     * @throws CouldNotHandleApiRequest
     */
    public static function jsonEncode($data, int $flag = 0): string
    {
        if (!function_exists('json_encode')) {
            throw CouldNotHandleApiRequest::jsonExtensionNotAvailable();
        }

        return json_encode($data, $flag);
    }

    /**
     * @throws CouldNotHandleApiRequest
     */
    public static function jsonDecode(string $data, bool $associative = false, int $flag = 0)
    {
        if (!function_exists('json_decode')) {
            throw CouldNotHandleApiRequest::jsonExtensionNotAvailable();
        }

        return json_decode($data, $associative, 512, $flag);
    }
}
