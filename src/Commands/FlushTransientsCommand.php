<?php

namespace Helick\RelatedPosts\Commands;

use Helick\RelatedPosts\Contracts\Bootable;
use WP_CLI;

final class FlushTransientsCommand implements Bootable
{
    /**
     * Boot the service.
     *
     * @return void
     */
    public static function boot(): void
    {
        WP_CLI::add_command('helick-related-posts flush-transients', static::class);
    }

    /**
     * Flush transient data.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $this->deleteTransients();

        $this->deleteTransientTimeouts();
    }

    /**
     * Delete transients.
     *
     * @return void
     */
    private function deleteTransients(): void
    {
        global $wpdb;

        $query = "DELETE FROM {$wpdb->options} WHERE `option_name` REGEXP '^_transient_helick_related_posts_[0-9]+_[^_]+$'";
        $count = $wpdb->query($query);

        WP_CLI::success("{$count} related posts transients deleted.");
    }

    /**
     * Delete transient timeouts.
     *
     * @return void
     */
    private function deleteTransientTimeouts(): void
    {
        global $wpdb;

        $query = "DELETE FROM {$wpdb->options} WHERE `option_name` REGEXP '^_transient_timeout_helick_related_posts_[0-9]+_[^_]+$'";
        $count = $wpdb->query($query);

        WP_CLI::success("{$count} related posts transient timeouts deleted.");
    }
}
