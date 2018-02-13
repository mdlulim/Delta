<?php
/**
 * Copyright � Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Consnet\CustomerFilter\Block\Html;

use Magento\Framework\View\Element\Template;

/**
 * Html page title block
 *
 * @method $this setTitleId($titleId)
 * @method $this setTitleClass($titleClass)
 * @method string getTitleId()
 * @method string getTitleClass()
 * @api
 * @since 100.0.2
 */
class Title extends Template
{
    /**
     * Own page title to display on the page
     *
     * @var string
     */
    protected $pageTitle;

    /**
     * Provide own page title or pick it from Head Block
     *
     * @return string
     */
    public function getPageTitle()
    {
        if (!empty($this->pageTitle)) {
            return $this->pageTitle;
        }
        if (strpos($this->pageConfig->getTitle()->getShortHeading(), "#0") !== false) {
            $magento_order = substr($this->pageConfig->getTitle()->getShort(), 1);
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $sales_order = $om->create('\Magento\Sales\Model\Order')->loadByIncrementId($magento_order);
            if($sales_order->getData('ECC_ORDER') == null || 
               $sales_order->getData('ECC_ORDER') == ''){
                $messageManager = $om->create('\Magento\Framework\Message\ManagerInterface');
                $messageManager->addWarningMessage('Pricing Is Still To Be Proccessed');
                return __("Temporary Order # ".$sales_order->getIncrementId());
            }
            return __("Order # ".$sales_order->getData('ECC_ORDER'));
        }
        return __($this->pageConfig->getTitle()->getShort());
    }

    /**
     * Provide own page content heading
     *
     * @return string
     */
    public function getPageHeading()
    {

        if (!empty($this->pageTitle)) {
            return __($this->pageTitle);
        }
        if (strpos($this->pageConfig->getTitle()->getShortHeading(), "Order #") !== false) {
            $magento_order = substr($this->pageConfig->getTitle()->getShortHeading(), 8);
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $sales_order = $om->create('\Magento\Sales\Model\Order')->loadByIncrementId($magento_order);
            /*$resources = $om->get('Magento\Framework\App\ResourceConnection');
            $connection = $resources->getConnection();
            $query = "SELECT `erpOrderId` FROM `erp_magento` WHERE `magOrderId` = '".$magento_order."'";
            $erporder =  $connection->fetchRow($query)['erpOrderId'];*/
            $sales_order = $om->create('\Magento\Sales\Model\Order')->loadByIncrementId($magento_order);
            if($sales_order->getData('ECC_ORDER') == null || 
               $sales_order->getData('ECC_ORDER') == ''){
                $messageManager = $om->create('\Magento\Framework\Message\ManagerInterface');
                $messageManager->addWarningMessage('Pricing Is Still To Be Proccessed');
                return __("Temporary Order # ".$sales_order->getIncrementId());
            }
            return __("Order # ".$sales_order->getData('ECC_ORDER'));
        }
        if (strpos($this->pageConfig->getTitle()->getShortHeading(), "Requisition Lists") !== false) {
            return __("Order Templates");
        }
        
       return __($this->pageConfig->getTitle()->getShortHeading());
    }

    /**
     * Set own page title
     *
     * @param string $pageTitle
     * @return void
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
        if(substr($pageTitle, 0,12) == "Edit Order #"){
            //Edit Order #000000162
            //var_dump(substr($pageTitle, 12));die();
            $magento_order = substr($pageTitle, 12);//substr($pageTitle, 11);
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $sales_order = $om->create('\Magento\Sales\Model\Order')->loadByIncrementId($magento_order);
            //return __("Edit Order # ".$sales_order->getData('ECC_ORDER'));
            $this->pageTitle = "Edit Order # ".$sales_order->getData('ECC_ORDER');
        }
    }
}
