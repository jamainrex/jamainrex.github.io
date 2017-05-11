<?php
namespace DirectoryCrawler\Config;

function getDirectoryCrawlerConfig( $key )
{
    $config = [
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'db' => 'directorycrawlerdb'
    ];

    return isset( $config[$key] ) ? $config[$key] : false;
}