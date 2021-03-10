<?php
namespace app\helpers;

class ThumbnailHelper {
    public static function getThumbnailStyle($file, $thumb, $videos){
        $fileEmpty = !isset($file) || empty($file);
        $hasThumb = isset($thumb) && $thumb;
        if($fileEmpty || !$hasThumb) return "";

        $stylePrefix = "background-color: white; background-image: ";
        $style = $stylePrefix . "url($thumb)";

        $fileIsYoutube = strpos($file, "youtube") !== false;
        if($fileIsYoutube) return $style;

        $path_info = pathinfo($file);
        $fileIsVideo = isset($path_info['extension']) && in_array(strtolower($path_info['extension']), $videos);
        
        return $fileIsVideo ? $style : "";
    }
}
?>