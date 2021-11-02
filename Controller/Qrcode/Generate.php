<?php

namespace Trive\Fiskal\Controller\Qrcode;

use Magento\Framework\App\Action\Context;
use Endroid\QrCode\QrCode;

class Generate extends \Magento\Framework\App\Action\Action
{
    const POREZNA_URL = 'https://porezna.gov.hr/rn/?zki=%s&datv=%s&izn=%s';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $request = $this->getRequest();

        $text = sprintf(
            self::POREZNA_URL,
            $request->getParam('zki'),
            $request->getParam('datv'),
            $request->getParam('izn')
        );

        $qrCode = new QrCode($text);
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
        die;
    }
}
