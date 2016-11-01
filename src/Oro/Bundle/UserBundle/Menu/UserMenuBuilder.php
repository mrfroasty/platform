<?php

namespace Oro\Bundle\UserBundle\Menu;

use Symfony\Component\Security\Core\SecurityContextInterface;

use Knp\Menu\ItemInterface;

use Oro\Bundle\NavigationBundle\Menu\BuilderInterface;

class UserMenuBuilder implements BuilderInterface
{
    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ItemInterface $menu, array $options = array(), $alias = null)
    {
        $menu->setExtra('type', 'dropdown');
        /* Disabled status menu till active stream will be implemented (BAP-617)
         $menu->addChild(
            'Update status',
            array(
                 'route'      => 'oro_user_status_create',
                 'attributes' => array(
                     'class' => 'update-status'
                 ),
                'linkAttributes' => array(
                    'class' => 'no-hash'
                )
            )
        );*/

        $menu->addChild('divider-user-before-logout')
            ->setLabel('')
            ->setExtra('divider', true);
        $menu->addChild(
            'Logout',
            [
                'route' => 'oro_user_security_logout',
                'linkAttributes' => [
                    'class' => 'no-hash'
                ]
            ]
        );
    }
}
