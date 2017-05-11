<?php namespace DirectoryCrawler\Controllers;

use DirectoryCrawler\Helpers\Filter;
use Symfony\Component\CssSelector\CssSelectorConverter;
use DirectoryCrawler\Models\Category;
use DirectoryCrawler\Models\CrawlData;

class IndependentShopsCrawlerController extends DomCrawlerController {
    protected $crawler;
    protected $model;
    public $directory_id = 35;
    protected $filters;

    protected $target_directory_site = "http://www.independentshops.co.uk/";
    protected $results;

    protected $list_of_search_urls = array(
        'http://www.independentshops.co.uk/in.php?Postcode=a',
        'http://www.independentshops.co.uk/in.php?Postcode=b',
        'http://www.independentshops.co.uk/in.php?Postcode=c',
        'http://www.independentshops.co.uk/in.php?Postcode=d',
        'http://www.independentshops.co.uk/in.php?Postcode=e',
        'http://www.independentshops.co.uk/in.php?Postcode=f',
        'http://www.independentshops.co.uk/in.php?Postcode=g',
        'http://www.independentshops.co.uk/in.php?Postcode=h',
        'http://www.independentshops.co.uk/in.php?Postcode=i',
        'http://www.independentshops.co.uk/in.php?Postcode=j',
        'http://www.independentshops.co.uk/in.php?Postcode=k',
        'http://www.independentshops.co.uk/in.php?Postcode=l',
        'http://www.independentshops.co.uk/in.php?Postcode=m',
        'http://www.independentshops.co.uk/in.php?Postcode=n',
        'http://www.independentshops.co.uk/in.php?Postcode=o',
        'http://www.independentshops.co.uk/in.php?Postcode=p',
        'http://www.independentshops.co.uk/in.php?Postcode=q',
        'http://www.independentshops.co.uk/in.php?Postcode=r',
        'http://www.independentshops.co.uk/in.php?Postcode=s',
        'http://www.independentshops.co.uk/in.php?Postcode=t',
        'http://www.independentshops.co.uk/in.php?Postcode=u',
        'http://www.independentshops.co.uk/in.php?Postcode=v',
        'http://www.independentshops.co.uk/in.php?Postcode=w',
        'http://www.independentshops.co.uk/in.php?Postcode=x',
        'http://www.independentshops.co.uk/in.php?Postcode=y',
        'http://www.independentshops.co.uk/in.php?Postcode=z'
    );

    public function __construct()
    {
        parent::__construct();

        $this->filters = new Filter();
    }

    public function landingLetterLinks($htmlTagToTarget='a')
    {
        /*$_results = [
            'name' => $crawler->cleanText( $name ),
            'href' => $link->href,
            'tag' => $tag,
            'text' => $link->text(),
            'innertext' => $link->innertext(),
            'outertext' => $link->outertext()
        ];*/

        return [ 'ScrapedData' => ['match' => '' ] , 'RawResults' => ''];
    }

    public function getCategoriesByLetterLink( $target_link )
    {
        $this->results = array();
        //$filters = new Filter();

        //$filters->addFilter( 'Letters', "^[A-Z]$" ); // Regex Only Alphabet(Upper-case) only
        try {

            foreach ($this->list_of_search_urls as $link) {

                $name = 'Businesses whose postcode contains the letter ' . substr($link, -1);
                $_results = [
                    'name' => $name,
                    'href' => $link,
                    'tag' => '',
                    'text' => $name,
                    'innertext' => '',
                    'outertext' => ''
                ];

                $this->results[] = $_results;
            }
        } catch (\Exception $e) {
            //ignore
        }

        return $this->results;
        /*$ScrapeAlphabetNames = $filters->applyFilter( 'Letters', $results, 'name' );
        return [ 'ScrapedData' => $ScrapeAlphabetNames, 'RawResults' => $results ];*/
    }

    public function getAllCategoriesByLetterLink ($dataSource='')
    {
    }

    public function updateCategoryRecords($scraped_data,$dir_id)
    {
        // don't even bother for this directory, just hold them in memory, there's not enough of them
    }

    public function getCategories()
    {
        $allCategories = array();
        $allCategories[] = [
            'include'=>'y',
            'id'=>'1',
            'name'=>'all pages'];
        return $allCategories;
    }

