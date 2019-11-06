<?php

use app\models\Lecturesfiles;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LecturesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lekcijas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lectures-index">
    
        <div class="row">
        <?php foreach ($models as $model) {
        $lecturefiles = Lecturesfiles::getLectureFiles($model->id);
        ?>
            <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                <a class="lecture-thumb" href="<?=Url::to(['lekcijas/lekcija', 'id' => $model->id])?>" style="background-image: url('<?=trim($this->render('video_thumb', ['lecturefiles' => $lecturefiles, 'videos' => $videos, 'baseUrl' => $baseUrl]))?>');"></a>
                <?=$model->title?>
            </div>
        <?php } ?>        
        </div>
    <?php
    // display pagination
    echo LinkPager::widget([
        'pagination' => $pages,
    ]); ?>
</div>