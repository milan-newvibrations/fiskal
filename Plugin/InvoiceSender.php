<?php

namespace Trive\Fiskal\Plugin;

use Magento\Sales\Model\Order\Email\Sender\InvoiceSender as OriginalInvoiceSender;
use Magento\Sales\Model\Order\Invoice;

class InvoiceSender {

    /**
     * @var \Trive\Fiskal\Model\InvoiceManagement
     */
    private $invoiceManagement;

    public function __construct(
        \Trive\Fiskal\Model\InvoiceManagement $invoiceManagement
    ) {
        $this->invoiceManagement = $invoiceManagement;
    }

    /**
     * Avoid sending invoice emails without fiskal invoice
     *
     * @param OriginalInvoiceSender $subject
     * @param callable $proceed
     * @param Invoice $invoice
     * @param false $forceSyncMode
     * @return bool
     */
    public function aroundSend(
        OriginalInvoiceSender $subject,
        callable $proceed,
        Invoice $invoice,
        $forceSyncMode = false
    ) {
        $fiskalInvoice = $this->invoiceManagement->getFiskalInvoice($invoice->getId());
        if (
            $fiskalInvoice
            && $fiskalInvoice->getZki()
            && $fiskalInvoice->getJir()
        ) {
            return $proceed($invoice, $forceSyncMode);
        }
        return false;
    }
}
