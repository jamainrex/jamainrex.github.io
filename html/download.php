<?php
set_time_limit(0);

require_once __DIR__ . '/../vendor/autoload.php';
// Crawler
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;
// Controller
use DirectoryCrawler\Controllers\DomCrawlerController;
use DirectoryCrawler\Controllers\B99CrawlerController;

// Model
use DirectoryCrawler\Models\CrawlerLink;
use DirectoryCrawler\Models\Category;
use DirectoryCrawler\Models\Locations;
use DirectoryCrawler\Models\Entry;

// Scraper
use DirectoryCrawler\Scrapers\Categories;

// Filters
use DirectoryCrawler\Helpers\Filter;

$crawler = new \DirectoryCrawler\Controllers\B99CrawlerController();
$crawl_id = filter_input(INPUT_GET, 'crawl_id');
header( 'Content-Type: text/csv' );
header( 'Content-Disposition: attachment;filename=export.csv' );
$result = $crawler->getAllCrawlData($crawl_id);
$header_row = true;
foreach ($result as $row) {
    if ($header_row) {
        echocsv( array_keys( $row ) );
        $header_row = false;
    }
    echocsv( $row );
}
function echocsv( $fields )
{
    $separator = '';
    foreach ( $fields as $field )
    {
        if ( preg_match( '/\\r|\\n|,|"/', $field ) )
        {
            $field = '"' . str_replace( '"', '""', $field ) . '"';
        }
        echo $separator . $field;
        $separator = ',';
    }
    echo "\r\n";
}
