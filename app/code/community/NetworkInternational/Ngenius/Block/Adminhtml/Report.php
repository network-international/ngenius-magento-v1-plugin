<?php

/**
 * Ngenius Report Block
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Initialize Report Block
     */
    public function __construct()
    {
        $this->_blockGroup = 'ngenius';
        $this->_controller = 'adminhtml_report';
        $this->_headerText = Mage::helper('ngenius')->__('n-genius Orders');
        parent::__construct();
        $this->_removeButton('add');
    }
}
