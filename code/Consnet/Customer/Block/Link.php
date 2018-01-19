<?php

namespace Consnet\Customer\Block;

class Link extends \Magento\Framework\View\Element\Html\Link
{
/**
* Render block HTML.
*
* @return string
*/
protected function _toHtml()
    {
     if (false != $this->getTemplate()) {
          return parent::_toHtml();
     }

     if(!(isset($_SESSION['menu'])))
     {

        return '';
     }
     if($_SESSION['menu'] == false ){

     	return '';
    
     }else{
     	return '<li><a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($this->getLabel()) . '</a></li>';
    }
     


     
    }
}
