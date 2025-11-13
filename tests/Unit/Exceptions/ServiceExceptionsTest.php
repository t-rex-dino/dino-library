<?php

namespace Dino\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Dino\Exceptions\ServiceNotFoundException;
use Dino\Exceptions\CircularDependencyException;
use Dino\Exceptions\ServiceResolutionException;
use Dino\Exceptions\ServiceFactoryException;
use Dino\Core\ErrorCodes;

class ServiceExceptionsTest extends TestCase
{
    public function testServiceNotFoundException()
    {
        $exception = new ServiceNotFoundException('logger', ['available_services' => ['cache', 'database']]);
        $this->assertEquals(ErrorCodes::SERVICE_NOT_FOUND, $exception->getErrorCode());
        $this->assertStringContainsString('logger', $exception->getErrorMessage());
        $this->assertArrayHasKey('available_services', $exception->getContext());
    }

    public function testCircularDependencyException()
    {
        $exception = new CircularDependencyException('ServiceA', ['dependency_chain' => ['A','B','C','A']]);
        $this->assertEquals(ErrorCodes::SERVICE_CIRCULAR_DEPENDENCY, $exception->getErrorCode());
        $this->assertEquals('CRITICAL', $exception->getSeverity());
    }

    public function testServiceResolutionException()
    {
        $exception = new ServiceResolutionException('mailer');
        $this->assertEquals(ErrorCodes::SERVICE_RESOLUTION_FAILED, $exception->getErrorCode());
        $this->assertStringContainsString('mailer', $exception->getErrorMessage());
    }

    public function testServiceFactoryException()
    {
        $exception = new ServiceFactoryException('MailerFactory');
        $this->assertEquals(ErrorCodes::SERVICE_FACTORY_ERROR, $exception->getErrorCode());
        $this->assertStringContainsString('MailerFactory', $exception->getErrorMessage());
    }
}
