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
$crawlData = $_SESSION['crawlData'];

$crawler = $crawlData['crawler'];
$cat = $crawlData['cat'];

$domCrawler = $_SESSION['current_crawler'];

$domresult = $crawler->saveLinks($domCrawler);

$record_count = count($domresult);
$_SESSION['crawlData']['total_records'] = $record_count;
/*$response = json_encode(array(
    'directory_records_to_crawl' => $record_count,
    'message' => $record_count.' UK locations to crawl within this directory, each of which may contain more than one business.',
    'deeper_level_exists' => true
)); */
$response = $record_count.' UK locations to crawl within this directory, each of which may contain more than one business.<br/>loading...';
?>
<html>
<head>
</head>
<body><?php echo $response;?>

<script>
        var stuburl = "entries-stub.php?current_record=0";
        window.location = stuburl;

</script>

</body>
</html>
