<?php

namespace Helick\RelatedPosts;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Helick\Contracts\Bootable;

final class CustomFields implements Bootable
{
    /**
     * Boot the service.
     *
     * @return void
     */
    public static function boot(): void
    {
        $self = new static;

        add_action('carbon_fields_register_fields', [$self, 'register']);
    }

    /**
     * Register the custom fields.
     *
     * @return void
     */
    public function register(): void
    {
        /**
         * Control the supported post types.
         *
         * @param array $supportedPostTypes
         */
        $supportedPostTypes = apply_filters('helick_related_posts_supported_post_types', ['post']);

        /**
         * Control the associated post types.
         *
         * @param array $associatedPostTypes
         */
        $associatedPostTypes = apply_filters('helick_related_posts_associated_post_types', ['post']);

        $associationTypes = array_map(function (string $postType) {
            return [
                'type'      => 'post',
                'post_type' => $postType,
            ];
        }, $associatedPostTypes);

        Container::make('post_meta', __('Related Posts', DOMAIN))
                 ->where('post_type', 'IN', (array)$supportedPostTypes)
                 ->add_fields([
                     Field::make('association', 'helick_related_posts', __('Manually selected related posts', DOMAIN))
                          ->set_help_text(__('By default, 10 related posts are dynamically generated.', DOMAIN))
                          ->set_types($associationTypes),
                 ]);
    }
}
