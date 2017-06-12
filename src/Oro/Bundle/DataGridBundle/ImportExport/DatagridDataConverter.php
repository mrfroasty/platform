<?php

namespace Oro\Bundle\DataGridBundle\ImportExport;

use Symfony\Component\Translation\TranslatorInterface;

use Oro\Bundle\ImportExportBundle\Context\ContextInterface;
use Oro\Bundle\ImportExportBundle\Converter\DataConverterInterface;
use Oro\Bundle\ImportExportBundle\Context\ContextAwareInterface;
use Oro\Bundle\EntityConfigBundle\DependencyInjection\Utils\ServiceLink;
use Oro\Bundle\DataGridBundle\Exception\RuntimeException;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Property\PropertyInterface;
use Oro\Bundle\LocaleBundle\Formatter\NumberFormatter;
use Oro\Bundle\LocaleBundle\Formatter\DateTimeFormatter;
use Oro\Bundle\ImportExportBundle\Exception\InvalidConfigurationException;

class DatagridDataConverter implements DataConverterInterface, ContextAwareInterface
{
    /**
     * @var ServiceLink
     */
    protected $gridManagerLink;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var NumberFormatter
     */
    protected $numberFormatter;

    /**
     * @var DateTimeFormatter
     */
    protected $dateTimeFormatter;

    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     *
     * @param ServiceLink         $gridManagerLink
     * @param TranslatorInterface $translator
     * @param NumberFormatter     $numberFormatter
     * @param DateTimeFormatter   $dateTimeFormatter
     */
    public function __construct(
        ServiceLink $gridManagerLink,
        TranslatorInterface $translator,
        NumberFormatter $numberFormatter,
        DateTimeFormatter $dateTimeFormatter
    ) {
        $this->gridManagerLink   = $gridManagerLink;
        $this->translator        = $translator;
        $this->numberFormatter   = $numberFormatter;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToExportFormat(array $exportedRecord, $skipNullValues = true)
    {
        if ($this->context->getValue('columns')) {
            $columns = $this->context->getValue('columns');
        } elseif ($this->context->hasOption('gridName')) {
            $gridName   = $this->context->getOption('gridName');
            $gridConfig = $this->gridManagerLink->getService()->getConfigurationForGrid($gridName);
            $columns    = $gridConfig->offsetGet('columns');
        } else {
            throw new InvalidConfigurationException(
                'Configuration of datagrid export processor must contain "gridName" or "columns" options.'
            );
        }

        $result = array();
        foreach ($columns as $columnName => $column) {
            if (isset($column['renderable']) && false === $column['renderable']) {
                continue;
            }

            $val = isset($exportedRecord[$columnName]) ? $exportedRecord[$columnName] : null;
            $val = $this->applyFrontendFormatting($val, $column);
            $result[$this->translator->trans($column['label'])] = $val;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToImportFormat(array $importedRecord, $skipNullValues = true)
    {
        throw new RuntimeException('The convertToImportFormat method is not implemented.');
    }

    /**
     * @param mixed       $val
     * @param array       $options
     *
     * @return string|null
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function applyFrontendFormatting($val, $options)
    {
        if (null !== $val) {
            $frontendType = isset($options['frontend_type']) ? $options['frontend_type'] : null;
            switch ($frontendType) {
                case PropertyInterface::TYPE_DATE:
                    // Date data does not contain time and timezone information.
                    $val = $this->dateTimeFormatter->formatDate($val, null, null, 'UTC');
                    break;
                case PropertyInterface::TYPE_DATETIME:
                    $val = $this->dateTimeFormatter->format($val);
                    break;
                case PropertyInterface::TYPE_TIME:
                    // Date data does not contain time and timezone information.
                    $val = $this->dateTimeFormatter->formatDate($val, null, null, 'UTC');
                    break;
                case PropertyInterface::TYPE_DECIMAL:
                    $val = $this->numberFormatter->formatDecimal($val);
                    break;
                case PropertyInterface::TYPE_INTEGER:
                    $val = $this->numberFormatter->formatDecimal($val);
                    break;
                case PropertyInterface::TYPE_BOOLEAN:
                    $val = $this->translator->trans((bool)$val ? 'Yes' : 'No', [], 'jsmessages');
                    break;
                case PropertyInterface::TYPE_PERCENT:
                    $val = $this->numberFormatter->formatPercent($val);
                    break;
                case PropertyInterface::TYPE_CURRENCY:
                    $val = $this->numberFormatter->formatCurrency($val);
                    break;
                case PropertyInterface::TYPE_SELECT:
                    if (isset($options['choices'][$val])) {
                        $val = $this->translator->trans($options['choices'][$val]);
                    }
                    break;
                case PropertyInterface::TYPE_HTML:
                    $val = $this->formatHtmlFrontendType(
                        $val,
                        isset($options['export_type']) ? $options['export_type'] : null
                    );
                    break;
            }
        }

        return $val;
    }

    /**
     * Converts HTML to its string representation
     *
     * @param string $val
     * @param string $exportType
     *
     * @return string
     */
    protected function formatHtmlFrontendType($val, $exportType)
    {
        $result = trim(
            str_replace(
                "\xC2\xA0", // non-breaking space (&nbsp;)
                ' ',
                html_entity_decode(strip_tags($val))
            )
        );
        if ($exportType === 'list') {
            $result = preg_replace('/\s*\n\s*/', ';', $result);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function setImportExportContext(ContextInterface $context)
    {
        $this->context = $context;
    }
}
