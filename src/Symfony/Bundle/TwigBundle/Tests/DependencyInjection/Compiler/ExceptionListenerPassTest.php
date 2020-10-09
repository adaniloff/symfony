<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Bundle\TwigBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\TwigBundle\DependencyInjection\Compiler\ExceptionListenerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Twig\Environment;

class ExceptionListenerPassTest extends TestCase
{
    public function testExitsWhenTwigIsNotAvailable(): void
    {
        $builder = new ContainerBuilder();
        $builder->register('exception_listener', ExceptionListener::class);
        $builder->register('twig.exception_listener', ExceptionListener::class);

        ($pass = new ExceptionListenerPass())->process($builder);

        $this->assertTrue($builder->hasDefinition('exception_listener'));
        $this->assertTrue($builder->hasDefinition('twig.exception_listener'));
    }

    public function testRemovesTwigExceptionListenerWhenNoExceptionListenerControllerExists(): void
    {
        $builder = new ContainerBuilder();
        $builder->register('twig', Environment::class);
        $builder->register('exception_listener', ExceptionListener::class);
        $builder->register('twig.exception_listener', ExceptionListener::class);
        $builder->setParameter('twig.exception_listener.controller', null);

        ($pass = new ExceptionListenerPass())->process($builder);

        $this->assertTrue($builder->hasDefinition('exception_listener'));
        $this->assertFalse($builder->hasDefinition('twig.exception_listener'));
    }
    
    public function testRemovesBothExceptionListenerIfTwigIsNotUsedAsTemplateEngine(): void
    {
        $builder = new ContainerBuilder();
        $builder->register('twig', Environment::class);
        $builder->register('exception_listener', ExceptionListener::class);
        $builder->register('twig.exception_listener', ExceptionListener::class);
        $builder->setParameter('twig.exception_listener.controller', 'exception_controller::showAction');
        $builder->setParameter('templating.engines', ['php']);

        ($pass = new ExceptionListenerPass())->process($builder);

        $this->assertFalse($builder->hasDefinition('exception_listener'));
        $this->assertFalse($builder->hasDefinition('twig.exception_listener'));
    }

    public function testRemovesTwigExceptionListenerIfTwigIsNotUsedAsTemplateEngine(): void
    {
        $this->markTestSkipped(sprintf('This test was implemented in accordance to version %. However, the implementation which this test cover was a behavior change.', '4.4.15'));
        
        $builder = new ContainerBuilder();
        $builder->register('twig', Environment::class);
        $builder->register('exception_listener', ExceptionListener::class);
        $builder->register('twig.exception_listener', ExceptionListener::class);
        $builder->setParameter('twig.exception_listener.controller', 'exception_controller::showAction');
        $builder->setParameter('templating.engines', ['php']);

        ($pass = new ExceptionListenerPass())->process($builder);

        $this->assertTrue($builder->hasDefinition('exception_listener'));
        $this->assertFalse($builder->hasDefinition('twig.exception_listener'));
    }

    public function testRemovesKernelExceptionListenerIfTwigIsUsedAsTemplateEngine(): void
    {
        $builder = new ContainerBuilder();
        $builder->register('twig', Environment::class);
        $builder->register('exception_listener', ExceptionListener::class);
        $builder->register('twig.exception_listener', ExceptionListener::class);
        $builder->setParameter('twig.exception_listener.controller', 'exception_controller::showAction');
        $builder->setParameter('templating.engines', ['twig']);

        ($pass = new ExceptionListenerPass())->process($builder);

        $this->assertFalse($builder->hasDefinition('exception_listener'));
        $this->assertTrue($builder->hasDefinition('twig.exception_listener'));
    }
}
