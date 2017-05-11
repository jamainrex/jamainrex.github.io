<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 01/02/16
 * Time: 01:59
 */

namespace DirectoryCrawler\Controllers;


class MyUKLocalCrawlerController extends DomCrawlerController
{
    private $devmode = true;
    private $devmodelimiter = 40;
    public $crawler;
    public $model;
//    public $directory_id = 7;

    public $filters;
    public static $expiry;

    public $source_directory_site = "http://myuklocal.co.uk/";
    public $results;

    public $crawl_id;

    private $crawl_type = array(
        'link_to_external_url' => 1,
        'link_to_url_within_directory' => 2,
        'link_to_business_listing_within_directory' => 4,
        'paginated_link' => 8,
        'data_to_be_stored' => 16
    );

    public $crawl_map = array(
        array(
            'uri' => 'http://myuklocal.co.uk/categories/',
            'css_location_of_data_to_mine' => 'article.b-article>section>div',
            'selectors_to_save' => 'ul li>a',
            'to_be_narrowed_by_the_user' => true, // eg categories
            'is_pagination_seq' => false,
            'storage' => array(
                array(
                    'crawl_type' => 2, // selector contains data of which type?
                    'results_storage_table' => 'categories'
                )
            ),
        ),
        array(
            'uri' => 'http://myuklocal.co.uk/categories/$1/',
            'css_location_of_data_to_mine' => 'article.b-article>section>div',
            'selectors_to_save' => 'ul li>a',
            'to_be_narrowed_by_the_user' => false,
            'is_pagination_seq' => false,
            'storage' => array(
                array(
                    'crawl_type' => 2, // selector contains data of which type?
                    'results_storage_table' => 'categories'
                )
            ),
        ),
        array(
            'uri' => 'http://myuklocal.co.uk/categories/$1/$2/',
            'css_location_of_data_to_mine' => 'article.b-article>section>div',
            'selectors_to_save' => 'ul li>a',
            'to_be_narrowed_by_the_user' => false,
            'is_pagination_seq' => false,
            'storage' => array(
                array(
                    'crawl_type' => 2, // selector contains data of which type?
                    'results_storage_table' => 'categories'
                )
            ),
        ),
        array(
            'uri' => 'http://$3.myuklocal.co.uk/categories/$2/',
            'css_location_of_data_to_mine' => 'article.b-article>section>div',
            'selectors_to_save' => 'ul li.b-section__list-item',
            'to_be_narrowed_by_the_user' => false,
            'is_pagination_seq' => false,
            'storage' => array(
                array(
                    'crawl_type' => 16, // selector contains data of which type?
                    'results_storage_table' => 'crawl_data'
                )
            )
        ),
        array(
            'uri' => 'http://$3.myuklocal.co.uk/categories/$2/page-$4', //starting at 1, going up til there's a 404
            'css_location_of_data_to_mine' => 'article.b-article>section>div',
            'selectors_to_save' => 'ul li.b-section__list-item',
            'to_be_narrowed_by_the_user' => false,
            'is_pagination_seq' => true,
            'storage' => array(
                array(
                    'crawl_type' => 2, // selector contains data of which type?
                    'results_storage_table' => 'categories'
                ),
                array(
                    'crawl_type' => 16, // selector contains data of which type?
                    'results_storage_table' => 'crawl_data'
                )
            )
        )

    );

    public function __construct()
    {
        self::$expiry = time()+30*24*60*60;
    }

    private function loadPageIntoCache($url, $expiry=0)
    {
        $expiry = ($expiry==0) ? self::$expiry : $expiry;

    }

    private function readPageDataFromCache($url)
    {

    }

}