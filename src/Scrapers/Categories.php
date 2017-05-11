<?php namespace DirectoryCrawler\Scrapers;

use DirectoryCrawler\Models\Category;

Class Categories extends Scraper
  {
    protected $model;
    protected $regex;
    protected $targetLink;
    protected $data;
    protected $filters = array();

    public function __construct()
    {
        parent::__construct();
        $this->model = new Category();
        $this->filters = $this->Filters();
    }

    public function addFilter($type,$filter) {
        $this->filters[$type] = $filter;
    }

    public function setTargetLink( $target_link )
    {
        $this->targetLink = $target_link;
    }

    public function getTargetLink()
    {
        return $this->targetLink;
    }

    public function setData( $data )
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getDataByTargetLink( $target_link = null )
    {
        $target_link = is_null( $target_link ) ? $this->getTargetLink() : $target_link;

        if( is_null( $target_link ) ) return false;

        return $this->data = $this->crawlerLinkModel->getByTargetLink( $target_link );
    }

    public function invoke( $data = null, $store = false )
    {
        $links = is_null( $data ) ? $this->getData() : $data;

        if( !sizeof( $links ) ) return false;

        $results = array();
        foreach( $links as $link )
        {
            // Filter for only valid Categories
            if( $res = $this->filters->applyFilter( 'category', $link['href'] ) )
            {
                $name =  $this->filters->cleanText( $link['innertext'] );
                $tag = $this->filters->getTag( $name );
                $results[] = [ 'name' => $name, 'tag' => $tag, 'url' => $link['href'], 'include' => 'n' ];
            }
        }

        $parseValues = $this->filters->parseMultipleValues( $results );

        if( $store )
        {
            //return $parseValues;
            // Insert Filtered Data to Category Table
            try{
                $this->model->insertMultipleValues( $parseValues );
            }catch (\Exception $e ){
                throw new \Exception( 'Error Query: ',  $e->getMessage(), "\n" );
            }
        }
        return ['results' => $results, 'insert_values' => $parseValues ];
    }
}
?>
