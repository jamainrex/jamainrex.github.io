<?php
require_once __DIR__ . '/../vendor/autoload.php';
    $list_of_directories = new \DirectoryCrawler\Controllers\B99CrawlerController();
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
        <h3>A directory crawler: STEP 1</h3>
        <p>Directories available for crawling (only active directories are enabled):</p>
        <ul><?php
            foreach ( $list_of_directories as $directory) {
                ?>
            <li><?php
                if (!empty($directory['active'])) {
                    echo ('<strong><a href="directory.php?dir=' . urlencode($directory['name']) . '">');
                }
                echo $directory['name'];
                if (!empty($directory['active'])) {
                    echo (': run crawler on this directory</a></strong>');
                }
                echo (' | <a href="'.$directory['url'].'" target="_blank">open '.$directory['name'].' directory in new window</a></li>');
            }
            ?>
        </ul>
    </div>
</div>
</body>

</html>