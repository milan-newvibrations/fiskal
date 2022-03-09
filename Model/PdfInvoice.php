<?php
namespace Trive\Fiskal\Model;


use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Writer\PngWriter;

class PdfInvoice extends \Magento\Sales\Model\Order\Pdf\Invoice{

	const POREZNA_URL = 'https://porezna.gov.hr/rn/?zki=%s&datv=%s&izn=%s';

	/**
     * @var \Trive\Fiskal\Model\InvoiceManagement
     */
    private $invoiceManagement;
    
    public function __construct(
    	\Magento\Payment\Helper\Data $paymentData,
    	\Magento\Framework\Stdlib\StringUtils $string,
    	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    	\Magento\Framework\Filesystem $filesystem,
    	\Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
    	\Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
    	\Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
    	\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
    	\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
    	\Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
    	\Magento\Store\Model\StoreManagerInterface $storeManager,
    	\Magento\Store\Model\App\Emulation $appEmulation,
    	\Trive\Fiskal\Model\InvoiceManagement $invoiceManagement,
    	array $data = []
    ) {
    	$this->invoiceManagement = $invoiceManagement;
    	parent::__construct($paymentData, $string, $scopeConfig, $filesystem, $pdfConfig, $pdfTotalFactory, $pdfItemsFactory, $localeDate,	$inlineTranslation, $addressRenderer, $storeManager, $appEmulation, $data);
    }
   
  	
	protected function insertTotals($page, $source){
		parent::insertTotals($page, $source);
		$this->InsertFiskalInfo($page, $source);
		return $page;
		
	}
	
	public function InsertFiskalInfo( $page, $invoice) {
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

        $this->y -= 20;
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

        $page = $this->drawLineBlocks($page, [$lineBlock]);
        
        $text = sprintf(
            self::POREZNA_URL,
            $fiskalInvoice->getZki(),
            date('Ymd_Hi', strtotime($fiskalInvoice->getFiskalDateTime())),
            number_format($invoice->getBaseGrandTotal(), 2, '', '')
        );
       
        $filename = tempnam('', 'qrcode-pdf') .'.png';
        $qrCode   = QrCode::create($text);
        $writer   = new PngWriter();
		$writer->write($qrCode)->saveToFile($filename);
        $image = \Zend_Pdf_Image::imageWithPath($filename);
        $page->drawImage($image, 30, $this->y, 130, $this->y + 100);
    }
    

}
