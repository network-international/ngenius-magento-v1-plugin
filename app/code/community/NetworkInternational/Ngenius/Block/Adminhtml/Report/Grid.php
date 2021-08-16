<?php

/**
 * Ngenius Grid Block
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Initialize Grid Block
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('ngenius_grid');
        $this->setDefaultSort('order_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for grid
     * @return NetworkInternational_Ngenius_Block_Adminhtml_Report_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('ngenius/standard_collection');
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare grid columns
     * @return NetworkInternational_Ngenius_Block_Adminhtml_Report_Grid
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('ngenius');

        $this->addColumn(
            'order_id',
            array(
            'header' => $helper->__('Order Id'),
            'index' => 'order_id',
            'width' => '100px'
            )
        );
        $this->addColumn(
            'amount',
            array(
            'header' => $helper->__('Amount'),
            'index' => 'amount',
            'type' => 'currency',
            'currency' => 'currency',
            'width' => '100px'
            )
        );
        $this->addColumn(
            'reference',
            array(
            'header' => $helper->__('Order Reference'),
            'index' => 'reference'
            )
        );
        $this->addColumn(
            'action',
            array(
            'header' => $helper->__('Payment Action'),
            'index' => 'action',
            'width' => '100px'
            )
        );
        $this->addColumn(
            'state',
            array(
            'header' => $helper->__('State'),
            'index' => 'state',
            'width' => '150px'
            )
        );
        $this->addColumn(
            'status',
            array(
            'header' => $helper->__('Order Status'),
            'index' => 'status'
            )
        );
        $this->addColumn(
            'payment_id',
            array(
            'header' => $helper->__('Payment Id'),
            'index' => 'payment_id'
            )
        );
        $this->addColumn(
            'captured_amt',
            array(
            'header' => $helper->__('Captured Amount'),
            'index' => 'captured_amt',
            'type' => 'currency',
            'currency' => 'currency',
            'width' => '100px'
            )
        );
        $this->addColumn(
            'created_at',
            array(
            'header' => $helper->__('Created At'),
            'type' => 'datetime',
            'index' => 'created_at',
            'width' => '150px'
            )
        );

        $this->addExportType('*/*/exportNgeniusCsv', $helper->__('CSV'));
        $this->addExportType('*/*/exportNgeniusExcel', $helper->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Return grid URL
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
