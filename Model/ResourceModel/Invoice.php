<?php
/**
 * Trive Fiskal API Library.
 *
 * @category  Trive
 * @package   Trive_Fiskal
 * @copyright 2017 Trive d.o.o (http://trive.digital)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link      http://trive.digital
 */

namespace Trive\Fiskal\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Invoice extends AbstractDb
{
	protected static $oldEid = false;
	protected $_logger  = false;
	
    /**
     * Invoice constructor.
     *
     * @param Context $context
     * @param null    $resourcePrefix
     */
    public function __construct(
        Context $context,
        $resourcePrefix = null,
    	\Psr\Log\LoggerInterface $logger
    ) {
        $this->context = $context;
        $this->resourcePrefix = $resourcePrefix;
        $this->_logger = $logger;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('trive_fiskal_invoice', 'invoice_id');
    }

    /**
     * Initialize unique fields
     *
     * @return \Trive\Fiskal\Model\ResourceModel\Invoice
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = [
            [
                'field' => ['entity_type', 'entity_id'],
                'title' => __('Fiskal Invoice for this entity'),
            ],
        ];

        return $this;
    }
    
    
    
    /**
     * Save object object data
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @throws \Exception
     * @throws AlreadyExistsException
     * @api
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)  {
    	//DODANO BY NewVibrations - Sumic  ZBOG NEKOGA RAZLOGA  FUNKCIJA SE POZIVA 2 PUTA i onda pukne jer vec postoji a transakcija ne zavrsi!!!!
    	try{
	    	$eid = $object->getEntityType(). $object->getEntityId();
		   	if (self::$oldEid != $eid){
	    		self::$oldEid = $eid;
	    		parent::save($object);
	    	}	
    	}catch (\Exception $e){
    		$this->_logger->alert('GRESKA PRLIKOM SPREMANJA FISKALIZACIJE', $object->toArray()); 
    		throw $e;
    	}
		return $this;
    }
}
