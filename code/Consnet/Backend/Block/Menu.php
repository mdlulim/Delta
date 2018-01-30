<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Consnet\Backend\Block;

/**
 * Backend menu block
 *
 * @api
 * @method \Magento\Backend\Block\Menu setAdditionalCacheKeyInfo(array $cacheKeyInfo)
 * @method array getAdditionalCacheKeyInfo()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @api
 * @since 100.0.2
 */
class Menu extends \Magento\Backend\Block\Template
{

    
    const CACHE_TAGS = 'BACKEND_MAINMENU';

    /**
     * @var string
     */
    protected $_containerRenderer;

    /**
     * @var string
     */
    protected $_itemRenderer;

    /**
     * Backend URL instance
     *
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_url;

    /**
     * Current selected item
     *
     * @var \Magento\Backend\Model\Menu\Item|false|null
     */
    protected $_activeItemModel = null;

    /**
     * @var \Magento\Backend\Model\Menu\Filter\IteratorFactory
     */
    protected $_iteratorFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\Backend\Model\Menu\Config
     */
    protected $_menuConfig;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var MenuItemChecker
     */
    private $menuItemChecker;

    /**
     * @var AnchorRenderer
     */
    private $anchorRenderer;

    /**
     * @param Template\Context $context
     * @param \Magento\Backend\Model\UrlInterface $url
     * @param \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Backend\Model\Menu\Config $menuConfig
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     * @param \Magento\Backend\Block\MenuItemChecker|null $menuItemChecker
     * @param \Magento\Backend\Block\AnchorRenderer|null $anchorRenderer
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\UrlInterface $url,
        \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Backend\Model\Menu\Config $menuConfig,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = [],
        \Magento\Backend\Block\MenuItemChecker $menuItemChecker = null,
        \Magento\Backend\Block\AnchorRenderer $anchorRenderer 
    ) {
        $this->_url = $url;
        $this->_iteratorFactory = $iteratorFactory;
        $this->_authSession = $authSession;
        $this->_menuConfig = $menuConfig;
        $this->_localeResolver = $localeResolver;
        $this->menuItemChecker =  $menuItemChecker;
        $this->anchorRenderer = $anchorRenderer;
        parent::__construct($context, $data);
    }

    /**
     * Initialize template and cache settings
     *
     * @return void
     */
    protected function _construct()
    {

        

        parent::_construct();
        $this->setCacheTags([self::CACHE_TAGS]);
    }

    /**
     * Render menu item anchor label
     *
     * @param \Magento\Backend\Model\Menu\Item $menuItem
     * @return string
     */
    protected function _getAnchorLabel($menuItem)
    {
        return $this->escapeHtml(__($menuItem->getTitle()));
    }

    /**
     * Render menu item mouse events
     * @param \Magento\Backend\Model\Menu\Item $menuItem
     * @return string
     */
    protected function _renderMouseEvent($menuItem)
    {
        return $menuItem->hasChildren() ? 'onmouseover="Element.addClassName(this,\'over\')" onmouseout="Element.removeClassName(this,\'over\')"' : '';
    }

    /**
     * Render item css class
     *
     * @param \Magento\Backend\Model\Menu\Item $menuItem
     * @param int $level
     * @return string
     */
    protected function _renderItemCssClass($menuItem, $level)
    {
        $isLast = 0 == $level && (bool)$this->getMenuModel()->isLast($menuItem) ? 'last' : '';
        $isItemActive = $this->menuItemChecker->isItemActive(
            $this->getActiveItemModel(),
            $menuItem,
            $level
        ) ? '_current _active' : '';

        $output =  $isItemActive .
            ' ' .
            ($menuItem->hasChildren() ? 'parent' : '') .
            ' ' .
            $isLast .
            ' ' .
            'level-' .
            $level;
        return $output;
    }

    /**
     * Get menu filter iterator
     *
     * @param \Magento\Backend\Model\Menu $menu
     * @return \Magento\Backend\Model\Menu\Filter\Iterator
     */
    protected function _getMenuIterator($menu)
    {
        return $this->_iteratorFactory->create(['iterator' => $menu->getIterator()]);
    }

    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $html = preg_replace_callback(
            '#' . \Magento\Backend\Model\UrlInterface::SECRET_KEY_PARAM_NAME . '/\$([^\/].*)/([^\/].*)/([^\$].*)\$#U',
            [$this, '_callbackSecretKey'],
            $html
        );

