<?php
set_time_limit(0);

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

$_inputDir = $this_directory = filter_input(INPUT_GET, 'dir');
$list_of_directories = new \DirectoryCrawler\Controllers\B99CrawlerController();
$classToLoad = $list_of_directories->getCrawlerControllerClassName($this_directory);
$directory_names = $list_of_directories->getDirectoryNames();
$list_of_directories = $list_of_directories->getDirectories();

$directoryModel = new \DirectoryCrawler\Models\Directory();
$categoryModel = new \DirectoryCrawler\Models\Category();

$dir_arr = $directoryModel->getByName($this_directory);
$dir_id = $dir_arr[0]['id'];

$crawl_id = time();
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
        <h3>A directory crawler: STEP 4</h3>
        <p><?php

            $this_directory = filter_input(INPUT_GET, 'dir');
            $this_categories = $_GET['cat'];

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
                die('sorry the \DirectoryCrawler\Controllers\\'.$classToLoad.' class doesn\'t appear to have been defined yet (or at least I can\'t find it)');
            }
            if (empty($this_directory) || !in_array($this_directory,$directory_names) || empty($crawler)) {
                die('something odd has happened. Despite our best efforts we just can\'t display a listing here');
            }

            $_directory = $directoryModel->getByName( $this_directory );

            $_categories = $crawler->getCategoriesByDirIdAndCatId( $_directory[0]['id'], $this_categories );

            $record_count = 0;
            foreach ($_categories as $_category) {
                echo 'url to crawl' . $_category['href'] . '<br />';

                $crawlData = $crawler->getBusinessesFromLocationsByCategory( $_category['href'] );

                $record_count += count($crawlData['results']);

                //Insert Crawldata
                $crawler->updateCrawlDataRecord( $crawlData['crawlBusinessData'], $crawl_id );
            }

            ?>
        <p>Data has been collected: <?php echo $record_count ?> records</p>
        <p>The email addresses must now be harvested (this means visiting every website in the records and recording email addresses)</p>
        <p>This is not a quick process</p>
        <form action="harvest.php">
            <input type="hidden" name="dir" value="<?php echo $this_directory; ?>">
            <input type="hidden" name="dir_id" value="<?php echo $dir_id; ?>">
            <input type="hidden" name="crawl_id" value="<?php echo $crawl_id; ?>">
            <input type="submit" value="Begin email harvesting">
            <?php
//            $crawlBusinessesData = $crawler->getAllCrawlData( $crawl_id);
  //          echo '<pre>'.print_r($crawlBusinessesData,true).'</pre>';
            ?>
        </form>

        </p>
    </div>
</div>
</body>

</html>




