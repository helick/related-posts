<?php

namespace Helick\RelatedPosts\Contracts;

interface Bootable
{
    /**
     * Boot the service.
     *
     * @return void
     */
    public static function boot(): void;
}
