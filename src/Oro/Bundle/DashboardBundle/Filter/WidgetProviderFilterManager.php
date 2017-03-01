<?php

namespace Oro\Bundle\DashboardBundle\Filter;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

use Oro\Bundle\DashboardBundle\Model\WidgetOptionBag;

class WidgetProviderFilterManager
{
    /** @var WidgetProviderFilterInterface[] */
    protected $filters = [];

    /**
     * @param WidgetProviderFilterInterface $filter
     */
    public function addFilter(WidgetProviderFilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @param  QueryBuilder    $queryBuilder
     * @param  WidgetOptionBag $widgetOptions
     * @return Query
     */
    public function filter(QueryBuilder $queryBuilder, WidgetOptionBag $widgetOptions)
    {
        foreach ($this->filters as $filter) {
            $filter->filter($queryBuilder, $widgetOptions);
        }

        return $this->aclHelper->apply($queryBuilder);
    }
}
