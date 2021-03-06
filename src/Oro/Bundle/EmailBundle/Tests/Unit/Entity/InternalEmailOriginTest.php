<?php

namespace Oro\Bundle\EmailBundle\Tests\Unit\Entity;

use Oro\Bundle\EmailBundle\Entity\InternalEmailOrigin;

class InternalEmailOriginTest extends \PHPUnit\Framework\TestCase
{
    public function testNameGetterAndSetter()
    {
        $entity = new InternalEmailOrigin();
        $entity->setName('test');
        $this->assertEquals('test', $entity->getName());
    }
}
