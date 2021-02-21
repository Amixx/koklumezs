<button type="button" class="btn btn-orange" data-toggle="modal" data-target="#alertEvaluation"><?= \Yii::t('app',  'Next lesson'); ?></button>
<div class="modal fade" id="alertEvaluation" tabindex="-1" role="dialog" aria-labelledby="alertEvaluationLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>  
                <h3 class="modal-title" id="alertEvaluationLabel"><?= \Yii::t('app',  'Hey! Please evaluate the lesson before moving on to the next one!'); ?></h3>
            </div>
            <div class="modal-body">
                <?= $this->render("amount-evaluation", [
                    'difficultyEvaluation' => $difficultyEvaluation,
                    'force' => $force,
                    'redirectToNext' => true,
                ]) ?>
            </div>
        </div>
    </div>
</div>