<?php
/**
 * Consnet_ManageCustomer extension
 */
namespace Consnet\ManageCustomer\Model\Customer\Source;

class CountryField implements \Magento\Framework\Option\ArrayInterface
{
    const _EMPTY = 1;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::_EMPTY,
                'label' => __('')
            ],
        ];
        return $options;

    }
}
