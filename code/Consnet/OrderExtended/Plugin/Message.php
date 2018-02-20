<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Consnet\OrderExtended\Plugin;

class Message 
{
    public function beforeAddError($error, $group){
        if($error == 'Please specify a shipping method.'){
            $error = 'Please Select Calculate Grand Total.';
            return [$error, $group];
        }
        return null;
    }
}