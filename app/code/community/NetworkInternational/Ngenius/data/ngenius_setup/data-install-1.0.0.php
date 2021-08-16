<?php

$installer = $this;
// Required tables
$statusTable = $installer->getTable('sales/order_status');
$statusStateTable = $installer->getTable('sales/order_status_state');
$stateName = NetworkInternational_Ngenius_Model_Standard::STATE;
$statusArr = NetworkInternational_Ngenius_Model_Standard::STATUS;

// Insert statuses
$installer->getConnection()->insertArray(
    $statusTable,
    array('status', 'label'),
    $statusArr
);
$dataArr = array();
foreach ($statusArr as $status) {
    $data['status'] = $status['status'];
    $data['state'] = $stateName;
    $data['is_default'] = 0;
    $dataArr[] = $data;
}
$dataArr[0]['is_default'] = 1;

// Insert states and mapping of statuses to states
$installer->getConnection()->insertArray(
    $statusStateTable,
    array('status', 'state', 'is_default'),
    $dataArr
);
