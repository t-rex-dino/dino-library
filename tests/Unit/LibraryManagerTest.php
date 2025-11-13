<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Dino\Core\LibraryManager;
use Dino\Exceptions\ServiceNotFoundException;

final class LibraryManagerTest extends TestCase
{
    public function testRegisterAndRetrieveService(): void
    {
        $manager = new LibraryManager();
        $service = new stdClass();

        $manager->register('test', $service);
        $this->assertSame($service, $manager->get('test'));
    }

    public function testHasService(): void
    {
        $manager = new LibraryManager();
        $manager->register('test', new stdClass());

        $this->assertTrue($manager->has('test'));
        $this->assertFalse($manager->has('missing'));
    }

    public function testGetMissingServiceThrowsException(): void
    {
        $this->expectException(ServiceNotFoundException::class);

        $manager = new LibraryManager();
        $manager->get('unknown');
    }
}
