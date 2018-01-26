<?php
/**
 * Consnet_ManageCustomer extension
 */
namespace Consnet\ManageCustomer\Model\ResourceModel;

class Customer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Date model
     * 
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * constructor
     * 
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */

     protected $authSession;
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
         \Magento\Backend\Model\Auth\Session $authSession

    )
    {
        $this->_date = $date;
        parent::__construct($context);
         $this->authSession = $authSession;
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customer_entity', 'entity_id');
    }

    /**
     * Retrieves Customer Name from DB by passed id.
     *
     * @param string $id
     * @return string|bool
     */

     public function getCurrentUserId()
     {
        return $this->authSession->getUser()->getId();
     }
    public function getCustomerNameById($id)
    {
       var_dump($this->getCurrentUserId());
       $userId=$this->authSession->getCurrentUser->getUsername();
        $adapter = $this->getConnection();
         $select = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('entity_id = :entity_id');
            
        $binds = ['entity_id' => (int)$id];
        return $adapter->fetchOne($select, $binds);
    }
    /**
     * before save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\Mageplaza\ManageCustomer\Model\Customer $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->_date->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->_date->date());
        }
        return parent::_beforeSave($object);
    }
}
