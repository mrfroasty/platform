<?php

namespace Oro\Bundle\FilterBundle\Form\Type\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\FilterBundle\Form\Type\DateTimeRangeType;

class DateTimeRangeFilterType extends AbstractDateFilterType
{
    const NAME = 'oro_type_datetime_range_filter';

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return DateRangeFilterType::NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            array(
                'field_type' => DateTimeRangeType::NAME,
                'widget_options' => [
                    'showDatevariables' => true,
                    'showTime' => true,
                    'showTimepicker' => true,
                ]
            )
        );
    }
}
