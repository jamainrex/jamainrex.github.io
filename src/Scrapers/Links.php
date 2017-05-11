<?php namespace DirectoryCrawler\Scrapers;

use DirectoryCrawler\Models\CrawlerLink;
Class Links extends BaseScraper
  {
     protected $htmlDom;
     protected $selector;
     protected $parameter;
     protected $target_link;
      
     protected $model;
     
     public function invoke(){
        $this->model = new CrawlerLink();
        
        $this->htmlDomLoadFromCurl();
        $results = array();
        
        foreach( $this->htmlDomFind() as $link )
        {    
            echo $link->href;     
            // No filter ran by global.
            //if( $res = $this->model->filterLinksByRegEx( "/http:\/\/.(\w|\-)*.b99.co.uk/i", $link->href ) ){
                $name =  $this->cleanText( $link->innertext() );
                $tag = $this->getTag( $name );
                $_results = array( 'name' => $this->cleanText( $name ), 
                                   'href' => $link->href, 
                                   'tag' => $tag, 
                                   'text' => $link->text(), 
                                   'innertext' => $link->innertext(), 
                                   'outertext' => $link->outertext() );   
                
                // Insert to database
                $this->model->insert( $this->target_link, $_results['name'], $_results['href'], $_results['tag'], $_results['text'], $_results['innertext'], $_results['outertext'] );
                $results[] = $_results;
            //}
            
        }
        
        return $results;
    }
  }
?>
