<?php
set_time_limit(0);
/**
 * Base Test
 */

// For PSR-0
require_once __DIR__ . '/../vendor/autoload.php';

// Call the Controller
$crawlerController = new \DirectoryCrawler\Controllers\IndependentShopsCrawlerController();

// Test Echo Result
function dc( $res )
{
    echo '<pre>'.print_r($res,true).'</pre>';
}

if( isset( $_GET['scrape_business_entry'] ) )
{
    $crawl_id = time();    
    //$bizEntry = $crawlerController->scrapeBusinessEntriesByPostcodeLetterPage( "http://www.independentshops.co.uk/in.php?Postcode=a" );
    $bizEntry = $crawlerController->getBusinessesFromLocationsByCategory("http://www.independentshops.co.uk/in.php?Postcode=a");
    
    // Insert Crawldata
    $crawlerController->updateCrawlDataRecord( $bizEntry['crawlBusinessData'], $crawl_id );
    dc( $bizEntry );
}

if( isset( $_GET['updateEntryEmailAddresses'] ) )
{
    $data_to_crawl = $crawlerController->scrapeCrawledDataById('1717');
    dc( $data_to_crawl );
    
    /*if (isset($data_to_crawl['business'])) {
                foreach( $data_to_crawl['business'] as $bizEntry )
                {
                    $crawlerController->updateBusinessEmails( $bizEntry );
                }
            }
    
    $biz = $crawlerController->scrapeForBusinessEmailAddress( "http://www.snappysnaps-kingslynn.co.uk" );
    dc( $biz );*/
    
    
    //dc( $email );
    //$updateEmails = $crawlerController->updateBusinessEmails( $bizEntry );
    //updateBusinessEmails
}

if( isset( $_GET['extractEmailAddressFromPage'] ) )
{
    //$emailaddress = $crawlerController->extractEmailAddressFromPage( "http://www.tickledpinkbridal.co.uk" );
    $emailaddress = $crawlerController->scrapeForBusinessEmailAddress( "http://www.tickledpinkbridal.co.uk" );
    
    dc( $emailaddress );
}