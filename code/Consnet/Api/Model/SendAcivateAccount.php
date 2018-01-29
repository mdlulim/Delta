<?php

namespace Consnet\Api\Model;

use \Magento\Framework\Object;
use Magento\Framework\Math\Random;

class SendAcivateAccount extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var Random
     */
    private $mathRandom;
    protected $accountManager ;
    protected $customerFactory ;
    protected $objectManager;

    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager, 
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Escaper $escaper,
        Random $mathRandom,
        \Magento\Customer\Model\AccountManagement $accountManager , 
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager

    )
    {
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->mathRandom = $mathRandom;
        $this->accountManager  = $accountManager;
        $this->objectManager = $objectManager;

    }

    public function sendEmail($toemail)
    {
        //$toemail = $_SESSION['email_activate'];

        $websiteId = $this->storeManager->getWebsite()->getWebsiteId();
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId)->loadByEmail($toemail);
        $customerInt = $this->objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface')->getById($customer->getId()) ;

        //$customer->setEmail('mlamulizondo@gmail.com');
 
        $tkn  = $this->mathRandom->getUniqueHash();
        //$this->accountManager->changeResetPasswordLinkToken($customerInt, $tkn);

        //
        $resources = $this->objectManager->get('Magento\Framework\App\ResourceConnection'); 
        $connection = $resources->getConnection();

        $customer_entity = $resources->getTableName('customer_entity');//2017-12-12 12:12:32
        $created_at = date("Y-m-d H:i:s", strtotime('+5 hours'));
        //$created_at = date("Y-m-d H:i:s");
        $customer_id = $customerInt->getId();
        //var_dump($created_at); die();
        $sql = "UPDATE " . $customer_entity . " SET `rp_token`='$tkn', `rp_token_created_at`='$created_at' 
                WHERE 
                `entity_id` = '$customer_id'";
        $connection->query($sql);
        //
        
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId)->loadByEmail($toemail);
        $customer->sendNewAccountEmail();

        $templateParams = [
            'full_name' => $customer->getName()
        ];

        $sender = [
            'name' => $this->_escaper->escapeHtml('localhost'),
            'email' => $this->_escaper->escapeHtml('mlamuli@consnet.co.za'),
        ];
        $postData = [

            'name' => $customer->getName() ,
            'id' => $customer->getId() ,
            'rp_token' => $customer->getRpToken(),
            'email' => $customer->getEmail(),
        ];
        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($templateParams);



        $transport = $this->_transportBuilder
            ->setTemplateIdentifier('customer_account_activate')
            ->setTemplateOptions(
                [
                    'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars(['user' => $postObject])
            ->setFrom($sender)
            ->addTo('mlamulizondo@gmail.com')
            ->setReplyTo($toemail)
            ->getTransport();
        $transport->sendMessage();


    }
}

?>