    public function getBusinessesFromLocationsByCategory($url='')
    {
        $this->results = array();
        $crawledBusinessesData = array();
        //foreach ($this->list_of_search_urls as $url) {
            try {
                $_results = $this->scrapeBusinessEntriesByPostcodeLetterPage( $url );
                $this->results[] = $_results['results'];
                $crawledBusinessesData = array_merge( $crawledBusinessesData, $_results['crawlBusinessData'] );
            } catch(\Exception $e) {
                //ignore
            }
        //}
        //$this->updateCrawledBusinessData( $this->crawlBusinessesData );

        return [ 'results' => $this->results, 'crawlBusinessData' => $crawledBusinessesData ];
    }

    public function scrapeBusinessEntriesByPostcodeLetterPage( $url )
    {
        $this->results = array();

        $crawler = new DomCrawler( $url, 'div.directory', [] );
            $catLink = $url;

            $crawledBusinessesData = array();
            // Crawl every Locaation for Businesses.
            try {
                $page = self::getPage($url);
                //$crawler->htmlDomLoadFromCurl( $url );
                $crawler->htmlDomLoadFromString($page);
                foreach ($crawler->htmlDomFind() as $article) {

                    $_link = $article->find('a.directoryheading', 0);
                    
                    $_results = [
                        'name' => $article->find('span[itemprop=name]', 0)->plaintext,
                        'href' => ( isset( $_link->href ) ? $_link->href : "" )
                    ];

                    $item['business_name'] = $_results['name'];
                    $item['business_website'] = $_results['href'];
                    $item['business_address'] = $article->find('span[itemprop=address]', 0)->plaintext;
                    //$item['details'] = $article->find('a.directoryheading', 0)->plaintext;
                    //$item['activity'] = $article->find('span.activity', 0)->plaintext;
                    //$item['facebook'] = $article->find('a.Casepoint on Facebook', 0)->href;
                    //$item['twitter'] = $article->find('a.Giftspace on Twitter', 0)->href;
                    //$articles[] = $item;

                    $_results['businesses'] = $item;
                    $crawledBusinessesData[] = $item;

                    sleep(2);
                    $this->results[] = $_results;
                }
            } catch(\Exception $e) {
                //ignore
            }



        //$this->updateCrawledBusinessData( $this->crawlBusinessesData );

        return [ 'results' => $this->results, 'crawlBusinessData' => $crawledBusinessesData ];
    }

    public function getCategoriesByDirIdAndCatId( $dir_id, $cat_ids )
    {
        $_categories = array();
        foreach ($this->list_of_search_urls as $url)
            $_categories[] = ['href' => $url];

        return $_categories;
    }
    public function getBusinessByLocation( $catLink, $Location_link )
    {
        $crawler = new DomCrawler( $Location_link, 'div#latestnews a', [] );
        $results = array();
        // Crawl every Locaation for Businesses.
        try {

            $crawler->htmlDomLoadFromCurl();
            foreach ($crawler->htmlDomFind() as $link) {

                $name = $crawler->cleanText($link->innertext());
                $tag = $crawler->getTag($name);
                $_results = [
                    'name' => $crawler->cleanText($name),
                    'href' => $catLink . $link->href,
                    'tag' => $tag,
                    'text' => $link->text(),
                    'innertext' => $link->innertext(),
                    'outertext' => $link->outertext()
                ];

                $results[] = $this->scrapeBusinessEntry($_results['href']);
            }
        } catch (\Exception $e) {
            // ignore
        }

        return $results;
    }

    public function scrapeBusinessEntry( $business_link )
    {
        $html = \DirectoryCrawler\Helpers\file_get_html( $business_link );
        $articles = array();
        foreach( $html->find( "div#latestnews div" ) as $article )
        {
            $articles[] = explode( "\n", $article->plaintext );
        }
        return $this->parseBusinessInfo( $articles );
    }

    public function parseBusinessInfo( $bizInfo )
    {
        if( !isset( $bizInfo[0] ) ) return false;

        $business_parsed_info = array();
        foreach( $bizInfo[0] as $info )
        {
            $_tempInfo = explode( ":", $info );
            if( isset( $_tempInfo[1]) )
            {
                if( preg_match( "/business type/i", $_tempInfo[0], $match ) ) {
                    $business_parsed_info[ trim( $match[0] ) ] = trim( $_tempInfo[1] );
                    break;
                }else{
                    $business_parsed_info[ trim( $_tempInfo[0] ) ] = trim( $_tempInfo[1] );
                }
            }

            else
                $business_parsed_info['other_info'][] = trim( $_tempInfo[0] );
        }

        return $business_parsed_info;
    }

