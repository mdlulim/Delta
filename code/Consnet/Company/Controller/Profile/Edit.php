<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Consnet\Company\Controller\Profile;

/**
 * Class Edit
 */
class Edit extends \Magento\Company\Controller\AbstractAction
{
    /**
     * Edit company profile form
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \InvalidArgumentException
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Customer Profile'));

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed()
    {
        return $this->companyContext->isResourceAllowed('Magento_Company::edit_account')
        || $this->companyContext->isResourceAllowed('Magento_Company::edit_address');
    }
}
