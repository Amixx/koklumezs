<div class="modal fade" id="alertEvaluation-<?= $idPostfix ?>" tabindex="-1" role="dialog" aria-labelledby="alertEvaluationLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="alertEvaluationLabel">
                    <?= \Yii::t('app',  'Hey! Please evaluate the lesson before moving on to the next one!'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <?= $this->render("amount-evaluation", [
                    'difficultyEvaluation' => $difficultyEvaluation,
                    'force' => $force,
                    'redirectLessonId' => $redirectLessonId,
                ]) ?>
            </div>
        </div>
    </div>
</div>