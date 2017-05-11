<?php namespace DirectoryCrawler\Controllers;

use DirectoryCrawler\Models\CrawlerLink;
use DirectoryCrawler\Models\Category;
use Symfony\Component\DomCrawler\Crawler;use DirectoryCrawler\Scrapers\Categories as CategoryScrapers;

class ScraperController extends BaseController {
    protected $scrape;
    protected $mergequery;
    protected $model;
    protected $filters;

    public function __construct()
    {
        $this->filters = new Filter();
    }

    public function scrapeBySingleLetters( $params = array() )
    {
        $target_link = $params['target_link'];
        $selector = $params['selector'];
        $parameters = array();

        $this->filters->addFilter( 'Letters', "^[A-Z]$" ); // Regex Only Alphabet(Upper-case) only

        $crawler = new DomCrawler( $target_link, $selector, $parameters );

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

        $ScrapeAlphabetNames = $this->filters->applyFilter( 'Letters', $results, 'name' );

        return [ 'ScrapedData' => $ScrapeAlphabetNames, 'RawResults' => $results ];
    }

    // See: Scrapers/Categories
    public function defaultCategoyScraper(){
        $target_link = "http://b99.co.uk";
        $scraper = new CategoryScrapers();
        $scraper->getDataByTargetLink( $target_link );
        return $scraper->invoke();
    }
}