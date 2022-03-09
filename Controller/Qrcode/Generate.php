<?php

namespace Trive\Fiskal\Controller\Qrcode;


use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class Generate extends \Magento\Framework\App\Action\Action
{
    const POREZNA_URL = 'https://porezna.gov.hr/rn/?zki=%s&datv=%s&izn=%s';

    /**
     * @inheritDoc
     */
    public function execute() {
        $request = $this->getRequest();

        $text = sprintf(
            self::POREZNA_URL,
            $request->getParam('zki'),
            $request->getParam('datv'),
            $request->getParam('izn')
        );

        $qrCode   = QrCode::create($text);
        $writer   = new PngWriter();
        $result   = $writer->write($qrCode);
        
        header('Content-Type: '.$result->getMimeType());
        echo $result->getString();
      
        die;
    }
}
