<?php namespace DirectoryCrawler\Scrapers;

use DirectoryCrawler\Models\CrawlerLink;
use DirectoryCrawler\Helpers\Filter;

Abstract Class Scraper {

    protected $crawlerLinkModel;
    protected $model;
    protected $filters = array();

    public function __construct(){
        $this->crawlerLinkModel = new CrawlerLink();
        // Filter
        $this->filters = new Filter();
    }

    public function addFilter($type,$filter) {
        $this->filters[$type] = $filter;
    }

    public function Filters()
    {
        return $this->filters;
    }

    public function getCrawlerLinkModel()
    {
        return $this->crawlerLinkModel;
    }

    abstract public function invoke();
}
?>
