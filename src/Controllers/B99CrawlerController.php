<?php namespace DirectoryCrawler\Controllers;

use DirectoryCrawler\DomCrawlers\DomCrawler;
use DirectoryCrawler\Helpers\Filter;
use DirectoryCrawler\Models\CrawlerLink;
use Symfony\Component\CssSelector\CssSelectorConverter;
use DirectoryCrawler\Models\Category;
use DirectoryCrawler\Models\CrawlData;
use DirectoryCrawler\Models\Page;
use Symfony\Component\DomCrawler\Crawler;
use DirectoryCrawler\HtmlPageDom;

class B99CrawlerController extends DomCrawlerController {
    private $devmode = false;
    private $devmodelimiter = 40;
    public $crawler;
    public $model;
    public $directory_id = 7;
	public $crawlData = array();

    public $filters;

    public $target_directory_site = "http://b99.co.uk";
    public $results;

    public function __construct()
    {
        parent::__construct();

        $this->filters = new Filter();
    }

    public function landingLetterLinks($use_cached=true, $htmlTagToTarget='a')
    {
        $this->results = array();
        // this will almost certainly time-out due to asynchronous page loading strategies but we only care about the first bit of the page anyway
        //$this->crawler = new DomCrawler( $this->target_directory_site, $htmlTagToTarget, ['use_cached'=>$use_cached] );
        $this->crawler = new DomCrawler( $this->target_directory_site, $htmlTagToTarget, ['use_cached'=>$use_cached] );
        $pageCache = new Page();
        $filters = new Filter();
        try {

            $filters->addFilter( 'Letters', "^[A-Z]$" ); // Regex Only Alphabet(Upper-case) only

            if (!$use_cached) {
                $this->crawler->htmlDomLoadFromCurl();
            } else {
                $this->crawler->htmlDomLoadFromCache($pageCache);
            }
			$dom = $this->crawler->getHtmlDom()->filter('table a')->links();
            foreach( $dom as $link ) {
                $name = $this->crawler->cleanText( $link->getNode()->textContent );
                $tag = $this->crawler->getTag( $name );
                $_results = [
                    'name' => $name,
                    'href' => $link->getUri(),
                    'tag' => $tag,
                    'text' => $name,
                    'innertext' => $name,
                    'outertext' => $name
                ];

                $this->results[] = $_results;
            }
        } catch(\Exception $e) {
            //ignore
        }

        $ScrapeAlphabetNames = $filters->applyFilter( 'Letters', $this->results, 'name' );
        return [ 'ScrapedData' => $ScrapeAlphabetNames, 'RawResults' => $this->results ];
    }

