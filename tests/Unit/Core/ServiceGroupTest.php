<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Dino\Core\ServiceGroup;

final class ServiceGroupTest extends TestCase
{
    public function testCanAddAndRetrieveServices(): void
    {
        $group = new ServiceGroup('storage');
        $group->addService('cache');
        $group->addService('database');

        $services = $group->getServices();
        $this->assertEqualsCanonicalizing(['cache', 'database'], $services);
    }

    public function testGroupNameIsStoredCorrectly(): void
    {
        $group = new ServiceGroup('network');
        $this->assertEquals('network', $group->getName());
    }

    public function testMetadataManagement(): void
    {
        $group = new ServiceGroup('analytics', ['version' => '1.0']);
        $this->assertEquals(['version' => '1.0'], $group->getMetadata());

        $group->setMetadata(['version' => '2.0']);
        $this->assertEquals(['version' => '2.0'], $group->getMetadata());
    }

    public function testServicesWithOptions(): void
    {
        $group = new ServiceGroup('communication');
        $group->addService('mailer', ['priority' => 'high']);
        $group->addService('notifier', ['priority' => 'low']);

        $servicesWithOptions = $group->getServicesWithOptions();
        $this->assertArrayHasKey('mailer', $servicesWithOptions);
        $this->assertArrayHasKey('notifier', $servicesWithOptions);
        $this->assertEquals('high', $servicesWithOptions['mailer']['priority']);
    }
}