    public function updateCrawlDataRecord( $crawlBusinessesData, $crawl_id )
    {

        $crawlDataModel = new CrawlData();

        foreach( $crawlBusinessesData as $bizData )
        {
            //$bizData['address'] = $bizData['Street address']." ".$bizData['Locaation or Suburb']. " ". $bizData['Country or region'].", ".$bizData['Postcode'];

            $data = ['crawl_id' => $crawl_id,
                'categories' => "",
                'business_name' => $bizData['business_name'],
                'business_phone' => "",
                'business_address' => $bizData['business_address'],
                'business_website' => $bizData['business_website'],
                'business_emails' => "",
                'record_status' => 'incomplete'
            ];

            // Insert Crawl Data
            $crawlDataModel->insert( $data );
        }


    }

    public function getAllCrawlData($crawl_id)
    {
        $crawlDataModel = new CrawlData();
        return $crawlDataModel->getAll(0,0,'*','ASC','business_name'," WHERE `crawl_id`='$crawl_id'");
    }

    public function scrapeCrawledData( $crawl_id, $scrapeFor = 'emailaddress' )
    {
        $crawlDataModel = new CrawlData();

        $businessEntries = $crawlDataModel->getAll(0,0,'*','ASC','business_name'," WHERE `crawl_id`='$crawl_id' AND `business_website` <> ''");
        //$cd = $this->scrapeForBusinessEmailAddress( "http://" . $businessEntries[1]['business_website'] );
        //return $cd;
        //return $businessEntries;
        $results = array();
        try{
            foreach( $businessEntries as $bizEntry )
            {
                if( $resp = self::checkSite( $bizEntry['business_website'] ) )
                {
                    //Check if valid URL
                    $business_link = $bizEntry['business_website'];

                    if( strpos( $bizEntry['business_website'], 'http://' ) === false ) $business_link = "http://".$bizEntry['business_website'];

                    if( $crawledEmailAddressData = $this->scrapeForBusinessEmailAddress( $business_link ) ){
                        $bizEntry['scrapeEmailAddress'] = $crawledEmailAddressData;
                        $results['business'][] = $bizEntry;
                    }

                    $results['online'][] = $bizEntry['business_website'];
                }else
                    $results['offline'][] = $bizEntry['business_website'];
            }
        }catch(\Exception $e) {
            //throw new Exception( $e );
        }
        return $results;
    }
    
    public function scrapeCrawledDataById( $crawl_data_id, $scrapeFor = 'emailaddress' )
    {
        $crawlDataModel = new CrawlData();

        $businessEntries = $crawlDataModel->getAll(0,0,'*','ASC','business_name'," WHERE `id`=$crawl_data_id AND `business_website` <> ''");
        //$cd = $this->scrapeForBusinessEmailAddress( "http://" . $businessEntries[1]['business_website'] );
        //return $cd;
        //return $businessEntries;
        $results = array();
        try{
            foreach( $businessEntries as $bizEntry )
            {
                if( $resp = self::checkSite( $bizEntry['business_website'] ) )
                {
                    //Check if valid URL
                    $business_link = $bizEntry['business_website'];

                    if( strpos( $bizEntry['business_website'], 'http://' ) === false ) $business_link = "http://".$bizEntry['business_website'];

                    if( $crawledEmailAddressData = $this->scrapeForBusinessEmailAddress( $business_link ) ){
                        $bizEntry['scrapeEmailAddress'] = $crawledEmailAddressData;
                        $results['business'][] = $bizEntry;
                    }
                    
                    /*if( $crawledEmailAddressData = $this->extractEmailAddressFromPage( $business_link ) ){
                        $bizEntry['scrapeEmailAddress'] = $crawledEmailAddressData;
                        $results['business'][] = $bizEntry;
                    }*/

                    $results['online'][] = $bizEntry['business_website'];
                }else
                    $results['offline'][] = $bizEntry['business_website'];
            }
        }catch(\Exception $e) {
            //throw new Exception( $e );
        }
        return $results;
    }

