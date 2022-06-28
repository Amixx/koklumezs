<?php

use yii\helpers\Html;
?>
<div class="modal fade" id="moreLessons" tabindex="-1" role="dialog" aria-labelledby="moreLessonsLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="moreLessonsLabel"><?= \Yii::t('app', 'I want more tasks') ?></h5>
            </div>
            <div class="modal-body">
                <h3><?= \Yii::t('app', 'How difficult lesson you want') ?>?</h3>
                <?php if (isset($nextLessons['easy'])) { ?>
                    <?= Html::a(
                        '<h4>' . \Yii::t('app', 'Easier') . '</h4><p>' . $nextLessons['easy']->title . '</p>',
                        '',
                        [
                            'class' => 'btn btn-orange',
                            'data-method' => 'POST',
                            'data-params' => ['lessonId' => $nextLessons['easy']->id]
                        ]
                    ) ?>
                <?php }
                if (isset($nextLessons['medium'])) { ?>
                    <?= Html::a(
                        '<h4>' . \Yii::t('app', 'Just as complicated') . '</h4><p>' . $nextLessons['medium']->title . '</p>',
                        '',
                        [
                            'class' => 'btn btn-orange',
                            'data-method' => 'POST',
                            'data-params' => ['lessonId' => $nextLessons['medium']->id]
                        ]
                    ) ?>
                <?php }
                if (isset($nextLessons['hard'])) { ?>
                    <?= Html::a(
                        '<h4>' . \Yii::t('app', 'Challenge') . '</h4><p>' . $nextLessons['hard']->title . '</p>',
                        '',
                        [
                            'class' => 'btn btn-orange',
                            'data-method' => 'POST',
                            'data-params' => ['lessonId' => $nextLessons['hard']->id]
                        ]
                    ) ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>