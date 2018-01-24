<?php

namespace Consnet\CustomerFilter\Ui\Component;

use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Ui\Component\Listing\AttributeRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param AttributeRepository $attributeRepository
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        AttributeRepository $attributeRepository,
        array $meta = [],
        array $data = []
    ) {
        $this->attributeRepository = $attributeRepository;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getData() {
        $data = parent::getData();
        foreach ($this->attributeRepository->getList() as $attributeCode => $attributeData) {
            foreach ($data['items'] as &$item) {
                if (isset($item[$attributeCode]) && !empty($attributeData[AttributeMetadataInterface::OPTIONS])) {
                    $item[$attributeCode] = explode(',', $item[$attributeCode]);
                }
            }
        }

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $authSession = $om->get('\Magento\Backend\Model\Auth\Session');
        $user = $authSession->getUser();
        $userId = $user->getUserId();
        $roleId = $authSession->getUser()->getRole()->getRoleId();
       
        $resource = $om->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('company'); 
        $tableName2 = $resource->getTableName('company_advanced_customer_entity'); 
        
        if(($user->getRole()->getRoleName() == 'sales_rep')) {
            $tableName = $resource->getTableName('company'); 
            $tableName2 = $resource->getTableName('company_advanced_customer_entity'); 

            $authSession = $om->get('\Magento\Backend\Model\Auth\Session');
            $userId=$authSession->getUser()->getUserId(); 
            
            $sql = "Select entity_id FROM " . $tableName." WHERE  sales_representative_id =".$userId;
            $result = $connection->fetchAll($sql); 
        
            $arrayIds = [];
            if($result != null){
                $sql2 = "Select customer_id FROM " . $tableName2.' WHERE  company_id IN (' . implode(',', $arrayIds) . ')';
                $result2 = $connection->fetchAll($sql2); 
                $customerIds = [];
                $ids=0;
                foreach($result2 as $arr){
                $customerIds[$ids]=$arr['customer_id'];
                $ids++;
                }
            }
            $Arra = $arrayIds;
        $tmp['items'] = array ();

        foreach($data['items'] as $line){
            if(in_array($line['entity_id'],$Arra)){
              array_push($tmp['items'],$line);
            }
        }
        $data = $tmp;
        return $data;
        }else{
          return $data;
        } 
  
    }
}

