<?php namespace DirectoryCrawler\Scrapers;

use DirectoryCrawler\Models\CrawlerLink;

Class BaseScraper {
    protected $htmlDom;
    protected $selector;
    protected $parameter;
    protected $target_link;
    
    protected $model;
    
    public static function init( $target_link='', $selector='a', $parameter=array() ){
        return new self( $target_link, $selector, $parameter );    
    }
    
    public function __construct( $target_link='', $selector='a', $parameter=array() ){
        $this->htmlDom = new \simple_html_dom();
        $this->setTargetLink( $target_link );
        $this->setSelector( $selector );
    }
    
    public function setTargetLink($target_link){
        $this->target_link = $target_link;
    }
    
    public function setSelector($selector){
        $this->selector = $selector;
    }
    
    public function setParameter($key,$value){
        $this->parameter[$key] = $value;
    }
    
    public function htmlDomLoadFile(){
        $this->htmlDom->load_file( $this->target_link );
    }
    public function htmlDomFind(){
        return $this->htmlDom-w>find( $this->selector );
    }
    
    public function filterLinksByRegEx( $regex, $links ){
        if( preg_match( $regex, $links, $match ) ){
            return array( 'results' => true, 'match' => $match );
        }else{
            return false;
        }        
    }
    
    public function cleanText( $text ){
        return trim( preg_replace("/[\/\&%<#\$]|raquo;/", "", $text) );
    }
    
    public function getTag( $text ){
        return  strtolower( str_replace( array(' ','  '), '-', $text ) );
    }
    
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
