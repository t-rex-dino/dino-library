<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dino\Core\ServiceTagRegistry;
use Dino\Core\ServiceGroup;
use Dino\Core\ServiceContainer;
use Dino\Core\Utils\InstanceFactory;

echo "🚀 Dino Library - Service Tagging Demo" . PHP_EOL;
echo "=========================================" . PHP_EOL;

$registry = new ServiceTagRegistry();

// Basic tagging
$registry->addTag('logger', 'utility');
$registry->addTag('cache', 'utility');
$registry->addTag('mailer', 'communication');

echo "📌 Services tagged with 'utility': " . implode(', ', $registry->getServicesByTag('utility')) . PHP_EOL;

// ServiceGroup usage
$group = new ServiceGroup('communication');
$group->addService('mailer');
$group->addService('notifier');

echo "📦 Services in group 'communication': " . implode(', ', $group->getServices()) . PHP_EOL;

// Advanced integration with ServiceContainer
$container = new ServiceContainer();
$container->addFactory('logger', new InstanceFactory(new class {
    public function log($message) { echo "LOG: $message\n"; }
}));
$container->addFactory('mailer', new InstanceFactory(new class {
    public function send($to) { echo "Sending email to $to\n"; }
}));

$registry->tagService($container, 'logger', ['utility', 'core']);
$registry->tagService($container, 'mailer', ['communication', 'core']);

echo "🔧 Core services:" . PHP_EOL;
foreach ($registry->getTaggedServices($container, 'core') as $name => $service) {
    echo " - $name" . PHP_EOL;
}
