<?php

if (!function_exists('getYoutubeVideoId')) {
    function isLongYtVidLink($fileUrl)
    {
        return strpos($fileUrl, "youtube") !== false;
    }

    function isShortYtVidLink($fileUrl)
    {
        return strpos($fileUrl, "youtu.be") !== false;
    }

    function isYtVidLink($fileUrl)
    {
        return isLongYtVidLink($fileUrl) || isShortYtVidLink($fileUrl);
    }

    function getYoutubeVideoId($fileUrl)
    {
        if (isLongYtVidLink($fileUrl)) {
            return substr(explode("?v=", $fileUrl)[1], 0, 11);
        }

        if (isShortYtVidLink($fileUrl)) {
            return substr(explode("youtu.be/", $fileUrl)[1], 0, 11);
        }

        return null;
    }
}

$path_info = pathinfo($fileUrl);
$isYoutubeVideo = isYtVidLink($fileUrl);

$fileExt = !$isYoutubeVideo && isset($path_info['extension']) ? strtolower($path_info['extension']) : null;
$playbackRates = "\"playbackRates\": [0.5, 0.75, 1, 1.25, 1.5, 2]";

$videoId = getYoutubeVideoId($fileUrl);

$playerId = "player_" . $id;

$posters[$playerId] = isset($thumbnail) && $thumbnail ? $thumbnail : '';
?>

<div>
    <?php if ($isYoutubeVideo) { ?>
        <div class="video-container">
            <div id="<?= $playerId ?>"
                 data-plyr-provider="youtube"
                 data-plyr-embed-id="<?= $videoId ?>"
                 data-role="player"></div>
        </div>
    <?php } else { ?>
        <video id="player" playsinline controls data-role="player">
            <source src="<?= $fileUrl ?>" type="video/<?= $fileExt ?>"/>
        </video>
    <?php } ?>
</div>

<script>
    if (typeof posters === 'undefined') posters = {};

    <?php foreach ($posters as $id => $poster) { ?>
    posters["<?= $id ?>"] = "<?= $poster ?>";
    <?php } ?>
</script>