        return $html;
    }

    /**
     * Replace Callback Secret Key
     *
     * @param string[] $match
     * @return string
     */
    protected function _callbackSecretKey($match)
    {
        return \Magento\Backend\Model\UrlInterface::SECRET_KEY_PARAM_NAME . '/' . $this->_url->getSecretKey(
            $match[1],
            $match[2],
            $match[3]
        );
    }

    /**
     * Retrieve cache lifetime
     *
     * @return int
     */
    public function getCacheLifetime()
    {
        return 0;//86400;
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = [
            'admin_top_nav',
            $this->getActive(),
            $this->_authSession->getUser()->getId(),
            $this->_localeResolver->getLocale(),
        ];
        // Add additional key parameters if needed
        $newCacheKeyInfo = $this->getAdditionalCacheKeyInfo();
        if (is_array($newCacheKeyInfo) && !empty($newCacheKeyInfo)) {
            $cacheKeyInfo = array_merge($cacheKeyInfo, $newCacheKeyInfo);
        }
        return $cacheKeyInfo;
    }

    /**
     * Get menu config model
     *
     * @return \Magento\Backend\Model\Menu
     */
    public function getMenuModel()
    {
        return $this->_menuConfig->getMenu();
    }

    /**
     * Render menu
     *
     * @param \Magento\Backend\Model\Menu $menu
     * @param int $level
     * @return string HTML
     */
    public function renderMenu($menu, $level = 0)
    {

        
        $output = '<ul ' . (0 == $level ? 'id="nav" role="menubar"' : '') . ' >';

        /** @var $menuItem \Magento\Backend\Model\Menu\Item  */
        foreach ($this->_getMenuIterator($menu) as $menuItem) {
            
           

            $output .= '<li ' . $this->_renderMouseEvent(
                $menuItem
            ) . ' class="' . $this->_renderItemCssClass(
                $menuItem,
                $level
            ) . '"' . $this->getUiId(
                $menuItem->getId()
            ) . 'role="menuitem">';

            $output .= $this->anchorRenderer->renderAnchor($this->getActiveItemModel(), $menuItem, $level);

            if ($menuItem->hasChildren()) {
                $output .= $this->renderMenu($menuItem->getChildren(), $level + 1);
            }
            $output .= '</li>';
        }
        $output .= '</ul>';

        return $output;
    }

    /**
     * Count All Subnavigation Items
     *
     * @param \Magento\Backend\Model\Menu $items
     * @return int
     */
    protected function _countItems($items)
    {
        $total = count($items);
        foreach ($items as $item) {
            /** @var $item \Magento\Backend\Model\Menu\Item */
            if ($item->hasChildren()) {
                $total += $this->_countItems($item->getChildren());
            }
        }
        return $total;
    }

    /**
     * Building Array with Column Brake Stops
     *
     * @param \Magento\Backend\Model\Menu $items
     * @param int $limit
     * @return array|void
     * @todo: Add Depth Level limit, and better logic for columns
     */
    protected function _columnBrake($items, $limit)
    {
        $total = $this->_countItems($items);
        if ($total <= $limit) {
            return;
        }
        $result[] = ['total' => $total, 'max' => ceil($total / ceil($total / $limit))];
        $count = 0;
        foreach ($items as $item) {
            $place = $this->_countItems($item->getChildren()) + 1;
            $count += $place;
            if ($place - $result[0]['max'] > $limit - $result[0]['max']) {
                $colbrake = true;
                $count = 0;
            } elseif ($count - $result[0]['max'] > $limit - $result[0]['max']) {
                $colbrake = true;
                $count = $place;
            } else {
                $colbrake = false;
            }
            $result[] = ['place' => $place, 'colbrake' => $colbrake];
        }
        return $result;
    }

    /**
     * Add sub menu HTML code for current menu item
     *
     * @param \Magento\Backend\Model\Menu\Item $menuItem
     * @param int $level
     * @param int $limit
     * @param $id int
     * @return string HTML code
     */
    protected function _addSubMenu($menuItem, $level, $limit, $id = null)
    {
        $output = '';
        if (!$menuItem->hasChildren()) {
            return $output;
        }


        $output .= '<div class="submenu"' . ($level == 0 && isset($id) ? ' aria-labelledby="' . $id . '"' : '') . '>';
        $colStops = null;
        if ($level == 0 && $limit) {
            $name  = $this->_getAnchorLabel($menuItem) ;
            if($name == 'Customers'){
                $name  = 'Companies';
            }
            $colStops = $this->_columnBrake($menuItem->getChildren(), $limit);
            $output .= '<strong class="submenu-title">' . $name . '</strong>';
            $output .= '<a href="#" class="action-close _close" data-role="close-submenu"></a>';
        }

        $output .= $this->renderNavigation($menuItem->getChildren(), $level + 1, $limit, $colStops);
        $output .= '</div>';
       // }
        return $output;
    }


    private function excludeSales($menu){
        
        $arr_menu = array(
            "Magento_Sales::sales",
            "Magento_Sales::sales_operation",
            "Magento_Sales::sales_order",
            "Magento_Customer::customer",
           // "Magento_Customer::customer_manage",
            "Magento_Company::company_index",
    
        );

        if(in_array($menu,$arr_menu)){
            return false;
        }else{
            return true;
        }

    }


    private function excludeAdmin($menu){
        
        $arr_menu = array(
            "Magento_Catalog::catalog",
            "Magento_Catalog::inventory",
            "Magento_Catalog::catalog_products",
            "Magento_Catalog::catalog_categories",
            "Magento_SharedCatalog::shared_list",
            "Magento_Backend::content",
            "Magento_Cms::cms_block",
            "Magento_Banner::cms_magento_banner",
            "Magento_Widget::cms_widget_instance",
            "Magestore_Bannerslider::bannerslider",

            "Magestore_Bannerslider::bannerslider_banners"
        
        );

        if(in_array($menu,$arr_menu)){
            return false;
        }else{
            return true;
        }

    }

    private function excludeCIC($menu){

        $arr_menu = array(
            
            "Magento_Backend::system",
            "Magento_User::system_acl",
            "Magento_User::system_acl_users",
            "Magento_User::system_acl_locks",
        );

        if(in_array($menu,$arr_menu)){
            return false;
        }else{
            return true;
        }

    }

    private function changeTitle($menuItem){

        $arr_menu = array(
         
            "Magento_Customer::customer",
            "Magento_Customer::customer_manage",
            "Magento_Company::company_index"
        
        );

        if(in_array($menuItem->getId(),$arr_menu)){
            
            if($menuItem->getTitle() == 'Customers'){
                $menuItem->setTitle('Companies');
            }
            if($menuItem->getTitle() == 'All Customers'){
                $menuItem->setTitle('Contact Person');
            }
            return  $menuItem;
        }else{
            return  $menuItem;
        }

    }

    /**
     * Render Navigation
     *
     * @param \Magento\Backend\Model\Menu $menu
     * @param int $level
     * @param int $limit
     * @param array $colBrakes
     * @return string HTML
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function renderNavigation($menu, $level = 0, $limit = 0, $colBrakes = [])
    {
        $itemPosition = 1;
        $outputStart = '<ul ' . (0 == $level ? 'id="nav" role="menubar"' : 'role="menu"') . ' >';
        $output = '';

        /** @var $menuItem \Magento\Backend\Model\Menu\Item  */
        foreach ($this->_getMenuIterator($menu) as $menuItem) {

             
            
            $menuId = $menuItem->getId();
            $itemName = substr($menuId, strrpos($menuId, '::') + 2);
            $itemClass = str_replace('_', '-', strtolower($itemName));

            $menuItem = $this->changeTitle($menuItem);

            //var_dump($menuItem->getId());

            if (count($colBrakes) && $colBrakes[$itemPosition]['colbrake'] && $itemPosition != 1) {
                $output .= '</ul></li><li class="column"><ul role="menu">';
            }

            $user = $this->_authSession->getUser();
           
            if(($user->getRole()->getRoleName() == 'sales_rep' ||
                $user->getRole()->getRoleName() == 'cic_agent' ) && 
                $this->excludeSales($menuItem->getId())  ) 
             {
               // var_dump($menuItem->getTitle());
               //echo  $user->getRole()->getRoleName();
                
            }elseif(($user->getRole()->getRoleName() == 'cic_support_stuff' ) && 
            $this->excludeCIC($menuItem->getId()))
            {

            }elseif(($user->getRole()->getRoleName() == 'web-shop-administrator' ) && 
            $this->excludeAdmin($menuItem->getId())){}
            else{

            
            $id = $this->getJsId($menuItem->getId());
            $subMenu = $this->_addSubMenu($menuItem, $level, $limit, $id);
            $anchor = $this->anchorRenderer->renderAnchor($this->getActiveItemModel(), $menuItem, $level);
            
            $output .= '<li ' . $this->getUiId($menuItem->getId())
                . ' class="item-' . $itemClass . ' ' . $this->_renderItemCssClass($menuItem, $level)
                . ($level == 0 ? '" id="' . $id . '" aria-haspopup="true' : '')
                . '" role="menu-item">' . $anchor . $subMenu . '</li>';
            $itemPosition++;
            }
        }

        if (count($colBrakes) && $limit) {
            $output = '<li class="column"><ul role="menu">' . $output . '</ul></li>';
        }

        return $outputStart . $output . '</ul>';
    }

    /**
     * Get current selected menu item
     *
     * @return \Magento\Backend\Model\Menu\Item|false
     */
    public function getActiveItemModel()
    {
        if ($this->_activeItemModel === null) {
            $this->_activeItemModel = $this->getMenuModel()->get($this->getActive());
            if (false == $this->_activeItemModel instanceof \Magento\Backend\Model\Menu\Item) {
                $this->_activeItemModel = false;
            }
        }
        return $this->_activeItemModel;
    }
}
