<?php

$downloads_folder = 'downloads';

if (!file_exists($downloads_folder)) {
    echo 'Creating Downloads folder...' . PHP_EOL . PHP_EOL;
    mkdir($downloads_folder);
}

$html = file_get_contents("https://www.amigaremix.com/remixes/");

$dom_document = new DOMDocument();
@$dom_document->loadHTML($html);

$optionTags = $dom_document->getElementsByTagName('option');
for ($i = 0; $i < $optionTags->length; $i++ ) {
    $total_pages = ($optionTags->item($i)->nodeValue);
}

echo $total_pages . ' found.' . PHP_EOL . PHP_EOL;

for ($i = 1; $i <= $total_pages; $i++) {
    echo 'Processing page ' . $i . ' of ' . $total_pages . PHP_EOL . PHP_EOL;
    $html = file_get_contents('https://www.amigaremix.com/remixes/' . $i);

    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $xpath = new DOMXPath($dom);
    $hrefs = $xpath->evaluate("/html/body//a");

    for ($i = 0; $i < $hrefs->length; $i++) {
        $href = $hrefs->item($i);
        $url = $href->getAttribute('href');

        if (substr($url, -4) === '.mp3') {
            $filename = basename($url);
            $folder = basename(str_replace($filename, '', $url));
            $final_filename = $folder . ' - ' . urldecode($filename);
            if (!file_exists($downloads_folder . '/' . $final_filename)) {
                echo 'Downloading ' . $final_filename . ' ...' . PHP_EOL;
                file_put_contents($downloads_folder . '/' . $final_filename, fopen('https://www.amigaremix.com/listen/' . $folder . '/' . urlencode(urldecode($filename)), 'r'));
            } else {
                echo $final_filename . ' already exists, skipping ...' . PHP_EOL;
            }
        }
    }
}
