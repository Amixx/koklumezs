<?php
$hasFiles = false;
foreach ($lecturefiles as $id => $file) {
    $path_info = pathinfo($file['file']);
    if (in_array(strtolower($path_info['extension']), $videos)) {
        $hasFiles = true;
    }
}
if ($hasFiles) {
    foreach ($lecturefiles as $id => $file) {
        $path_info = pathinfo($file['file']);
        if (!in_array(strtolower($path_info['extension']), $videos)) {
            continue;
        }
        ?>
    <?=$file['thumb'] ? $file['thumb'] : $baseUrl . '/files/cover.jpg'?>
<?php break;}
}?>