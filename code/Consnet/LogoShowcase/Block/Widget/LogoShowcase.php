<?php
namespace Consnet\LogoShowcase\Block\Widget;
 
class LogoShowcase extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	public function _toHtml()
    {
    	$this->setTemplate('widget/logoShowcase.phtml');
    }
}