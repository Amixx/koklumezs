<?php

$dateToday = Date("d.m.yy.");
$payDate = date_create($dateToday);
date_add($payDate, date_interval_create_from_date_string("20 days"));
$payDateString = date_format($payDate, "d.m.yy.");

$payAmount = $subplan['pay_amount'] * $subplan['discount'] / 100;

?>

<html>

<head>
    <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
</head>

<body>
    <div>
        <div>
            <h3 class="align-center font-l"><strong>Rēķins Nr. <?= $id ?></strong> </h3>
            <div class="font-xs align-right lh-2">
                <div>Rēķina datums: <span class="font-m"><?= $dateToday ?></span></div>
                <div>Apmaksāt līdz: <span class="font-m"><?= $payDateString ?></span></div>
                <div>Apmaksas termiņš: <span class="font-m">20 dienu laikā</span></div>
            </div>
            <hr>
            <table class="lh-2">
                <tr>
                    <td class="leftcol">
                        Piegādātājs:
                    </td>
                    <td>
                        <strong>
                            SIA Kokļu Mežs
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td class="leftcol">
                        Reģistrācijas Nr.:
                    </td>
                    <td>
                        44103120159
                    </td>
                </tr>
                <tr>
                    <td class="leftcol">
                        Juridiskā adrese:
                    </td>
                    <td>
                        Jūras iela 21 - 11, Limbaži LV 4001
                    </td>
                </tr>
                <tr>
                    <td class="leftcol">
                        Banka:
                    </td>
                    <td>
                        A/S Swedbank
                    </td>
                </tr>
                <tr>
                    <td class="leftcol">
                        Konta Nr.:
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
                        Saņēmējs:
                    </td>
                    <td>
                        <strong><?= $fullName ?></strong>
                    </td>
                </tr>
                <tr>
                    <td class="leftcol">
                        Kontakti:
                    </td>
                    <td>
                        <?= $email ?>
                    </td>
                </tr>
            </table>
            <table class="bordered-table">
                <tr>
                    <th>Nosaukums</th>
                    <th>Cena (Eur)</th>
                    <th>Atlaide (%)</th>
                    <th>PVN (%)</th>
                    <th>Summa (Eur)</th>
                </tr>
                <tr>
                    <td><?= $subplan['name'] ?></td>
                    <td><?= $subplan['pay_amount'] ?></td>
                    <td><?= $subplan['discount'] ?></td>
                    <td></td>
                    <td><?= $payAmount ?></td>
                </tr>
            </table>
            <div class="lh-2 align-right">
                <div class="font-s">Summa bez PVN (Eur) <?= $payAmount ?></div>
                <div class="font-xs">PVN (Eur) 0,00</div>
                <div class="font-s"><strong>Summa apmaksai (Eur) <?= $payAmount ?></strong></div>
                <!-- <div class="font-xs">Apmaksas summa vārdiem: viens simts piecdesmit eiro un nulle centi</div> -->
            </div>
            <div class="lh-2">
                <div>Sastādīja: <u>Laura Laugale (vārds, uzvārds)</u>
                </div>
                <div><u><?= $dateToday ?></u></div>
                <div class="font-xs">Rēķins sagatavots elektroniski un ir derīgs bez paraksta.</div>
            </div>
        </div>
</body>

</html>