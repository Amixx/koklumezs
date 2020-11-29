<?php

use yii\helpers\Html;

$this->title = \Yii::t('app', 'FAQs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="difficulties-index">
    <div>
        <h3 class="row text-center hidden-xs">
            <div class="col-sm-6"><?= Yii::t("app", "Question") ?></div>
            <div class="col-sm-6"><?= Yii::t("app", "Answer") ?></div>
        </h3>
            <?php foreach($faqs as $faq){ ?>
            <div class="row Buj__Item">
                <div class="col-sm-6"><?= $faq["question"] ?>:</div>
                <div class="col-sm-6"><?= $faq["answer"] ?></div>
            </div>
        <?php } ?>   
    </div>    
    <hr>
    <div>
        <h3><?= Yii::t("app", "Still have unanswered questions?") ?></h3>
        <p><?= Yii::t("app", "Ask them to us!") ?></p>
        <form action="/sys/student-questions/create" method="post">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <textarea name="question-text" placeholder="Jūsu jautājums..." style="display:block; margin-bottom:8px;" cols="70" rows="5"></textarea>
            <?= Html::submitButton(\Yii::t('app',  'Submit'), ['class' => 'btn btn-success']) ?>
        </form>        
    </div>
</div>