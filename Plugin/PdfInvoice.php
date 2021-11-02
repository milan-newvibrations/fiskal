<?php

namespace Trive\Fiskal\Plugin;

use Magento\Sales\Model\Order\Pdf\Invoice as OriginalPdfInvoice;

class PdfInvoice
{

    /**
     * @var \Trive\Fiskal\Model\InvoiceManagement
     */
    private $invoiceManagement;

    public function __construct(
        \Trive\Fiskal\Model\InvoiceManagement $invoiceManagement
    ) {
        $this->invoiceManagement = $invoiceManagement;
    }

    public function beforeInsertFiskalInfo(OriginalPdfInvoice $subject, $page, $invoice) {
        $fiskalInfos = [];
        $fiskalInvoice = $this->invoiceManagement->getFiskalInvoice($invoice->getId());
        if (
            !$fiskalInvoice
            || !$fiskalInvoice->getZki()
            || !$fiskalInvoice->getJir()
        ) {
            return;
        }

        $fiskalInfos[] = [
            'label' => 'Broj računa',
            'value' => $fiskalInvoice->getInvoiceNumber()
        ];
        $fiskalInfos[] = [
            'label' => 'Vrijeme izdavanja računa',
            'value' => $fiskalInvoice->getFiskalDateTime()
        ];
        $fiskalInfos[] = [
            'label' => 'ZKI',
            'value' => $fiskalInvoice->getZki()
        ];
        $fiskalInfos[] = [
            'label' => 'JIR',
            'value' => $fiskalInvoice->getJir()
        ];

        $subject->y -= 20;
        $lineBlock = ['lines' => [], 'height' => 15];
        foreach ($fiskalInfos as $fiskalInfo) {
            $lineBlock['lines'][] = [
                [
                    'text' => $fiskalInfo['label'] . ':',
                    'feed' => 375,
                    'align' => 'right',
                    'font' => 'bold',
                ],
                [
                    'text' => $fiskalInfo['value'],
                    'feed' => 565,
                    'align' => 'right'
                ]
            ];
        }

        $page = $subject->drawLineBlocks($page, [$lineBlock]);

    }

}
