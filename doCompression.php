<?php

require_once 'ImgCompressor.php';
require_once 'CompressImg.php';
require_once 'ScanDir.php';


$options = array();
$options['original_dirname'] = 'compressed';
$options['compressed_dirname'] = 'original';
$options['project_dir'] = __DIR__;

$compressImg = new CompressImg($options);

$original_dir = __DIR__ . '/original' ;

$file_ext = array(
    "jpg",
    "png"
);

$files = scanDir::scan($original_dir, $file_ext, true);

echo '<pre>';
print_r($files);
echo '</pre>';



foreach ($files as $key => $file) {  
    // if ($file !== '/www/testriana/optimimg/maisonimg/original/blockcart/img/icon/basket_go.png' )  continue;
    $compressImg->compressImgFilter($file, $jpgQuality = 2); // png is 9 in ImgCompressor
}