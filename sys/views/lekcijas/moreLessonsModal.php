<?php
use yii\helpers\Html;
?>
<div>
    <button type="button" class="btn btn-orange btn-long" data-toggle="modal" data-target="#moreLessons">   
        Vēlos vel nodarbības
    </button>
    <div class="modal fade" id="moreLessons" tabindex="-1" role="dialog" aria-labelledby="moreLessonsLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <h5 class="modal-title" id="moreLessonsLabel"><?=\Yii::t('app', 'I want more tasks') ?></h5>
            
        </div>
        <div class="modal-body">
            <h3><?=\Yii::t('app', 'How difficult lesson you want')?>?</h3>
            <?php if (isset($nextLessons[0])) {?>
                <?= Html::a('<h4>'.\Yii::t('app', 'Easier').'</h4><p>'.$nextLessons[0]->title.'</p>','', ['class' => 'btn btn-orange', 'data-method' => 'POST', 'data-params' => ['lessonId' => $nextLessons[0]->id]]) ?>
            <?php }
            if (isset($nextLessons[1])) { ?>
                <?= Html::a('<h4>'.\Yii::t('app', 'Just as complicated').'</h4><p>'.$nextLessons[1]->title.'</p>','', ['class' => 'btn btn-orange', 'data-method' => 'POST', 'data-params' => ['lessonId' => $nextLessons[1]->id]]) ?>
            <?php }
            if (isset($nextLessons[2])) { ?>
                <?= Html::a('<h4>'.\Yii::t('app', 'Challenge').'</h4><p>'.$nextLessons[2]->title.'</p>','', ['class' => 'btn btn-orange', 'data-method' => 'POST', 'data-params' => ['lessonId' => $nextLessons[2]->id]]) ?>
            <?php } ?>
        </div>
        </div>
    </div>
    </div>
</div>