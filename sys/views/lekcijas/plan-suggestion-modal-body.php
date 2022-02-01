<?php

use yii\helpers\Html;

?>

<div class="text-center">
    <ul class="PlanSuggestion__Container">
        <li class="PlanSuggestion">
            <div class="PlanSuggestion__Header">1 mēnesis</div>
            <div class="PlanSuggestion__Description">Darbojies mēnesi pa mēnesim, pauzē, kad vēlies</div>
            <div class="PlanSuggestion__Price">Mēneša maksa (eiro): 35</div>
            <div class="PlanSuggestion__Savings">Ietaupi līdz: 92 eiro gadā</div>
            <button class="btn btn-success PlanSuggestion__CheckoutButton">Izvēlēties</button>
        </li>
        <li class="PlanSuggestion">
            <div class="PlanSuggestion__Header">1 mēnesis</div>
            <div class="PlanSuggestion__Description">Darbojies mēnesi pa mēnesim, pauzē, kad vēlies</div>
            <div class="PlanSuggestion__Price">Mēneša maksa (eiro): 35</div>
            <div class="PlanSuggestion__Savings">Ietaupi līdz: 92 eiro gadā</div>
            <div class="form-group PlanSuggestion__PaymentCheckbox">
                <label class="control-label">
                    <input type="checkbox" name="payment_all_at_once">&nbsp;<?= Yii::t('app', 'I will pay in one installment (10% discount)') ?>
                </label>
            </div>
            <button class="btn btn-success PlanSuggestion__CheckoutButton">Izvēlēties</button>
        </li>
        <li class="PlanSuggestion">
            <div class="PlanSuggestion__Header">1 mēnesis</div>
            <div class="PlanSuggestion__Description">Darbojies mēnesi pa mēnesim, pauzē, kad vēlies</div>
            <div class="PlanSuggestion__Price">Mēneša maksa (eiro): 35</div>
            <div class="PlanSuggestion__Savings">Ietaupi līdz: 92 eiro gadā</div>
            <button class="btn btn-success PlanSuggestion__CheckoutButton">Izvēlēties</button>
        </li>
        <li class="PlanSuggestion">
            <div class="PlanSuggestion__Header">1 mēnesis</div>
            <div class="PlanSuggestion__Description">Darbojies mēnesi pa mēnesim, pauzē, kad vēlies</div>
            <div class="PlanSuggestion__Price">Mēneša maksa (eiro): 35</div>
            <div class="PlanSuggestion__Savings">Ietaupi līdz: 92 eiro gadā</div>
            <button class="btn btn-success PlanSuggestion__CheckoutButton">Izvēlēties</button>
        </li>
    </ul>
</div>