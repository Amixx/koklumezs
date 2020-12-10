<?php
$hasFiles = false;
foreach ($lecturefiles as $id => $file) {
    $path_info = pathinfo($file['file']);
    if (in_array(strtolower($path_info['extension']), $videos)) {
        $hasFiles = true;
    }
}
if ($hasFiles) {
?>
    <div class="row">
        <?php foreach ($lecturefiles as $id => $file) {
            $path_info = pathinfo($file['file']);
            if (!in_array(strtolower($path_info['extension']), $videos)) {
                continue;
            }
        ?>
            <div class="col-md-12">
                <p><?= $file['title'] ?></p>
                <video id="my-player<?= $id ?>" class="video-js vjs-layout-x-large vjs-big-play-centered" controls preload="auto" poster="<?= isset($thumbnail) && $thumbnail ? $thumbnail : '' ?>" data-setup='{}'>
                    <source src="<?= $file['file'] ?>" type="video/<?= strtolower($path_info['extension']) ?>">
                    </source>
                    <p class="vjs-no-js">
                        To view this video please enable JavaScript, and consider upgrading to a
                        web browser that
                        <a href="https://videojs.com/html5-video-support/" target="_blank">
                            supports HTML5 video
                        </a>
                    </p>
                </video>
                <script>
                    var player = videojs('my-player<?= $id ?>', {
                        responsive: true,
                        width: 400,
                        playbackRates: [0.5, 0.75, 1, 1.25, 1.5, 2]
                    });
                </script>
            </div>
            <hr />
        <?php } ?>
    </div>
    <hr />
<?php } ?>