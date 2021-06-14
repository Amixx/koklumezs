<?php if (!empty($lectureVideoFiles)) {
    $posters = [];
?>
    <div class="row">
        <?php foreach ($lectureVideoFiles as $id => $file) {
            $path_info = pathinfo($file['file']);
            $isYoutubeVideo = strpos($file['file'], "youtube") !== false;
            $fileUrl = $file['file'];
            $fileExt = !$isYoutubeVideo && isset($path_info['extension']) ? strtolower($path_info['extension']) : null;
            $playbackRates = "\"playbackRates\": [0.5, 0.75, 1, 1.25, 1.5, 2]";

            $videoId = $isYoutubeVideo ? substr(explode("?v=", $fileUrl)[1], 0, 11) : null;

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

            $playerId = "player_" . $idPrefix . $id;

            $posters[$playerId] = isset($thumbnail) && $thumbnail ? $thumbnail : '';

        ?>
            <div class="col-md-12">
                <h4 class="visible-xs video-title-mobile"><?= $file['title'] ?></h4>
                <?php if ($isYoutubeVideo) { ?>
                    <div class="video-container">
                        <div id="<?= $playerId ?>" data-plyr-provider="youtube" data-plyr-embed-id="<?= $videoId ?>" data-role="player"></div>
                    </div>
                    <p>
                        <?= Yii::t('app', 'If you have any problems playing video, you can watch it on Youtube') . ' ' ?><a target="_blank" href="<?= $fileUrl ?>"><?= Yii::t('app', 'here') . '!' ?></a>
                    </p>
                <?php } else { ?>
                    <video id="player" playsinline controls data-role="player">
                        <source src="<?= $fileUrl ?>" type="video/<?= $fileExt ?>" />
                    </video>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <script>
        if (typeof posters === 'undefined') posters = {};

        <?php foreach ($posters as $id => $poster) { ?>
            posters["<?= $id ?>"] = "<?= $poster ?>";
        <?php } ?>
    </script>
<?php } ?>