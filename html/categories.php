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
use DirectoryCrawler\Models\Directory;
use DirectoryCrawler\Models\Locations;
use DirectoryCrawler\Models\Entry;

// Scraper
use DirectoryCrawler\Scrapers\Categories;

// Filters
use DirectoryCrawler\Helpers\Filter;

$use_cached = ($_GET['crawl_action']=='show');
$this_directory = filter_input(INPUT_GET, 'dir');
$list_of_directories = new \DirectoryCrawler\Controllers\B99CrawlerController();
$classToLoad = $list_of_directories->getCrawlerControllerClassName($this_directory);
$directory_names = $list_of_directories->getDirectoryNames();
$list_of_directories = $list_of_directories->getDirectories();
$crawl_id=time();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Directory meta-crawler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <style>
        #statusbox {
            width: 600px;
            height: 400px;
            position: fixed;
            left: 50%;
            top: 0;
            border: 1px solid grey;
            border-radius: 5px;
            overflow: auto;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script>
        function addToBox(msg,clear) {
            clear = clear || false;
            if (clear) {
                document.getElementById("statusbox").innerHTML = msg;
            } else {
                document.getElementById("statusbox").innerHTML += msg;
            }
        }

        var hasMoreRecords = true;
        var winframe = document.getElementById('statusbox');
        var current_record =0;
        var all_records_processed = false;
        var records_increment = 10;

        function readDirectory(dir, dir_id, cat_id) {
            var stuburl = "readDirectory.php?dir=" + dir + "&dir_id=" + dir_id + "&cat_id=" + cat_id + "&crawl_id=" + <?php echo $crawl_id ?>;
            document.getElementById('statusbox').src = stuburl;
        }

    </script>
</head>
<body>

<iframe id="statusbox"></iframe>
<div class="container">
    <div class="row">
        <h1>Meta Crawler</h1>
        <h3>A directory crawler: STEP 3</h3>
        <p><?php


if (empty($this_directory)) {
    die('sorry you need to specify which directory to crawl');
}
if (!in_array($this_directory,$directory_names) || empty($classToLoad)) {
    die('sorry I don\'t know what the '.$this_directory.' crawler is, here are the crawlers I\'ve heard of:\r\n<br/>' . print_r(array_keys($directories),true));
}
$classToLoad = '\DirectoryCrawler\Controllers\\' . $classToLoad;
try {
    $crawler = new $classToLoad;
} catch (Exception $e) {
    die('sorry the ' . $classToLoad . ' class doesn\'t appear to have been defined yet (or at least I can\'t find it)');
}
if (empty($this_directory) || !in_array($this_directory,$directory_names) || empty($crawler)) {
    die('something odd has happened. Despite our best efforts we just can\'t display a listing here');
}
            $everything = $crawler->landingLetterLinks($use_cached);
            //var_dump( $everything );
            $data = $everything['ScrapedData']['match'];
            // $letterLinks = $everything['ScrapedData']['data'];
            $categories = $crawler->getAllCategoriesByLetterLink($data);

            $directory_model = new Directory();
            $dir_arr = $directory_model->getByName($this_directory);
            $dir_id = $dir_arr[0]['id'];
            $values = $crawler->updateCategoryRecords($categories,$dir_id);

            //echo '<pre>'.print_r($values,true).'</pre>';

?>Please select the category you wish to scrape:
            <?php
            $cats = $crawler->getCategories();
            //echo '<pre>'.print_r($cats,true).'</pre>';
            ?>
            <p>WARNING - crawling can take a very long time...</p>
                <?php
            foreach ($cats as $cat) {
                $checked = ($cat['include']=='y') ? ' checked="checked"' : '' ;
                /*
                 * <div class="radio"><label><input type="radio" name="cat" value="<?php echo ($cat['id']) ?>"<?php echo($checked) ?>><?php echo($cat['name']);?></div>
                 */
                $id = $cat['id'];
                $url = 'entries.php?' . http_build_query(
                        array(
                            'dir' => $this_directory,
                            'dir_id' => $dir_id,
                            'cat' => $id
                        )
                    );
                $temp = "'$this_directory',$dir_id,$id";
                ?>
                <div><a href="#!" onclick="readDirectory(<?php echo $temp ?>)"><?php echo($cat['name']);?></a></div>
            <?php
            }
            ?>
            <p>WARNING - crawling can take a very long time...</p>

</div>
</div>
</body>

</html>




