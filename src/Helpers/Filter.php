<?php namespace DirectoryCrawler\Helpers;

Class Filter {

    protected $filters = array();

    public function setFilters( $tag, $filters = array() )
    {
        foreach( $filters as $filter )
            $this->addFilter( $tag, $filter );
    }

    public function addFilter( $tag, $regex )
    {
        $this->filters[$tag][] = $regex;
    }

    public function getFilters( $tag )
    {
        return isset( $this->filters[ $tag ] ) ? $this->filters[ $tag ] : false;
    }

    public function deleteFilter( $tag, $index = -1 )
    {
        if( !isset( $this->filters[ $tag ][ $index ] ) )
            return false;

        unset( $this->filters[ $tag ][ $index ] );
        return true;
    }

    public function deletFilters( $tag )
    {
        if( !isset( $this->filters[ $tag ] ) ) return false;

        unset( $this->filters[ $tag ] );
        return true;
    }

    public function applyFilter( $tag, $data, $filterBy = 'href' )
    {
        if( !isset( $this->filters[$tag] ) ) return false;

        if( is_string( $data ) ){
            if( preg_match( "/". implode( "|", $this->getFilters( $tag ) ) ."/i", $data, $match ) )
                return [ 'status' => true, 'data' => $data, 'match' => $match ];
            else
                return false;

        }elseif( is_array( $data ) ){
            $matches = [];
            $_data = [];
            $filters = "/". implode( "|", $this->getFilters( $tag ) ) ."/i";
            foreach( $data as $index => $d )
            {
                $value = $d;
                if( is_array( $d ) && isset( $d[ $filterBy ] ) ) $value = $d[ $filterBy ];

                if( preg_match( $filters, $value, $match ) )
                {
                    $matches[ $match[0] ] = $d ;
                    $_data[] = $d;
                }else
                    unset( $data[ $index ] );
            }

            return [ 'status' => true, 'data' => $_data, 'match' => $matches ];
        }else
            return [ 'status' => true, 'data' => [], 'match' => [] ];
    }

    /**
     * Static Functions
     */

    public static function filterLinksByRegEx( $regex, $links ){
        if( preg_match( $regex, $links, $match ) ){
            return array( 'results' => true, 'match' => $match );
        }else{
            return false;
        }
    }

    public static function cleanText( $text ){
        return trim( preg_replace("/[\/\&%<#\$]|raquo;/", "", $text) );
    }

    public static function getTag( $text ){
        return  strtolower( str_replace( array(' ','  '), '-', $text ) );
    }

    public static function parseMultipleValues( $values )
    {
        $parsedValues = array();
        // if Set as Arrays
        if( is_array( $values[0] ) )
            foreach( $values as $value )
                $parsedValues[] = "'" . implode( "', '", $value  ) . "'";;

        return "(". implode( "), (", $parsedValues  ) . ")";
    }
}
?>