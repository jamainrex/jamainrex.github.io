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

if (empty($_SESSION)) {
    session_start();
}
$_inputDir = $this_directory = filter_input(INPUT_GET, 'dir');

$list_of_directories = new \DirectoryCrawler\Controllers\B99CrawlerController();
$classToLoad = $list_of_directories->getCrawlerControllerClassName($this_directory);
$directory_names = $list_of_directories->getDirectoryNames();
$list_of_directories = $list_of_directories->getDirectories();

$directoryModel = new \DirectoryCrawler\Models\Directory();
$categoryModel = new \DirectoryCrawler\Models\Category();

$dir_arr = $directoryModel->getByName($this_directory);
$dir_id = $dir_arr[0]['id'];

$crawl_id = $_GET['crawl_id'];
$_SESSION['crawl_id'] = $crawl_id;

$this_directory = filter_input(INPUT_GET, 'dir');
    $this_categories = $_GET['cat_id'];

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
$crawlData = array(
    'this_directory' => $this_directory,
    'classToLoad' => $classToLoad,
    'directory_names' => $directory_names,
    'list_of_directories' => $list_of_directories,
    'dir_id' => $dir_id,
    'crawl_id' => $crawl_id,
    'this_categories' => $this_categories,
    'crawler' => $crawler,
    '_directory' => $_directory,
    '_categories' => $_categories
);

$record_count = 0;
foreach ($_categories as $_category) {
    $output = 'url to crawl' . $_category['href'] . '<br />';
    $crawlData['cat'] = $_category;
    $crawlData['results'] = $crawler->saveLocationsPage( $_category['href'], true );

    $output .= count($crawlData['results']);

}
$_SESSION['crawlData'] = $crawlData;
?>
<html>
<head>

</head>
<body>
Crawling directory, please wait;

<script>
        var stuburl = "findLinks.php?dir=<?php echo $_GET['dir'] ?>&dir_id=<?php echo $_GET['dir_id'] ?>&cat_id=<?php echo $_GET['cat_id'] ?>&crawl_id=<?php echo $crawl_id ?>";
        window.location = stuburl;
        //document.getElementById('statusbox').onload = findSecondTierLinks(dir, dir_id, cat_id, 0);

</script>
</body>
</html>

