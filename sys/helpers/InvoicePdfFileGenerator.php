<?php

namespace app\helpers;

use kartik\mpdf\Pdf;

class InvoicePdfFileGenerator
{
    public static function generate($invoicePath, $content, $title){
        $css = self::getInvoiceCss();

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_FILE,
            'filename' => $invoicePath,
            'content' => $content,
            'cssInline' => $css,
            'options' => ['title' => $title],
        ]);

        $pdf->render();
    }

    public static function getInvoiceCss(){
        return '
            body {
                font-family: Arial, serif;
                color: rgb(0, 0, 0);
                font-weight: normal;
                font-style: normal;
                text-decoration: none
            }

            .bordered-table {
                width: 100%; border: 1px solid black;
                border-collapse:collapse;
            }

            .bordered-table td, th {
                border: 1px solid black;
                text-align:center;
            }

            .bordered-table th {
                font-weight:normal;
                padding:8px 4px;
            }

            .bordered-table td {
                padding: 32px 4px;
            }

            .font-l {
                font-size: 18px;
            }

            .font-m {
                font-size: 15px;
            }

            .font-s {
                font-size: 14px;
            }

            .font-xs {
                font-size: 13px;
            }

            .align-center {
                text-align:center;
            }

            .align-right {
                text-align:right;
            }

            .lh-2 {
                line-height:2;
            }

            .leftcol {
                width:140px;
            }

            .info {
                line-height:unset;
                margin-top:16px;
            }
        ';
    }
}
