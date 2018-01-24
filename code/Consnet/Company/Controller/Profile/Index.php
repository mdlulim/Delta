<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Consnet\Company\Controller\Profile;

use Magento\Company\Controller\AbstractAction;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 */
class Index extends AbstractAction
{
    /**
     * Authorization level of a company session.
     */
    const COMPANY_RESOURCE = 'Magento_Company::view';

    /**
     * @var string
     */
    private $viewAccountInformationPermission = 'Magento_Company::view_account';

    /**
     * @var string
     */
    private $viewAddressPermission = 'Magento_Company::view_address';

    /**
     * @var string
     */
    private $viewContactsPermission = 'Magento_Company::contacts';

    /**
     * @var string
     */
    private $viewPaymentInformationPermission = 'Magento_Company::payment_information';

    /**
     * Company profile
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \InvalidArgumentException
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Customer Profile'));

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    protected function isAllowed()
    {
        if (!$this->companyContext->isResourceAllowed($this->viewAccountInformationPermission) &&
            !$this->companyContext->isResourceAllowed($this->viewAddressPermission) &&
            !$this->companyContext->isResourceAllowed($this->viewContactsPermission) &&
            !$this->companyContext->isResourceAllowed($this->viewPaymentInformationPermission)
        ) {
            return false;
        }

        return parent::isAllowed();
    }
}
