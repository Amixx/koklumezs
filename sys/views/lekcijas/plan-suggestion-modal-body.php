<div class="text-center">
    <ul class="PlanSuggestion__Container">
        <?php foreach ($planRecommendations as $plan) { ?>
            <li class="PlanSuggestion">
                <div class="PlanSuggestion__Header"><?= $plan->name ?></div>
                <?php if ($plan->description) { ?>
                    <div class="PlanSuggestion__Description"><?= $plan->description ?></div>
                <?php } ?>
                <div class="PlanSuggestion__Price">Mēneša maksa (eiro): <?= $plan->price() ?></div>
                <div class="PlanSuggestion__Savings">Ietaupi līdz: 92 eiro gadā</div>
                <?php if ($plan->allow_single_payment) { ?>
                    <div class="form-group PlanSuggestion__PaymentCheckbox">
                        <label class="control-label">
                            <input type="checkbox" name="payment_all_at_once">&nbsp;<?= Yii::t('app', 'I will pay in one installment (10% discount)') ?>
                        </label>
                    </div>
                <?php } ?>
                <button class="btn btn-success PlanSuggestion__CheckoutButton">Izvēlēties</button>
            </li>
        <?php } ?>
    </ul>
</div>