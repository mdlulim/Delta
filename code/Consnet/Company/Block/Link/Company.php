<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Consnet\Company\Block\Link;

use Magento\Framework\View\Element\Html\Link;

/**
 * Class Company.
 */
class Company extends Link implements \Magento\Customer\Block\Account\SortLinkInterface
{
    /**
     * Get href
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('company/profile');
    }

    /**
     * Get Label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Customer Profile');
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }
}
