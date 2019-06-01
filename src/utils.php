<?php

namespace Osds\Api\Utils;

function underscoreToCamelCase($input)
{
    return str_replace(' ', '', ucwords(str_replace('_', ' ', $input)));
}

function camelCaseToUnderscore($input)
{
    preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
    $ret = $matches[0];
    foreach ($ret as &$match) {
        $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
    }
    return implode('_', $ret);
}
