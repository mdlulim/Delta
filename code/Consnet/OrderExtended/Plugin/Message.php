<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Consnet\OrderExtended\Plugin;

class Message 
{
    public function beforeAddError($error){
        if($error == 'Please specify a shipping method.'){
            return ['Please Select Calculate Grand Total.'];
        }
        return null;
    }
}