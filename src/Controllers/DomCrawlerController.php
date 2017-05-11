<?php namespace DirectoryCrawler\Controllers;

use DirectoryCrawler\DomCrawlers\DomCrawler;
use DirectoryCrawler\Models\CrawlerLink;
use DirectoryCrawler\Models\Page;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;
class DomCrawlerController extends BaseController {
    public $domCrawler;
    public $model;
    public $page;
    public $base_dir = __DIR__ ;
    public $cache_dir = '/cache';
    public $directories_dir = '/directories';
    public $sites_dir = '/sites';


    public function __construct()
    {
        $this->domCrawler = new DomCrawler();
        $this->model = new CrawlerLink();
    }

    public function crawlTargetLink( $target_link, $selector='a', $parameter=array() )
    {
        $this->domCrawler = DomCrawler($target_link, $selector, $parameter);


   //     ( $target_link, $selector, $parameter );

        $this->domCrawler->htmlDomLoadFromCache($this->page);
        $results = array();

        foreach( $this->domCrawler->htmlDomFind() as $link )
        {
            // No filter ran by global.
            $name =  $this->domCrawler->cleanText( $link->innertext() );
            $tag = $this->domCrawler->getTag( $name );
            $_results = array( 'name' => $this->domCrawler->cleanText( $name ),
                'href' => $link->href,
                'tag' => $tag,
                'text' => $link->text(),
                'innertext' => $link->innertext(),
                'outertext' => $link->outertext() );

            // Insert to database
            $this->model->insertLinks( $this->domCrawler->getTargetLink(), $link->href, $name, $tag, $link->text(), $link->outertext(), $link->innertext() );
            $results[] = $_results;
        }

        return $results;
    }
}