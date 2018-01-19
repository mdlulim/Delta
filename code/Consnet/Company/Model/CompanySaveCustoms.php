<?php

namespace Consnet\Company\Model;
use Magento\Framework\Exception\CouldNotSaveException ;
use Magento\Company\Api\Data\CompanyExtensionFactory as CompanyExtension;
use Magento\Company\Api\CompanyRepositoryInterface;

class   CompanySaveCustoms {




  public function afterSave(
    \Magento\Company\Api\Data\companyRepositoryInterface $subject ,
    \Magento\Company\Api\Data\companyInterface  $entity
)
  {
    var_dump($entity);
               $extensionAttributes = $entity->getExtensionAttributes(); /** get current extension attributes from entity **/
              // $ourCustomData = $extensionAttributes->getVKORG();

            //   $this->companySaver->save($ourCustomData);

                return $entity;

  }


}
