<?php

namespace Consnet\Api\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    protected $storeManager;
    protected $objectManager;
    protected $writer;

    const XML_PATH_CONFIG = 'consnet_1/wsdls/';



    public function __construct(Context $context,
        \Magento\Framework\App\Config\Storage\WriterInterface $writer,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager  = $storeManager;
        $this->writer  =  $writer;
        parent::__construct($context);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function setConfigValue($field, $value)
    {
         $this->writer->save(
            self::XML_PATH_CONFIG.$field, 
            $value
        );
    }


    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CONFIG . $code, $storeId);
    }


}