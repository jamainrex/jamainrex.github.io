<?php
require_once __DIR__ . '/../vendor/autoload.php';
// Crawler
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;
// Controller
use DirectoryCrawler\Controllers\DomCrawlerController;
use DirectoryCrawler\Controllers\B99CrawlerController;

// Model
use DirectoryCrawler\Models\CrawlerLink;
use DirectoryCrawler\Models\Category;
use DirectoryCrawler\Models\Locations;
use DirectoryCrawler\Models\Entry;

// Scraper
use DirectoryCrawler\Scrapers\Categories;

// Filters
use DirectoryCrawler\Helpers\Filter;
$this_directory = filter_input(INPUT_GET, 'dir');

$list_of_directories = new \DirectoryCrawler\Controllers\B99CrawlerController();
$classToLoad = $list_of_directories->getCrawlerControllerClassName($this_directory);
$directory_names = $list_of_directories->getDirectoryNames();
$list_of_directories = $list_of_directories->getDirectories();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Directory meta-crawler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <div class="row">
        <h1>Meta Crawler</h1>
        <h3>A directory crawler: STEP 2</h3>
        <p><?php

            if (empty($this_directory)) {
                die('sorry you need to specify which directory to crawl');
            }
            if (!in_array($this_directory,$directory_names)) {
                die('sorry I don\'t know what the '.$this_directory.' crawler is, here are the crawlers I\'ve heard of:\r\n<br/>' . print_r($directory_names,true));
            }
            $classToLoad = '\DirectoryCrawler\Controllers\\' . $classToLoad;
            try {
                $crawler = new $classToLoad;
            } catch (Exception $e) {
                die('sorry the \DirectoryCrawler\Controllers\\'.$directory_names[$this_directory].' class doesn\'t appear to have been defined yet (or at least I can\'t find it)');
            }
            if (empty($this_directory) || !in_array($this_directory,$directory_names) || empty($crawler)) {
                die('something odd has happened. Despite our best efforts we just can\'t display a listing here');
            }
            ?></p>
        <form action="categories.php" method="gets"><input type="hidden" name="dir" value="<?=$this_directory?>">
            <button name="crawl_action" value="show">SHOW CATEGORIES</button>
            <button name="crawl_action" value="read">DETECT CATEGORIES (slow)</button>
            </form>
    </div>
</div>
</body>

</html>