    public function scrapeForBusinessEmailAddress( $business_website )
    {

        $crawler = new DomCrawler( $business_website, 'body', [] );
        $crawler->htmlDomLoadFromCurl();          
        $crawler->setSelector('a');
        $this->results = array();     
        try{
            foreach( $crawler->htmlDomFind() as $link )
            {
                //$results[] = $link->href;
                if( preg_match( "/mailto:/i", trim( $link->href ), $match ) ){
                    list( $_email ) = explode("?", $link->href);   
                    $scrapedEmail = trim( str_replace( "mailto:", "", trim( $_email ) ) ); 
                    
                    // Check for duplicates.
                    if( !in_array( $scrapedEmail, $this->results ) ) 
                        $this->results[] = str_replace( "mailto:", "", $scrapedEmail );    
                }
                
                if( preg_match( "/contact:/i", trim( $link->href ), $match ) )
                {
                    $this->extractEmailAddressFromPage( $link->href );
                        
                }

            } 
            
        } catch(\Exception $e) {
            //die( 'error' );
        }
        
        $page_str = \DirectoryCrawler\Helpers\file_get_html( $business_website )->plaintext; 
        $page_str = str_replace("\r\n",' ',$page_str);
        $page_str = str_replace("\n",' ',$page_str);
        
        try{
            foreach(preg_split('/ /', $page_str) as $token) {
                $email = filter_var($token, FILTER_VALIDATE_EMAIL);
                if ($email !== false) { 
                   list( $_email ) = explode("?", $email);   
                   $scrapedEmail = trim( str_replace( "mailto:", "", trim( $_email ) ) ); 
                    
                   // Check for duplicates.
                   if( !in_array( $scrapedEmail, $this->results ) ) 
                    $this->results[] = str_replace( "mailto:", "", $scrapedEmail );
                }
            }
         } catch(\Exception $e) {
            //die( 'error' );
        }

        return $this->results;
    }      
    
    public function extractEmailAddressFromPage( $page_link )
    {                            
        $page_str = \DirectoryCrawler\Helpers\file_get_html( $page_link )->plaintext; 
        $page_str = str_replace("\r\n",' ',$page_str);
        $page_str = str_replace("\n",' ',$page_str);
        
        try{
            foreach(preg_split('/ /', $page_str) as $token) {
                $email = filter_var($token, FILTER_VALIDATE_EMAIL);
                if ($email !== false) { 
                   list( $_email ) = explode("?", $email);   
                   $scrapedEmail = trim( str_replace( "mailto:", "", trim( $_email ) ) ); 
                    
                   // Check for duplicates.
                   if( !in_array( $scrapedEmail, $this->results ) ) 
                    $this->results[] = str_replace( "mailto:", "", $scrapedEmail );
                }
            }
         } catch(\Exception $e) {
            //die( 'error' );
        }
        
        //return $results;  
    }

    public function updateBusinessEmails( $bizEntry )
    {
        //$emailAddresses = array_merge( array( $bizEntry['business_emails'] ), $bizEntry['scrapeEmailAddress'] );
        $emailAddresses = $bizEntry['scrapeEmailAddress'];
        $crawlDataModel = new CrawlData();
        $crawlDataModel->updateEmailAddress( $bizEntry['id'], $emailAddresses );
    }


    private static function checkSite( $url ) {
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        $options = array(
            CURLOPT_RETURNTRANSFER => true,      // return web page
            CURLOPT_HEADER         => false,     // do not return headers
            CURLOPT_FOLLOWLOCATION => true,      // follow redirects
            CURLOPT_USERAGENT      => $useragent, // who am i
            CURLOPT_AUTOREFERER    => true,       // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 2,          // timeout on connect (in seconds)
            CURLOPT_TIMEOUT        => 2,          // timeout on response (in seconds)
            CURLOPT_MAXREDIRS      => 10,         // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false,     // SSL verification not required
            CURLOPT_SSL_VERIFYHOST => false,     // SSL verification not required
        );
        $ch = curl_init( $url );
        curl_setopt_array( $ch, $options );
        curl_exec( $ch );

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($httpcode == 200);
    }

    private static function getPage ($url) {
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        $options = array(
            CURLOPT_RETURNTRANSFER => true,      // return web page
            CURLOPT_HEADER         => false,     // do not return headers
            CURLOPT_FOLLOWLOCATION => true,      // follow redirects
            CURLOPT_USERAGENT      => $useragent, // who am i
            CURLOPT_AUTOREFERER    => true,       // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 2,          // timeout on connect (in seconds)
            CURLOPT_TIMEOUT        => 2,          // timeout on response (in seconds)
            CURLOPT_MAXREDIRS      => 10,         // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false,     // SSL verification not required
            CURLOPT_SSL_VERIFYHOST => false,     // SSL verification not required
        );
        $ch = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $page = curl_exec( $ch );

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $page;
    }

}