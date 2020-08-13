# Customer indexer fix for magento 2.3

Customers data being reindexed each time when setup:upgrade command is running. Actual for magento 2.3.* (no need for 2.4) 
Install this module to your vendor

`composer require robolv/magento-customer-indexer-fix`

And after enable module in magento

`bin/magento mo:en Robo_CustomerIndexerFix`


Issue reported here: https://github.com/magento/magento2/issues/19469
