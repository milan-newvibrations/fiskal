<?php

namespace Trive\Fiskal\Model;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Trive\Fiskal\Api\Data\InvoiceInterface;
use Trive\Fiskal\Api\InvoiceRepositoryInterface;

class InvoiceManagement
{
    /**
     * @param InvoiceRepositoryInterface $fiskalInvoiceRepository
     */
    protected $fiskalInvoiceRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Store config
     *
     * @var Config
     */
    protected $config;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        InvoiceRepositoryInterface $fiskalInvoiceRepository,
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->fiskalInvoiceRepository = $fiskalInvoiceRepository;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Get fiskal invoice by identifier
     *
     * @todo Make this configurable
     * @param int $identifier
     * @return InvoiceInterface[]
     */
    public function getFiskalInvoice($identifier)
    {
        $this->searchCriteriaBuilder->addFilter(
            InvoiceInterface::ENTITY_TYPE,
            InvoiceInterface::ENTITY_TYPE_INVOICE
        )->addFilter(
            InvoiceInterface::ENTITY_ID,
            $identifier
        )->addFilter(
            InvoiceInterface::SYNCED_AT,
            null,
            'notnull'
        );

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->fiskalInvoiceRepository->getList($searchCriteria)->getItems();
        $fiskalInvoice = array_shift($searchResults);

        return $fiskalInvoice;
    }
}
