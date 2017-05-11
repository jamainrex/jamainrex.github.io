<?php
set_time_limit(0);
/**
 * Base Test
 */

// For PSR-0
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

// Test Echo Result
function dc( $res )
{
    echo '<pre>'.print_r($res,true).'</pre>';
}

// Call the Controller
$b99CrawlerController = new B99CrawlerController();

if( isset( $_GET['get_categories'] ) ) {
    $cats = $b99CrawlerController->getCategories();
    dc($cats);
}

/**
 * Route to Controller
 */
// Get Letter links on Landing Page
if( isset( $_GET['landing_page_letter_links'] ) )
{
    $letterLinks = $b99CrawlerController->landingLetterLinks();
    dc( $letterLinks );
}

// Categories on Landing Page.
/**
 * 1.) Crawler that can save all the categories in b99.co.uk, by visiting the letters pages and saving the categories.
 */
if( isset( $_GET['get_all_categories'] ) )
{
    $crawledCategories = $b99CrawlerController->getCategoriesByLetterLink( "http://b99.co.uk/letter/a" ); // Example: Letter A link - See Controller function
    dc( $crawledCategories );
}

/**
 * 2.) Crawler that can visit all the locaations within the categories and save an entry for each business containing business name, website url and if present in the directory, email address, from b99.co.uk.
 */
if( isset( $_GET['generate_entries_by_locaations'] ) )
{
    $businessEntries = $b99CrawlerController->getBusinessesFromLocationsByCategory( "http://abattoir.b99.co.uk/" ); //See Controller function
    dc( $businessEntries );
}

if( isset( $_GET['scrape_business_entry'] ) )
{
    $bizEntry = $b99CrawlerController->scrapeBusinessEntry( "http://abattoir.b99.co.uk/bakewell/valerie-turner/" );
    dc( $bizEntry );
}

if( isset( $_GET['inserCrawlData'] ) )
{
    $data = array();
    $data[] = array( 'Item name' => 'Wishaw Abattoir Ltd',
        'Street address' => '185 Caledonian Road',
        'Postcode' => 'ML2 0HT',
        'Locaation or Suburb' => 'Wishaw (Lanarkshire)',
        'Country or region' => 'Lanarkshire',
        'Telephone(s)' => '01698 372 667',
        'Fax' => '01698 351 057',
        'Website' => '',
        'Email'=> '',
        'business type' => 'Abattoirs' );

    $crawlData = $b99CrawlerController->updateCrawlDataRecord( $data );
    dc( $crawlData );
}

if( isset( $_GET['getCrawlDataWithWebsites'] ) )
{

    $crawlData = $b99CrawlerController->scrapeCrawledData(1);
    
    // Add new emails.
    if( isset( $crawlData['business'] ) ){
        foreach( $crawlData['business'] as $bizEntry )
        {
            $b99CrawlerController->updateBusinessEmails( $bizEntry );     
        }    
    }
    
    dc( $crawlData );
}

if( isset( $_GET['scrapeBusinessEmailFromCrawledData'] ) )
{

    $business_website = 'http://www.papworthbutchers.co.uk';
    $scrapeEmails = $b99CrawlerController->scrapeForBusinessEmailAddress( $business_website );
    
    dc( $scrapeEmails );

    //dc( $scrapeEmails );
    
    //if ( filter_var( trim( 'INFO@ABP.COM' ), FILTER_VALIDATE_EMAIL)) echo 'valid';   
}

if( isset( $_GET['updateEntryEmailAddresses'] ) )
{
    $biz = $b99CrawlerController->scrapeForBusinessEmailAddress( "http://www.abp.com" );
    dc( $biz );
    //$updateEmails = $b99CrawlerController->updateBusinessEmails( $bizEntry );
    //updateBusinessEmails
}
