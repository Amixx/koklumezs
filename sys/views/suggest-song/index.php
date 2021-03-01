<?php
use yii\helpers\Html;
$this->title = \Yii::t('app', 'Suggest a song');

?>
<div class="container-fluid" style="text-align: center;">
    <div class="row">
        <div class="col-12">
            <h4 class="LectureOverview__Title"><?= \Yii::t('app', 'Suggest a song you want to learn');?>!</h4>
            <form action="/sys/suggest-song/create" method="post">
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                <textarea name="song-name" placeholder="Dziesmas nosaukums..." class="suggest-song-textarea" cols="70" rows="2"></textarea>
                <?= Html::submitButton(\Yii::t('app',  'Submit'), ['class' => 'btn btn-success']) ?>
            </form>
        </div>
    </div>
</div>