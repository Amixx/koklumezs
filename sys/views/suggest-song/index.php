<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = \Yii::t('app', 'Suggest a song');

?>
<div class="container-fluid" style="text-align: center;">
    <?php if (!$isTeacher) {?>
    <div class="row">
        <div class="col-12">
            <h4 class="LectureOverview__Title"><?= \Yii::t('app', 'Suggest a song you want to learn');?>!</h4>
            <form action="/suggest-song/create" method="post">
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                <textarea name="song-name" placeholder="Dziesmas nosaukums..." class="suggest-song-textarea" cols="70" rows="2"></textarea>
                <?= Html::submitButton(\Yii::t('app',  'Submit'), ['class' => 'btn btn-success']) ?>
            </form>
        </div>
    </div>
    <div>
        <h2> <?= \Yii::t('app', 'Suggest a song') ?>! </h2>
        <?= GridView::widget([
            'dataProvider' => $suggestionsDataProvider,
            'layout' => '{items}{pager}',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'song',
                'times_suggested',
                [   
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{vote}',
                    'header' => \Yii::t('app', 'Vote'),
                    'buttons' => [
                        'vote' => function() {
                            return Html::a('<span class="glyphicon glyphicon-thumbs-up"></span>', '',['class' => 'btn btn-orange']);
                        }
                    ],
                ],
            ],
        ]);?>
    </div>
    <?php } else { ?>
        <div>
            <h2> <?= \Yii::t('app', 'Suggest a song') ?>! </h2>
            <?= GridView::widget([
                'dataProvider' => $suggestionsDataProvider,
                'layout' => '{items}{pager}',
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'song',
                    'times_suggested',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete}',
                    ],
                ],
            ]);?>
        </div>
    <?php } ?>
</div>