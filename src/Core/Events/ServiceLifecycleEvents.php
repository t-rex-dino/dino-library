<?php
declare(strict_types=1);

namespace Dino\Core\Events;

class ServiceLifecycleEvents
{
    // Service Container Events
    public const BEFORE_CREATE = 'service.before_create';
    public const AFTER_CREATE = 'service.after_create';
    public const BEFORE_INIT = 'service.before_init';
    public const AFTER_INIT = 'service.after_init';
    public const BEFORE_DESTROY = 'service.before_destroy';
    public const AFTER_DESTROY = 'service.after_destroy';

    // Configuration Events
    public const BEFORE_CONFIG_LOAD = 'config.before_load';
    public const AFTER_CONFIG_LOAD = 'config.after_load';
    public const BEFORE_CONFIG_MERGE = 'config.before_merge';
    public const AFTER_CONFIG_MERGE = 'config.after_merge';

    // Cache Events
    public const CACHE_HIT = 'cache.hit';
    public const CACHE_MISS = 'cache.miss';
    public const CACHE_SET = 'cache.set';
    public const CACHE_DELETE = 'cache.delete';
}