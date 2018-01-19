<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Consnet\Erporder\Controller\Adminhtml\Order\Edit;

class Save extends \Consnet\Erporder\Controller\Adminhtml\Order\Create\Save
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

   

}
