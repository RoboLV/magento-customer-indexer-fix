<?php
/**
 * @author    Rihards Stasans <torengo120@gmail.com>
 */

namespace Robo\CustomerIndexerFix\Setup;

use Exception;
use Magento\Customer\Setup\RecurringData as MagentoRecurringDataAlias;
use Magento\Framework\Indexer\StateInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Indexer\IndexerRegistry;

/**
 * Class RecurringData
 * @package Robo\CustomerIndexerFix\Setup
 *
 * Added bug fix for this https://github.com/magento/magento2/issues/19469
 * Customer indexer reindex on each setup upgrade
 * based on this https://github.com/magento/magento2/commit/0fd8a5146cdf4e524150e68f89085d90f0d42be3
 */
class RecurringData extends MagentoRecurringDataAlias
{
    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * RecurringData constructor.
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(
        IndexerRegistry $indexerRegistry
    ) {
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * Setup install
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($this->isNeedToDoReindex($setup)) {
            $indexer = $this->indexerRegistry->get(Customer::CUSTOMER_GRID_INDEXER_ID);
            $indexer->reindexAll();
        }
    }

    /**
     * Check is re-index needed
     *
     * @param ModuleDataSetupInterface $setup
     * @return bool
     */
    private function isNeedToDoReindex(ModuleDataSetupInterface $setup) : bool
    {
        return !$setup->tableExists('customer_grid_flat');
    }
}
