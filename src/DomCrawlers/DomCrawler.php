<?php namespace DirectoryCrawler\DomCrawlers;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;
use DirectoryCrawler\Models\Page;

class DomCrawler {
    public $htmlDom;
    public $selector;
    public $cssSelector;
    public $parameter;
    public $target_link;
    public $page;

    public static function init( $target_link='', $selector='a', $parameter=array() ){
        return new self( $target_link, $selector, $parameter );
    }

    public function __construct( $target_link='', $selector='a', $parameter=array() ){
        $this->htmlDom = new Crawler('', $target_link);
        $this->setTargetLink( $target_link );
        $this->setSelector( $selector );
        $this->cssSelector = new CssSelectorConverter();
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

    public function getTargetLink()
    {
        return $this->target_link;
    }

    public function getSelector()
    {
        return $this->selector;
    }

    public function getParameter()
    {
        return $this->parameter;
    }

    public function getHtmlDom()
    {
        return $this->htmlDom;
    }          
    
    public function getPageString()
    {
        return $this->htmlDom->html();
    }
    public function htmlLoadFile()
    {
        $this->page = $this->getPage($this->target_link);
    }

    public function htmlLoadFromCache(Page $pageCache)
    {
        $result = $pageCache->getByHref($this->target_link);
        if (!empty($result) && !empty($result['content']) && stripos($result['content'],'</HTML')!==FALSE) {
            $content = $result['content'];
        } else {
            $content = $this->htmlLoadFromCurl();
            $pageCache->insert(array(
                'href' => $this->target_link,
                'content' => $content
            ));
        }
        return $content;
    }
    public function htmlLoadFromCurl(){
        $content = self::getPage($this->target_link);
        $retries = 10;
        while (stripos($content,'</html')===FALSE && $retries>0) {
            $content = self::getPage($this->target_link);
            $retries--;
        }
        $this->page = $content;
        return $content;
    }

    public function htmlDomLoadFile(){
        $page = $this->getPage($this->target_link);
        $this->htmlDom->clear();
        $this->htmlDom->addHtmlContent($page);
    }

    public function htmlDomLoadFromCache(Page $pageCache)
    {
        $result = $pageCache->getByHref($this->target_link);
        if (!empty($result) && !empty($result['content']) && stripos($result['content'],'</HTML')!==FALSE) {
            $this->htmlDom->clear();
            $this->htmlDom->addHtmlContent($result['content']);
        } else {
            $this->htmlDomLoadFromCurl();
            $pageCache->insert(array(
                'href' => $this->target_link,
                'content' => $this->page
            ));
        }
    }
    public function htmlDomLoadFromCurl(){
        $content = self::getPage($this->target_link);
        $retries = 10;
        while (stripos($content,'</html')===FALSE && $retries>0) {
            $content = self::getPage($this->target_link);
            $retries--;
        }
        $this->page = $content;
        $this->htmlDom->clear();
        $this->htmlDom->addHtmlContent($this->page);
    }
    public function htmlDomLoadFromString($str){
        $this->htmlDom->clear();
        $this->htmlDom->addHtmlContent($str);
    }

    public function htmlDomFind(){
        return $this->htmlDom->filter( $this->selector );
    }

    public function saveDom( $filepath = '' )
    {
        if (!empty($filepath)) {
            $handle = fopen($filepath,'r+');
            fwrite($handle, $this->htmlDom->html());
            fclose($handle);
        }
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

    public function get_file_html( $link )
    {
        return $this->getPage( $link );
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

