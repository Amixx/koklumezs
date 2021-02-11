<?php
$hasFiles = false;
foreach ($lecturefiles as $id => $file) {
    $path_info = pathinfo($file['file']);
    if (str_contains($file['file'], "youtube") || in_array(strtolower($path_info['extension']), $videos)) {
        $hasFiles = true;
    }
}
if ($hasFiles) {
?>
    <div class="row">
        <?php foreach ($lecturefiles as $id => $file) {
            $path_info = pathinfo($file['file']);
            if (!str_contains($file['file'], "youtube") && !in_array(strtolower($path_info['extension']), $videos)) {
                continue;
            }

            $isYoutubeVideo = str_contains($file['file'], "youtube");
            $fileUrl = $file['file'];

            $dataSetup = $isYoutubeVideo 
                ? "{
                    \"techOrder\": [\"youtube\"],
                    \"sources\": [{ \"type\": \"video/youtube\",
                    \"src\": \"$fileUrl\"}]
                }"
                : "{}"
        ?>
        <div class="col-md-12">
            <p><?= $file['title'] ?></p>
            <video
                id="my-player<?= $idPrefix ?><?= $id ?>"
                class="video-js vjs-layout-x-large vjs-big-play-centered"
                controls
                preload="auto"
                poster="<?= isset($thumbnail) && $thumbnail ? $thumbnail : '' ?>"
                data-setup='<?= $dataSetup ?>'
            >
                <?php if(!$isYoutubeVideo){ ?>
                    <source
                        src="<?=  $fileUrl ?>"
                        type="video/<?= strtolower($path_info['extension']) ?>"
                    ></source>
                <?php } ?>
                
                <p class="vjs-no-js">
                    To view this video please enable JavaScript, and consider upgrading to a
                    web browser that
                    <a href="https://videojs.com/html5-video-support/" target="_blank">
                        supports HTML5 video
                    </a>
                </p>
            </video>
            
        </div>
         <script>
            var player = videojs('my-player<?= $idPrefix ?><?= $id ?>', {
                responsive: true,
                width: 400,
                playbackRates: [0.5, 0.75, 1, 1.25, 1.5, 2]
            });
        </script>        
        <?php } ?>
    </div>
<?php } ?>