<?php
/**
 * @author    Rihards Stasans <torengo120@gmail.com>
 */

namespace Robo\CustomerIndexerFix\Model;

use Magento\Customer\Model\Customer as MagentoCustomer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\StateInterface;

/**
 * Class Customer
 * @package Robo\CustomerIndexerFix\Model
 *
 * Added bug fix for this https://github.com/magento/magento2/issues/19469
 * Customer indexer reindex on each setup upgrade
 * based on this https://github.com/magento/magento2/commit/0fd8a5146cdf4e524150e68f89085d90f0d42be3
 */
class Customer extends MagentoCustomer
{
    /**
     * @var IndexerInterface|null
     */
    private $indexer;

    /**
     * Micro-caching optimization
     *
     * @return IndexerInterface
     */
    private function getIndexer() : IndexerInterface
    {
        if ($this->indexer === null) {
            $this->indexer = $this->indexerRegistry->get(self::CUSTOMER_GRID_INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * Processing object after save data
     *
     * @return $this
     * @throws LocalizedException
     */
    public function afterSave()
    {
        $indexer = $this->indexerRegistry->get(self::CUSTOMER_GRID_INDEXER_ID);

        if ($this->getIndexer()->getState()->getStatus() === StateInterface::STATUS_VALID) {
            $this->_getResource()->addCommitCallback([$this, 'reindex']);
        }

        return parent::afterSave();
    }

    /**
     * Init indexing process after customer save
     *
     * @return void
     */
    public function reindex()
    {
        $this->getIndexer()->reindexRow($this->getId());
    }
}
