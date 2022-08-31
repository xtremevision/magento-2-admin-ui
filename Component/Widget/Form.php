<?php
declare(strict_types=1);

namespace Grasch\AdminUi\Component\Widget;

use Grasch\AdminUi\DataProvider\Widget\DataProviderInterface;
use Grasch\AdminUi\Model\Widget\MetadataInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form as BaseForm;
use Psr\Log\InvalidArgumentException;

class Form extends BaseForm
{
    /**
     * @var MetadataInterface
     */
    private MetadataInterface $metadata;

    /**
     * @var UiComponentFactory
     */
    private UiComponentFactory $uiComponentFactory;

    /**
     * @param ContextInterface $context
     * @param FilterBuilder $filterBuilder
     * @param MetadataInterface $metadata
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        FilterBuilder $filterBuilder,
        MetadataInterface $metadata,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $filterBuilder,
            $components,
            $data
        );

        $this->metadata = $metadata;
        $this->uiComponentFactory = $uiComponentFactory;

        $this->getDataProvider()->setMetadata($this->metadata);
    }

    /**
     * @inheridoc
     */
    public function prepare(): void
    {
        $arguments = [
            'context' => $this->getContext(),
            'data' => [
                'config' => [
                    'component' => 'Grasch_AdminUi/js/widget/form/element/abstract',
                    'template' => 'Grasch_AdminUi/widget/form/element/hidden',
                    'customInputName' => $this->metadata->getSyncFieldName()
                ],
                'name' => 'sync_field'
            ]
        ];

        $syncField = $this->uiComponentFactory->create(
            'sync_field',
            BaseForm\Element\Hidden::NAME,
            $arguments
        );
        $this->addComponent('sync_field', $syncField);
        $this->prepareChildComponent($syncField);

        parent::prepare();
    }

    /**
     * @return array
     */
    public function getDataSourceData(): array
    {
        return ['data' => $this->getDataProvider()->getData()];
    }

    /**
     * @return DataProviderInterface
     */
    public function getDataProvider(): DataProviderInterface
    {
        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $this->context->getDataProvider();
        if (!$dataProvider instanceof DataProviderInterface) {
            throw new InvalidArgumentException(
                '$dataProvider must implement ' . DataProviderInterface::class
            );
        }

        return $dataProvider;
    }
}
