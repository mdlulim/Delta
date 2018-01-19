<?php

namespace Consnet\Company\Model\Plugin;
use Magento\Framework\Exception\CouldNotSaveException ;

class   CompanySaveCustoms {



  public function afterSave(
    \Magento\Company\Model\CompanyRepository  $subject ,
     $resultCustom

    )
  {
    $resultCustom = $this->saveCustoms($resultCustom);

  }

  public  function saveCustoms($resultCustom )
  {
      $extensibleAttributes = $resultCustom->getExtensionAttributes();

      $VKORGAttribute = $extensibleAttributes->getVKORG();
        var_dump($VKORGAttribute);
    //  $PARVWAttribute = $extensionAttributes->getPARVW();
      $resultCustom->setExtensionAttributes($extensibleAttributes);

      return $resultCustom;

  }
}
