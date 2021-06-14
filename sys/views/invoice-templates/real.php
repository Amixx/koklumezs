<?php

use app\models\PlanParts;

$divider = 1 + ($subplan['pvn_percent'] / 100);
$totalCost = $subplanCost * $months;
$priceWithoutPvn = number_format($totalCost / $divider, 2);
$pvnAmount = number_format($totalCost - $priceWithoutPvn, 2);
$payAmount = number_format($totalCost, 2);

$usePayer = isset($payer) && $payer && $payer['name'] && $payer['address'];

?>

<!DOCTYPE html>
<html lang="lv">

<head>
    <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
</head>

<body>
    <div>
        <div>
            <h3 class="align-center font-l"><strong><?= Yii::t('app', 'Invoice Nr.') . ' ' .  $number ?></strong></h3>
            <div class="font-xs align-right lh-2">
                <div><?= Yii::t('app', 'Invoice date') ?>: <span class="font-m"><?= $datePaid ?></span></div>
            </div>
            <hr>
            <table class="lh-2">
                <tr>
                    <td class="leftcol">
                        <?= Yii::t('app', 'Supplier') ?>:
                    </td>
                    <td>
                        <strong>
                            SIA Kokļu Mežs
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td class="leftcol">
                        <?= Yii::t('app', 'Registration Nr.') ?>:
                    </td>
                    <td>
                        44103120159
                    </td>
                </tr>
                <tr>
                    <td class="leftcol">
                        <?= Yii::t('app', 'PVN registration Nr.') ?>:
                    </td>
                    <td>
                        LV44103120159
                    </td>
                </tr>
                <tr>
                    <td class="leftcol">
                        <?= Yii::t('app', 'Legal address') ?>:
                    </td>
                    <td>
                        Jūras iela 21 - 11, Limbaži LV 4001
                    </td>
                </tr>
                <tr>
                    <td class="leftcol">
                        <?= Yii::t('app', 'Bank') ?>:
                    </td>
                    <td>
                        A/S Swedbank
                    </td>
                </tr>
                <tr>
                    <td class="leftcol">
                        <?= Yii::t('app', 'Account Nr.') ?>:
                    </td>
                    <td>
                        <strong>
                            LV32HABA0551046058921
                        </strong>
                    </td>
                </tr>
            </table>
            <hr>
            <table class="lh-2">
                <tr>
                    <td class="leftcol">
                        <?= Yii::t('app', 'Recipient') ?>:
                    </td>
                    <td>
                        <strong><?= $usePayer ? $payer['name'] : $fullName ?></strong>
                    </td>
                </tr>
                <?php if ($usePayer) { ?>
                    <?php if ($payer['personal_code']) { ?>
                        <tr>
                            <td class="leftcol">
                                <?= Yii::t('app', 'Personal code') ?>:
                            </td>
                            <td>
                                <?= $payer['personal_code'] ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($payer['registration_number']) { ?>
                        <tr>
                            <td class="leftcol">
                                <?= Yii::t('app', 'Registration number') ?>:
                            </td>
                            <td>
                                <?= $payer['registration_number'] ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($payer['pvn_registration_number']) { ?>
                        <tr>
                            <td class="leftcol">
                                <?= Yii::t('app', 'PVN registration number') ?>:
                            </td>
                            <td>
                                <?= $payer['pvn_registration_number'] ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($payer['address']) { ?>
                        <tr>
                            <td class="leftcol">
                                <?= Yii::t('app', 'Address') ?>:
                            </td>
                            <td>
                                <?= $payer['address'] ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($payer['bank']) { ?>
                        <tr>
                            <td class="leftcol">
                                <?= Yii::t('app', 'Bank') ?>:
                            </td>
                            <td>
                                <?= $payer['bank'] ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($payer['swift']) { ?>
                        <tr>
                            <td class="leftcol">
                                SWIFT:
                            </td>
                            <td>
                                <?= $payer['swift'] ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($payer['account_number']) { ?>
                        <tr>
                            <td class="leftcol">
                                <?= Yii::t('app', 'Account number') ?>:
                            </td>
                            <td>
                                <strong><?= $payer['account_number'] ?></strong>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td class="leftcol">
                            <?= Yii::t('app', 'Contacts') ?>:
                        </td>
                        <td>
                            <?= $email ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <table class="bordered-table">
                <tr>
                    <th scope="col"><?= Yii::t('app', 'Product title') ?></th>
                    <th scope="col"><?= Yii::t('app', 'Subtotal') . ' ' ?> (Eur)</th>
                    <th scope="col">PVN (<?= $subplan["pvn_percent"] ?>%)</th>
                    <th scope="col"><?= Yii::t('app', 'Total') . ' ' ?> (Eur)</th>
                </tr>
                <?php foreach ($subplanParts as $part) { ?>
                    <tr>
                        <td><?= $part['title'] ?></td>
                        <td><?= PlanParts::getPriceWithoutPvn($part['monthly_cost'], $subplan['pvn_percent'], $months) ?></td>
                        <td><?= PlanParts::getPvnAmount($part['monthly_cost'], $subplan['pvn_percent'], $months) ?></td>
                        <td><?= PlanParts::getPayAmount($part['monthly_cost'], $months) ?></td>
                    </tr>
                <?php } ?>
            </table>
            <div class="lh-2 align-right">
                <div class="font-s"><?= Yii::t('app', 'Subtotal') . ' ' . $priceWithoutPvn ?></div>
                <div class="font-xs">PVN (Eur) <?= Yii::t('app', '') . ' ' . $pvnAmount ?></div>
                <div class="font-s"><strong><?= Yii::t('app', 'Total price') . ' (Eur) ' . $payAmount ?></strong></div>
            </div>
            <div class="lh-2">
                <div><?= Yii::t('app', 'Written by') ?>: <u>Laura Laugale (<?= Yii::t('app', 'Name') ?>, <?= ' ' . Yii::t('app', 'Surname') ?>)</u>
                </div>
                <div><u><?= $datePaid ?></u></div>
                <div class="font-xs"><?= Yii::t('app', 'Invoiced electronically and valid without signature') . '.' ?></div>
            </div>
        </div>
</body>

</html>