    public function getCategoriesByLetterLink($use_cached=true,  $target_link )
    {
        $this->crawler = new DomCrawler( $target_link, 'td a', ['use_cached'=>$use_cached] );
        $this->results = array();
        $pageCache = new Page();
        //$filters = new Filter();

        //$filters->addFilter( 'Letters', "^[A-Z]$" ); // Regex Only Alphabet(Upper-case) only
        try {

            if (!$use_cached) {
                $this->crawler->htmlDomLoadFromCurl();
            } else {
                $this->crawler->htmlDomLoadFromCache($pageCache);
            }
			$dom = $this->crawler->getHtmlDom()->filter('table a')->links();
            foreach ( $dom as $link ) {
				$name = $this->crawler->cleanText( $link->getNode()->textContent );
                $tag = $this->crawler->getTag( $name );
                $_results = [
                    'name' => $name,
                    'href' => $link->getUri(),
                    'tag' => $tag,
                    'text' => $name,
                    'innertext' => $name,
                    'outertext' => $name
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

    public function getAllCategoriesByLetterLink ($dataSource)
    {
        $categories = array();
        $letterLinksToCrawl = array();
        foreach ($dataSource as $dataArray) {
            $letterLinksToCrawl[] = $dataArray['href'];
        }
        foreach ($letterLinksToCrawl as $letterLink) 
		{
            $categories[] = $this->getCategoriesByLetterLink(true, $letterLink);
        }
        return $categories;
    }

    public function updateCategoryRecords($scraped_data,$dir_id)
    {
        $category = new Category();
         //$directory_id, $href, $name, $tag, $include='n', $id=-1
        $values = array();

        foreach ($scraped_data as $letter) {
            foreach ($letter as $data) {
                if (isset($data['href']) && isset($data['name']) && isset($data['tag'])) {
                    $value = array(
                        'directory_id' => $dir_id,
                        'href' => $data['href'],
                        'name' => $data['name'],
                        'tag' => $data['tag'],
                        'include' => 'n'
                    );
                    $values[] = $value;
                }
            }
        }

        //return $values;
        $category->insertMultipleValues($values);
    }

    public function getCategories()
    {
        $categoriesModel = new Category();
        return $categoriesModel->getAll(0,0,'*','ASC','name'," WHERE `directory_id`=7");
    }

    public function saveLocationsPage( $category_link, $echo=false, $use_cached=true )
    {
        if (strpos($category_link, 'allplaces')===FALSE) {
            if (!substr($category_link,-1)=='/') {
                $category_link .= '/';
            }
            $_SESSION['basehref'] = substr($category_link,0,strlen($category_link)-1);
            $category_link .= 'allplaces';
        }
        $this->crawler = new DomCrawler( $category_link, 'td a', [] );
        $catLink = substr( $category_link, 0, -1);
        $pageCache = new Page();
        $crawledBusinessesData = array();
        try {
            $this->crawler->htmlDomLoadFromCache($pageCache);
        } catch(\Exception $e) {
            //ignore
        }
        $_SESSION['current_crawler'] = $this->crawler;
        $_SESSION['category_link'] = $category_link;
        return 'page succeefully loaded, now crawling...';
    }

    public function saveLinks(\DirectoryCrawler\DomCrawlers\DomCrawler $crawler)
    {
        $crawlerLinkModel = new CrawlerLink();
        $crawler->htmlDomLoadFromString($crawler->page, $this->target_directory_site);
        $retlinks = array();

//        $crawler->filter('h1 a')->extract(array('_text', 'href'))

        $links = $crawler->htmlDom->filter('table a')->extract(array('href'));
        $count = 0;
        foreach ($links as $link) {
            $link = (strpos($link,'http://')===FALSE) ? $_SESSION['basehref'].$link : $link;
            $retlinks[] = array(
                'crawl_id'=>$_SESSION['crawl_id'],
                'tag' =>'',
                'url' => $link
            );
            if ($this->devmode) {
                $count ++;
                if ($count > $this->devmodelimiter) break;
            }
        }
        $crawlerLinkModel->insertMultipleValues($retlinks);

        return $links;
    }

    public function saveSecondTierLinks($current_record)
    {
        $directory_business_entries = array();
        $crawl_id = $_SESSION['crawl_id'];
        $pageCache = new Page();
        $crawlerLinkModel = new CrawlerLink();
        $directory_links = $crawlerLinkModel->getAll(10,$current_record,'*','ASC','id',"WHERE `crawl_id` = $crawl_id AND `tag`<>'business_entry'");
        if (!$directory_links) {
            return false;
        }
        $count = 0;
        foreach ($directory_links as $directory_link) {
            $crawler = new DomCrawler($directory_link['href'], 'div#latestnews a');
            $crawler->htmlDomLoadFromCache($pageCache);
/*            $links = $crawler->htmlDomFind();
            foreach ($links as $link) {
                $url = $link->extract(array('href'));
            }*/

            $dom = $crawler->htmlDom;
            $links = $dom->filter('div#latestnews a');
            if (empty($links)) continue;
            $urls = $links->extract(array('href'));
            if ($this->devmode) {
                $count ++;
                if ($count > $this->devmodelimiter) break;
            }
            foreach ($urls as $link) {
                $link = (strpos($link,'http://')===FALSE) ? $_SESSION['basehref'].$link : $link;
                $directory_business_entries[] = array(
                    'crawl_id'=>$_SESSION['crawl_id'],
                    'tag' =>'business_entry',
                    'url' => $link
                );
            }
            $constraint = " `crawl_id` = '$crawl_id' AND `tag` = 'business_entry'";
            $_SESSION['current_records_created'] = $crawlerLinkModel->getCount($constraint);
        }
		
        if ($crawlerLinkModel->insertMultipleValues($directory_business_entries)) {
            return count($directory_business_entries);
        } else {
            return false;
        }
    }

    public function mineDirectory($current_record)
    {
        $directory_business_entries = array();
        $crawl_id = $_SESSION['crawl_id'];
        $pageCache = new Page();
        $crawlerLinkModel = new CrawlerLink();
		$crawlDataModel = new CrawlData();
        $directory_links = $crawlerLinkModel->getAll(10,$current_record,'*', 'ASC', 'id', "WHERE `crawl_id` = '$crawl_id' AND `tag` = 'business_entry'");
        if (!$directory_links) {
            return false;
        }
        $count = 0;
        foreach ($directory_links as $directory_link) {
            $crawler = new DomCrawler($directory_link['href'], 'div#latestnews a');
            $crawler->htmlDomLoadFromCache($pageCache);
            /*            $links = $crawler->htmlDomFind();
                        foreach ($links as $link) {
                            $url = $link->extract(array('href'));
                        }*/
            $dom = $crawler->htmlDom;
			$business_data = $dom->filter('div#latestnews div');
			if (!empty($business_data)) {
				$bizEntry = $this->scrapeBusinessEntry($directory_link['href']);
				if (!empty($bizEntry)) {
                    $bizEntry['crawl_id'] = $crawl_id;
                    $this->updateCrawlDataRecord( $bizEntry, $crawl_id );
					if (isset($bizEntry['Website'])) {
						$directory_business_entries[] = array(
							'crawl_id'=>$_SESSION['crawl_id'],
							'tag' =>'business',
							'url' => $bizEntry['Website']
						);						
					}
                }
			} else {
				$links = $dom->filter('div#latestnews a');
				if (empty($links)) continue;
				$urls = $links->extract(array('href'));
				if ($this->devmode) {
					$count ++;
					if ($count > $this->devmodelimiter) break;
				}
				foreach ($urls as $link) {
					$link = (strpos($link,'http://')===FALSE) ? $_SESSION['basehref'].$link : $link;
					$bizEntry = $this->scrapeBusinessEntry( $link );
					if (!empty($bizEntry)) {
						$bizEntry['crawl_id'] = $crawl_id;
						$this->updateCrawlDataRecord( $bizEntry, $crawl_id );
						if (isset($bizEntry['Website'])) {
							$link = (strpos($link,'http://')===FALSE) ? $_SESSION['basehref'].$link : $link;
							$directory_business_entries[] = array(
								'crawl_id'=>$_SESSION['crawl_id'],
								'tag' =>'business',
								'url' => $link
							);
						}
					}
				}
			}
			$constraint = " `crawl_id` = '$crawl_id' AND `tag` = 'business_entry'";
			$_SESSION['current_records_created'] = $crawlerLinkModel->getCount($constraint);
        }
        if ($crawlDataModel->insertMultipleValues($this->crawlData)) {
            return count($this->crawlData);
        } else {
            return false;
        }

    }

    public function getBusinessesFromLocationsByCategory( $category_link, $echo=false, $use_cached=true )
    {
        $this->results = array();
        $this->saveLocationsPage( $category_link, $echo=false, $use_cached=true );
        $crawledBusinessesData = array();
        // Crawl every Locaation for Businesses.

            foreach ($this->crawler->htmlDomFind() as $link) {
                $catLink = $link->href;
                if (strpos($link->href, 'http://')===FALSE && strpos($link->href, 'https://')===FALSE) {
                    $catLink = $_SESSION['basehref'] . $link->href;
                }
                $name = $this->crawler->cleanText($link->text());
                $tag = $this->crawler->getTag($name);
                $_results = [
                    'name' => $this->crawler->cleanText($name),
                    'href' => $catLink,
                    'tag' => $tag,
                    'text' => $link->text(),
                    'innertext' => $link->text(),
                    'outertext' => $link->text()
                ];

                $_crawledBusinessesData = $_results['businesses'] = $this->getBusinessByLocation($catLink, $_results['href']);

                 $crawledBusinessesData = array_merge( $crawledBusinessesData, $_crawledBusinessesData );

                $this->results[] = $_results;
            }


        //$this->updateCrawledBusinessData( $this->crawlBusinessesData );

        return [ 'results' => $this->results, 'crawlBusinessData' => $crawledBusinessesData ];
    }

    public function getCategoriesByDirIdAndCatId( $dir_id, $cat_ids )
    {
        $category = new Category();

        if( is_array( $cat_ids ) )
        {
            return $category->getByDirIdAndCatIds( $dir_id, $cat_ids );
        }else{
            return $category->getByDirIdAndCatId( $dir_id, $cat_ids );
        }
    }



        public function getBusinessByLocation( $catLink, $Location_link, $use_cached=true )
    {
        $this->crawler = new DomCrawler( $Location_link, 'div#latestnews a', [] );
        $results = array();
        $pageCache = new Page();
        // Crawl every Locaation for Businesses.
        try {
            if (!$use_cached) {
                $this->crawler->htmlDomLoadFromCurl();
            } else {
                $this->crawler->htmlDomLoadFromCache($pageCache);
            }
            foreach ($this->crawler->htmlDomFind() as $link) {

                $name = $this->crawler->cleanText($link->text());
                $tag = $this->crawler->getTag($name);
                $_results = [
                    'name' => $this->crawler->cleanText($name),
                    'href' => $catLink . $link->href,
                    'tag' => $tag,
                    'text' => $link->text(),
                    'innertext' => $link->text(),
                    'outertext' => $link->text()
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
        $directory_business_entries = array();
        $crawl_id = $_SESSION['crawl_id'];
        $pageCache = new Page();
        $crawlerLinkModel = new CrawlerLink();
        $dom = null;
        try {
            $crawler = new DomCrawler($business_link, 'div#latestnews div');
            if (!empty($crawler)) {
                $crawler->htmlDomLoadFromCache($pageCache);
                /*            $links = $crawler->htmlDomFind();
                            foreach ($links as $link) {
                                $url = $link->extract(array('href'));
                            }*/
                $dom = $crawler->htmlDom;
            }
        } catch (\Exception $e) {

        }
        $content = null;
        try {
            if (!empty($dom)) {
                $content = $dom->filter('div#latestnews div');
                $html = '';
                foreach ($content as $domElement) {
                    $html .= $domElement->ownerDocument->saveHTML($domElement);
                }
                if (stripos($html, 'at.gif') !== false) {
                    /*                    $content->filter('img')->each(function (Crawler $crawler) {
                                            $atsymb = new Crawler('@', $_SERVER['SERVER_NAME'], '');
                                            $atsymb = $atsymb->getNode(0);
                                            foreach ($crawler as $node) {
                                                $node->parentNode->replaceChild($node->ownerDocument->importNode($atsymb), $node);
                                            }
                                        });
                    */
                    $htmlCrawler = new \DirectoryCrawler\HtmlPageDom\HtmlPageCrawler($html);
                    $htmlCrawler->filter('img')->append('<span>@</span>');
                    $content = $htmlCrawler->text();
                } else {
                    $htmlCrawler = new \DirectoryCrawler\HtmlPageDom\HtmlPageCrawler($html);
                    $content = $htmlCrawler->text();
                }
                if (!empty($content)) {
                    $articles = explode("\n", $content);
                    return $this->parseBusinessInfo($articles);
                }
                    //$content = $dom->filter('div#latestnews div')->text();
                    //if (!empty($content)) {
                    //    $articles = explode( "\n", $content );
                    //    return $this->parseBusinessInfo( $articles );
                    //}
            }
        } catch (\Exception $e) {
            die($e);
        }
    }

    public function parseBusinessInfo( $bizInfo )
    {
        if( !isset( $bizInfo[0] ) ) return false;

        $business_parsed_info = array();
        foreach( $bizInfo as $info )
        {
            $_tempInfo = explode( ":", $info );
            if( !empty( $_tempInfo[1]) )
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
		$data = array();
		if (is_array($crawlBusinessesData) && isset($crawlBusinessesData['Item name'])) { // this is an individual bizEntry
			$bizData = $crawlBusinessesData;
			if (stripos($bizData['Town or Suburb'], $bizData['Street address']) !== FALSE && stripos($bizData['Town or Suburb'], $bizData['Country or region']) !==FALSE) {
				$bizData['Town or Suburb'] = "";
			}
			$bizData['address'] = $bizData['Street address']." ".$bizData['Town or Suburb']. " ". $bizData['Country or region'].", ".$bizData['Postcode'];
            $data = ['crawl_id' => $bizData['crawl_id'],
                'business_name' => $bizData['Item name'],
                'business_phone' => $bizData['Telephone(s)'],
                'business_address' => $bizData['address'],
                'business_website' => $bizData['Website'],
                'business_emails' => $bizData['Email'],
                'record_status' => 'incomplete'
            ];
            // Insert Crawl Data
            $this->crawlData[] = $data;
		} else {
			foreach( $crawlBusinessesData as $bizData ) {
				if(!isset($bizData['address'])) {
					continue;
				}
				if (stripos($bizData['Town or Suburb'], $bizData['Street address']) !== FALSE && stripos($bizData['Town or Suburb'], $bizData['Country or region']) !==FALSE) {
					$bizData['Town or Suburb'] = "";
				}

				$bizData['address'] = $bizData['Street address']." ".$bizData['Town or Suburb']. " ". $bizData['Country or region'].", ".$bizData['Postcode'];

				$data = ['crawl_id' => $bizData['crawl_id'],
					'business_name' => $bizData['Item name'],
					'business_phone' => $bizData['Telephone(s)'],
					'business_address' => $bizData['address'],
					'business_website' => $bizData['Website'],
					'business_emails' => $bizData['Email'],
					'record_status' => 'incomplete'
				];
				// Insert Crawl Data
				$this->crawlData[] = $data;
			}
		}
	}

    public function getAllCrawlData($crawl_id)
    {
        $crawlDataModel = new CrawlData();
        return $crawlDataModel->getAll(0,0,'*','ASC','business_name'," WHERE `crawl_id`='$crawl_id'");
    }
    
    public function scrapeCrawledData( $record, $scrapeFor = 'emailaddress' )
    {
        $crawlDataModel = new CrawlData();
        $crawl_id=$_SESSION['crawl_id'];
        $businessEntries = $crawlDataModel->getAll(1,$record,'*','ASC','business_name'," WHERE `crawl_id`='$crawl_id' AND `business_website` <> ''");
        //$cd = $this->scrapeForBusinessEmailAddress( "http://" . $businessEntries[1]['business_website'] );
        //return $cd;
        //return $businessEntries;
        if ($businessEntries===FALSE) {
            return false;
        }
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
    
    public function scrapeForBusinessEmailAddress( $business_website, $use_cached=true )
    {

        $this->crawler = new DomCrawler( $business_website, 'a', [] );
        $pageCache = new Page();
        $results = array();
        try{
            if (!$use_cached) {
                $content = $this->crawler->htmlLoadFromCurl();
            } else {
                $content = $this->crawler->htmlLoadFromCache($pageCache);
            }
            $htmlCrawler = new \DirectoryCrawler\HtmlPageDom\HtmlPageCrawler($content);
            $htmlCrawler->filter('img')->append('<span>@</span>');
            $content = $htmlCrawler->text();
            $pattern = '/[A-Za-z0-9_-]+@[A-Za-z0-9_-]+\.([A-Za-z0-9_-][A-Za-z0-9_]+)/'; //regex for pattern of e-mail address
            preg_match_all($pattern, $content, $results); //find matching pattern
        } catch(\Exception $e) {
            //die( 'error' );
        }
        
        return $results;
    }
    
    public function updateBusinessEmails( $bizEntry )
    {
        $emailAddresses = array_merge( array( $bizEntry['business_emails'] ), $bizEntry['scrapeEmailAddress'] );
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
            CURLOPT_CONNECTTIMEOUT => 10,          // timeout on connect (in seconds)
            CURLOPT_TIMEOUT        => 10,          // timeout on response (in seconds)
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