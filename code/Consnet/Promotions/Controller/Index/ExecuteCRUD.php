<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExecuteCRUD
 *
 * @author Kanyinda
 */
namespace Consnet\Promotions\Controller\Index;

 
class ExecuteCRUD  {
    
    
 
    protected $_demoColFactory;
    protected $sessionConfig;
    protected $sessionManager;
    protected $cookieMetadataFactory;
    protected $cookieManager;
    protected $logger;
//    protected $cache;
    protected $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
       
        \Consnet\Promotions\Model\ResourceModel\Promotion\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Psr\Log\LoggerInterface $logger 
    )
    {
 
        $this->$_demoColFactory = $collectionFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->sessionConfig = $sessionConfig;
        $this->sessionManager = $sessionManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->cookieManager = $cookieManager;
        $this->logger = $logger;
     //   $this->cache = $cache;

        return parent::__construct($context);
    }

    /**
     * Url like http://magento2.ce/index.php/foggyline_office/test/crud/
     */
    public function CrudExec()
    {
        $resultPage = $this->resultPageFactory->create();


//
//        $model = $this->_objectManager->create('Magento\Customer\Model\Address');
//        $model->load(21);
//        $model->setCity('Update London');
//        $model->save();
//
//
//
//
//
//
//        exit('7');



//        $cookieValue = 'Just some value';
//        $cookieMetadata = $this->cookieMetadataFactory
//            ->createPublicCookieMetadata()
//            ->setDuration(3600)
//            ->setPath($this->sessionConfig->getCookiePath())
//            ->setDomain($this->sessionConfig->getCookieDomain())
//            ->setSecure($this->sessionConfig->getCookieSecure())
//            ->setHttpOnly($this->sessionConfig->getCookieHttpOnly());
//
//        $this->cookieManager
//            ->setPublicCookie('cookie_name_1', $cookieValue, $cookieMetadata);


//        $this->messageManager->addSuccess('Success-1');
//        $this->messageManager->addSuccess('Success-2');
//        $this->messageManager->addNotice('Notice-1');
//        $this->messageManager->addNotice('Notice-2');
//        $this->messageManager->addWarning('Warning-1');
//        $this->messageManager->addWarning('Warning-2');
//        $this->messageManager->addError('Error-1');
//        $this->messageManager->addError('Error-2');

        return $resultPage;
    }
    
    
    
    
    
}
