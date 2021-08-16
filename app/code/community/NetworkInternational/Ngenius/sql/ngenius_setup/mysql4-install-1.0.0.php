<?php

$table = 'ngenius_networkinternational';

$installer = $this;
$installer->startSetup();
$installer->run(
        "
        DROP TABLE IF EXISTS `{$this->getTable($table)}`;	
        CREATE TABLE `{$this->getTable($table)}` (
             `nid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'n-genius Id',
             `entity_id` int(10) UNSIGNED NOT NULL COMMENT 'Entity Id',
             `order_id` varchar(55) NOT NULL COMMENT 'Order Id',
             `amount` decimal(12,4) UNSIGNED NOT NULL COMMENT 'Amount',
             `currency` varchar(3) NOT NULL COMMENT 'Currency',
             `reference` text NOT NULL COMMENT 'Reference',
             `action` varchar(20) NOT NULL COMMENT 'Action',
             `state` varchar(20) NOT NULL COMMENT 'State',
             `status` varchar(50) NOT NULL COMMENT 'Status',
             `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Created At',
             `payment_id` text NOT NULL COMMENT 'Payment Id',
             `captured_amt` decimal(12,4) UNSIGNED NOT NULL COMMENT 'Captured Amount',
             PRIMARY KEY (`nid`),
             UNIQUE KEY `NGENIUS_ONLINE_ENTITY_ID_ORDER_ID` (`entity_id`,`order_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='n-genius order table';
        "
);

$installer->endSetup();
