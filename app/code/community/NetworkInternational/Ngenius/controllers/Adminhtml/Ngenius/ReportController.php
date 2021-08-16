<?php

/**
 * Ngenius Report Controller
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Adminhtml_Ngenius_ReportController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Grid action
     */
    public function indexAction()
    {
        $this->_title($this->__('Report'))->_title($this->__('n-genius Reports'));
        $this->loadLayout();
        $this->_setActiveMenu('report/report');
        $this->_addContent($this->getLayout()->createBlock('ngenius/adminhtml_report'));
        $this->renderLayout();
    }

    /**
     * Ajax callback for grid actions
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ngenius/adminhtml_report_grid')->toHtml()
        );
    }

    /**
     * Export ngenius csv actions
     */
    public function exportNgeniusCsvAction()
    {
        $fileName = 'orders_ngenius.csv';
        $grid = $this->getLayout()->createBlock('ngenius/adminhtml_report_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export ngenius excel actions
     */
    public function exportNgeniusExcelAction()
    {
        $fileName = 'orders_ngenius.xml';
        $grid = $this->getLayout()->createBlock('ngenius/adminhtml_report_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
