<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 01/02/16
 * Time: 02:10
 */

namespace DirectoryCrawler\Controllers;
use DirectoryCrawler\Models\Category;
use DirectoryCrawler\Models\CrawlData;
use DirectoryCrawler\Models\Page;
use Symfony\Component\DomCrawler;

class TierCrawl extends DomCrawlerController
{
    public $data_to_crawl = array(); // dumps of the html pages
    public $urls_to_crawl = array();
    public $css_selector_containing_data_to_mine;
    public $crawl_types_contained_in_this_tier; // can be 1 for external links, 2 for internal links or 3 for a combination of the two
    public $crawl_id;
    public $css_appended_selectors_of_data_to_save; // can be string or array - try to use css classes - this is the css path relative to the path for css_selector_containing_data_to_mine.
    public $expiry;
    public $model;
    private $pageCache;

    public function __construct($crawl_id, $expiry)
    {
        $this->crawl_id = $crawl_id;
        $this->expiry = $expiry;
        $this->pageCache = new Page();
        $this->loadData();
        $this->trimData();
        getDataByCssSelector();
        return;
    }

    public function runCrawl()
    {
        foreach ($this->urls_to_crawl as $url) {
            $this->setSourceUri($url);
        }
        foreach ($this->data_to_crawl as $uri=>$html) {
            $crawler = new DomCrawler\Crawler($html,$_SERVER['SERVER_NAME'],$uri);
            $dom = $crawler->filter($this->css_selector_containing_data_to_mine . $this->css_appended_selectors_of_data_to_save);
            //if we're looking for internal links, get internal links,
            //if we're looking for external links, get external links,
            //if we're looking for business info, get business info
        }
    }

    public function addUriToTierCrawl($uri)
    {
        $this->urls_to_crawl[$uri] = $uri;
    }

    public function setOverallCssSelector($css)
    {
        $this->css_selector_containing_data_to_mine = $css;
    }

    public function setCssSubSelectorForFields($css)
    {
        $this->css_appended_selectors_of_data_to_save = $css;
    }

    public function setSourceUri($uri)
    {
        $this->data_to_crawl[] = $this->loadData($uri);
    }

    public function loadData($uri)
    {
        return $this->getPageHTMLFromModel($uri);
    }

    private function getPageHTMLFromCurl($uri)
    {
        $content = self::getPage($uri);
        $retries = 10;
        while (stripos($content,'</html')===FALSE && $retries>0) {
            $content = self::getPage($uri);
            $retries--;
        }
        return $content;
    }

    private function getPageHTMLFromModel($uri)
    {
        $result = $this->pageCache->getByHref($uri);
        if (!empty($result) && !empty($result['content']) && stripos($result['content'],'</HTML')!==FALSE) {
            return $result['content'];
        } else {
            $content = $this->getPageHTMLFromCurl($uri);
            $this->pageCache->insert(array(
                'href' => $uri,
                'content' => $content
            ));
            return $content;
        }
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