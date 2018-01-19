<?php

namespace Consnet\Promotions\Setup;

use Consnet\Promotions\Model\Demo;
use Consnet\Promotions\Model\DemoFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface {

    /**
     * Demo factory
     *
     * @var DemoFactory
     */
    private $demoFactory;

    /**
     * Init
     *
     * @param DemoFactory $demoFactory
     */
    public function __construct(DemoFactory $demoFactory) {
        $this->demoFactory = $demoFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $demoData = [
            'title' => 'Demo Title',
            'is_active' => 1,
        ];
        /**
         * Insert demo data
         */
        $this->createDemo()->setData($demoData)->save();
    }

    /**
     * Create demo
     *
     * @return Demo
     */
    public function createDemo() {
        return $this->demoFactory->create();
    }

}
