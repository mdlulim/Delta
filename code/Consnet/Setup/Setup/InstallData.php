<?php


namespace Consnet\Setup\Setup;

use Magento\Customer\Model\GroupFactory;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;

/**
 * SharedCatalog Model.
 */
use Magento\SharedCatalog\Model\SharedCatalog;
use Magento\SharedCatalog\Api\SharedCatalogRepositoryInterface;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    protected $groupFactory;

    private $customerSetupFactory;

    /**
     * @var \Magento\SharedCatalog\Model\SaveHandler
     */
    private $saveHandler;
	private $sharedCatalog;
    private $sharedCatalogRepositoryInterface;

	/** @var  CategoryRepositoryInterface */
    private $categoryRepositoryInterface;
    /** @var  CategoryInterfaceFactory */
    private $categoryInterfaceFactory;

    public function __construct(
    	SharedCatalog $SharedCatalog,
    	\Magento\SharedCatalog\Model\Repository $SharedCatalogRepositoryInterface,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryInterfaceFactory,
        CustomerSetupFactory $customerSetupFactory,
        GroupFactory $groupFactory,
        \Magento\SharedCatalog\Model\SaveHandler $saveHandler
        ) {
        $this->groupFactory = $groupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
    	$this->sharedCatalog = $SharedCatalog;
    	$this->sharedCatalogRepositoryInterface = $SharedCatalogRepositoryInterface;
        $this->saveHandler = $saveHandler;

        $this->categoryRepositoryInterface = $categoryRepository;
        $this->categoryInterfaceFactory = $categoryInterfaceFactory;

    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        //Still Busy
        $this->createSharedCatalog();
        
        //Failing
        $this->createCategoryAttributes($setup);
        
        //Worked
        $this->createCategories();
        $this->createCustomerAttributes($setup);
    }

    public function createGroups()
    {
        // Create the new group
        /** @var \Magento\Customer\Model\Group $group */
        $group = $this->groupFactory->create();
        $group->setCode('Sparkling Beverages')->setTaxClassId(3)->save();
        $group = $this->groupFactory->create();
        $group->setCode('Lager')->setTaxClassId(3)->save();
        $group = $this->groupFactory->create();
        $group->setCode('Sorghum')->setTaxClassId(3)->save();
    }

    public function createCategoryAttributes($setup){
        /**
         * Add attributes to the eav/attribute
         */
        //$setup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'redirect_url');
        /*$setup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'redirect_url',
            [
                'type' => 'varchar',
                'group' => 'General',
                'label' => 'Redirect to another URL',
                'input' => 'text',
                'required' => false,
                'sort_order' => 2,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'visible_on_front' => true,
            ]
        );*/
    }

    public function createCustomerAttributes($setup){
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'CONTACT_ID', [
            'type' => 'varchar',
            'label' => 'Contact Id',
            'input' => 'text',
            'source' => '',
            'required' => true,
            'visible' => true,
            'position' => 333,
            'system' => false,
            'backend' => ''
        ]);

        
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'CONTACT_ID')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'customer_account_edit'
            ]]);
        $attribute->save();
    }

    public function createCategories()
    {
        $category = $this->categoryInterfaceFactory->create();
        $category->setName("Home");
        $category->setParentId(2);
        $category->setIsActive(1);
        $category->setData('stores', [0]);
        $this->categoryRepositoryInterface->save($category);

        $category = $this->categoryInterfaceFactory->create();
        $category->setName("Products");
        $category->setParentId(2);
        $category->setIsActive(1);
        $category->setData('stores', [0]);
        $this->categoryRepositoryInterface->save($category);

        $category = $this->categoryInterfaceFactory->create();
        $category->setName("About Us");
        $category->setParentId(2);
        $category->setIsActive(1);
        $category->setData('stores', [0]);
        $this->categoryRepositoryInterface->save($category);

        $category = $this->categoryInterfaceFactory->create();
        $category->setName("Promotions");
        $category->setParentId(2);
        $category->setIsActive(1);
        $category->setData('stores', [0]);
        $this->categoryRepositoryInterface->save($category);

        $category = $this->categoryInterfaceFactory->create();
        $category->setName("Contact Us");
        $category->setParentId(2);
        $category->setIsActive(1);
        $category->setData('stores', [0]);
        $this->categoryRepositoryInterface->save($category);

        $categoriesName = array('Sparkling Beverages', 'Lager', 'Sorghum');
        foreach ($categoriesName as $categoryName){
            /** new category instance **/
            $category = $this->categoryInterfaceFactory->create();
            $category->setName($categoryName);
            /** set root category as parent category **/
            $category->setParentId(4);
            $category->setIsActive(1);
            $category->setData('stores', [0]);
            $this->categoryRepositoryInterface->save($category);
        }
    }

    public function createSharedCatalog()
    {	
	$shareCatalogList = array(
    					    array("Sparkling Beverages", "Sparkling Beverages", "4", "Custom", "1", "3"),
    						array("Lager", "Lager", "5", "Custom", "1", "3"),
    						array("Sorghum", "Sorghum", "6", "Custom", "1", "3")
							 );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $repository = $objectManager->create('\Magento\SharedCatalog\Model\Repository');
        foreach ($shareCatalogList as $value) {
            $catalog = $this->sharedCatalog;
            $this->sharedCatalog->setName($value[0]);
            $this->sharedCatalog->setDescription($value[1]);
            $this->sharedCatalog->setType(0);
            $this->sharedCatalog->setStoreId($value[4]);
            $this->sharedCatalog->setTaxClassId($value[5]);
            $repository->save($catalog);
            $this->sharedCatalog->setId(null);
            $this->sharedCatalog->setCustomerGroupId(null);

        }
    }
}
