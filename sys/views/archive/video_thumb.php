<?php
$hasFiles = false;
foreach ($lecturefiles as $id => $file) {
    if (isset($file['file']) and !empty($file['file'])) {
        if(strpos($file['file'], "youtube") !== false){
            $hasFiles = true;
        }else{
            $path_info = pathinfo($file['file']);
            if (in_array(strtolower($path_info['extension']), $videos)) {
                $hasFiles = true;
            }
        }        
    }
}
if ($hasFiles) {
    foreach ($lecturefiles as $id => $file) {
        if (isset($file['file']) and !empty($file['file']) && strpos($file['file'], "youtube") === false) {
            $path_info = pathinfo($file['file']);
            if (!isset($path_info['extension']) or !in_array(strtolower($path_info['extension']), $videos)) {
                continue;
            }             
        }
?>
    <?= isset($file['thumb']) && $file['thumb'] ? $file['thumb'] : "" ?>
<?php break;
    }
} else {
    echo isset($file['thumb']) && $file['thumb'] ? $file['thumb'] : "";
}
