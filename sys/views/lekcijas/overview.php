<?php

use app\models\Lecturesfiles;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="container-fluid lectures-index">
    <div class="row">
        <?=
        $this->render('favouriteANDnew',[
            'Lectures' => $newLectures,
            'divTitle' => 'New lessons',
            'clickableTitle' => 'All new lessons',
            'type' => 'new',
            'emptyText' => 'Congratulations! You\'ve seen all new lessons',
            'videoThumb'=> $videoThumb,
            'videos' => $videos,
            'baseUrl' => $baseUrl
        ])?>
        <?=
        $this->render('favouriteANDnew',[
            'Lectures' => $favouriteLectures,
            'divTitle' => 'Favourite lessons',
            'clickableTitle' => 'All favourite lessons',
            'type' => 'favourite',
            'emptyText' => 'You have not added any lessons to this section yet. You can do this by marking in any lesson that you want to add it to this section.',
            'videoThumb'=> $videoThumb,
            'videos' => $videos,
            'baseUrl' => $baseUrl
        ])?>
    </div>
</div>