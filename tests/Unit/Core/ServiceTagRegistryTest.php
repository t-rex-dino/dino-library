<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Dino\Core\ServiceTagRegistry;
use Dino\Core\ServiceContainer;
use Dino\Core\Utils\InstanceFactory;

final class ServiceTagRegistryTest extends TestCase
{
    public function testCanRegisterAndRetrieveTags(): void
    {
        $registry = new ServiceTagRegistry();
        $registry->addTag('logger', 'utility');
        $registry->addTag('logger', 'debug');

        $tags = $registry->getTags('logger');
        $this->assertContains('utility', $tags);
        $this->assertContains('debug', $tags);
    }

    public function testCanFindServicesByTag(): void
    {
        $registry = new ServiceTagRegistry();
        $registry->addTag('logger', 'utility');
        $registry->addTag('cache', 'utility');

        $services = $registry->getServicesByTag('utility');
        $this->assertEqualsCanonicalizing(['logger', 'cache'], $services);
    }

    public function testTaggedServicesIntegrationWithContainer(): void
    {
        $container = new ServiceContainer();
        $container->addFactory('logger', new InstanceFactory(new stdClass()));
        $container->addFactory('cache', new InstanceFactory(new stdClass()));

        $registry = new ServiceTagRegistry();
        $registry->tagService($container, 'logger', ['utility', 'logging']);
        $registry->tagService($container, 'cache', ['utility', 'performance']);

        $utilityServices = $registry->getTaggedServices($container, 'utility');
        $this->assertCount(2, $utilityServices);
        $this->assertArrayHasKey('logger', $utilityServices);
        $this->assertArrayHasKey('cache', $utilityServices);
    }
}
