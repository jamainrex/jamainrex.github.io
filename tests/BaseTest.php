<?php
/**
 * Base Test
 */

// For PSR-0
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;
use DirectoryCrawler\Controllers\DomCrawlerController; // Controller

// Model
use DirectoryCrawler\Models\CrawlerLink;
use DirectoryCrawler\Models\Category;
use DirectoryCrawler\Models\Locations;
use DirectoryCrawler\Models\Entry;

use DirectoryCrawler\Scrapers\Categories; // Scraper

use DirectoryCrawler\Helpers\Filter; // Filters

function print_results( $res )
{
    echo '<pre>'.print_r($res,true).'</pre>';
}

// Route to Controller
$target_link = 'http://b99.co.uk';
$selector = 'a'; // Fetch all Links
function assertDomCrawler( $target_link, $selector, $params = array() )
{
    $domCrawler = new DomCrawlerController();
    return $domCrawler->crawlTargetLink( $target_link, $selector, $params );
}
//$crawledLinks = assertDomCrawler( $target_link, $selector );
//print_results( $crawledLinks );


// Accessing Model - Get all Crawled Links
function assertCrawlerLinkModel()
{
    $model = new CrawlerLink();
    return $model->getAll();
}
//$links = assertCrawlerLinkModel();
//print_results( $links );


// Pre-defined Scrapers
// http:\\.<category_name>.b99.co.uk Category URL Format from b99.co.uk site
function assertScrapeCategories()
{
    $categoryScraper = new Categories();
    $categoryScraper->addFilter('category',"http:\/\/.(\w|\-)*.b99.co.uk");
    $crawledDatas = $categoryScraper->getDataByTargetLink( "http://b99.co.uk" );
    $scrapedDatas = $categoryScraper->invoke( $crawledDatas, true ); // TRUE if to be inserted to Category Table

    return $scrapedDatas;
}

//$scrapeCategories = assertScrapeCategories();
//print_results( $scrapeCategories['results'] ); // Array format result
//print_results( $scrapeCategories['insert_values'] ); // Array SQL format result

function assertCustomScrape( $target_link, $selector, $parameter )
{
    $crawler = new DomCrawler( $target_link, $selector, $parameter );
    $filters = new Filter();

    $filters->addFilter( 'Letters', "^[A-Z]$" ); // Regex Only Alphabet(Upper-case) only

    $crawler->htmlDomLoadFromCurl();
    $results = array();

    foreach( $crawler->htmlDomFind() as $link )
    {

        $name =  $crawler->cleanText( $link->innertext() );
        $tag = $crawler->getTag( $name );
        $_results = [
            'name' => $crawler->cleanText( $name ),
            'href' => $link->href,
            'tag' => $tag,
            'text' => $link->text(),
            'innertext' => $link->innertext(),
            'outertext' => $link->outertext()
        ];

        $results[] = $_results;
    }

    $ScrapeAlphabetNames = $filters->applyFilter( 'Letters', $results, 'name' );

    return [ 'ScrapedData' => $ScrapeAlphabetNames, 'RawResults' => $results ];

}

$ScrapedAlphaLinks = assertCustomScrape( $target_link, $selector, array() );
print_results( $ScrapedAlphaLinks['ScrapedData'] );
//print_results( $ScrapedAlphaLinks['RawResults'] );