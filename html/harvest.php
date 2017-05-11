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

$current_entry = $_GET['current_entry'];
$crawl_id = $_SESSION['crawl_id'];
$results = $crawler->scrapeCrawledData($current_entry, $crawl_id);

//$_SESSION['crawlData']['total_records'] = $record_count;
$response = array(
    'message' => '<h3>Harvesting Emails</h3><br/>Total Records: ' . $_SESSION['crawlData']['total_records'] .'<br/>' .
        'Processing records in batches, current entry: ' . $current_entry . '<br/>' .
        'Total entries generated: ' . $_SESSION['current_records_created'] . '<br/>' .
		'<p><a target="_blank" href="download.php?crawl_id=' . $crawl_id . '">Download incomplete data saved so far for this crawl.</a></p>' .
        '<br/>currently processing: ' . print_r($results,true),
    'deeper_level_exists' => true
);
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
<body><?php echo $response['message'] ;?>
<?php
if (!empty($results) || $current_entry<$_SESSION['current_records_created']) {
    $current_entry++;
    ?>
    <script>
        var stuburl = "harvest.php?current_entry=<?php echo $current_entry; ?>";
        window.location = stuburl;
    </script>
<?php
} else {
?>
    <div>
        <div>
            <p>Harvesting complete</p>
            <p><a href="download.php?crawl_id=<?php echo $crawl_id ?>">Download Results</a></p>
        </div>
    </div>

    <?php
}?>
</body>

</html>




