<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Consnet\Company\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class ProductActions
 */
class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'company/*/edit',
                        ['id' => $item['entity_id']]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];

                $item[$this->getData('name')]['viewusers'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'switchcompany/SwitchCompany/viewUsers',
                        ['id' => $item['entity_id']]
                    ),
                    'label' => __('Contact Person'),
                    'hidden' => false,
                ];

                $item[$this->getData('name')]['order'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'switchcompany/SwitchCompany/newOrder',
                        ['id' => $item['entity_id']]
                    ),
                    'label' => __('Create Order'),
                    'hidden' => false,
                ];

                $item[$this->getData('name')]['vieworder'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'switchcompany/SwitchCompany/viewOrder',
                        ['id' => $item['entity_id']]
                    ),
                    'label' => __('View Orders'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
