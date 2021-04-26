<?php if (!empty($lectureVideoFiles)) { ?>
    <div class="row">
        <?php foreach ($lectureVideoFiles as $id => $file) {
            $path_info = pathinfo($file['file']);
            $isYoutubeVideo = strpos($file['file'], "youtube") !== false;
            $fileUrl = $file['file'];
            $fileExt = !$isYoutubeVideo && isset($path_info['extension']) ? strtolower($path_info['extension']) : null;
            $playbackRates = "\"playbackRates\": [0.5, 0.75, 1, 1.25, 1.5, 2]";

            $dataSetup = $isYoutubeVideo
                ? "{
                    \"techOrder\": [\"youtube\"],
                    \"sources\": [{
                        \"type\": \"video/youtube\",
                        \"src\": \"$fileUrl\"
                    }],
                    $playbackRates
                }"
                : "{
                    \"sources\": [{
                        \"type\": \"video/$fileExt\",
                        \"src\": \"$fileUrl\"
                    }],
                    $playbackRates
                }";

            $poster = isset($thumbnail) && $thumbnail ? $thumbnail : '';

            $playerId = "player_" . $idPrefix . $id;
        ?>
            <div class="col-md-12">
                <h4 class="visible-xs video-title-mobile"><?= $file['title'] ?></h4>
                <video id="<?= $playerId ?>" class="video-js vjs-layout-x-large vjs-big-play-centered" controls preload="auto" poster="<?= $poster ?>" data-setup='<?= $dataSetup ?>'>
                    <p class="vjs-no-js">
                        To view this video please enable JavaScript, and consider upgrading to a
                        web browser that
                        <a href="https://videojs.com/html5-video-support/" target="_blank">
                            supports HTML5 video
                        </a>
                    </p>
                </video>
            </div>
        <?php } ?>
    </div>
<?php } ?>