<?php namespace DirectoryCrawler\Controllers;

use DirectoryCrawler\Models\Directory;

Abstract class BaseController {
	protected $model;
    protected $crawlerClasses = array(
        'B99.co.uk'=>'B99CrawlerController',
        'Independent Shops'=>'IndependentShopsCrawlerController'
    );

    public function invoke(callable $function)
    {
        call_user_func( $function );
    }

    public function getDirectories()
    {
        $dir = new Directory();
        return $dir->getDirectories();
    }

    public function getCrawlerControllerClassName($directory_name)
    {
        if (isset($this->crawlerClasses[$directory_name])) {
            return $this->crawlerClasses[$directory_name];
        } else {
            return null;
        }
    }

    public function getDirectoryNames()
    {
        $dir = new Directory();
        return $dir->getDirectoryNames();
    }
}