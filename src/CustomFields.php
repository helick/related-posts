<?php

namespace Helick\RelatedPosts;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Helick\RelatedPosts\Contracts\Bootable;

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
         * Control the post types that should get the related posts support.
         *
         * @param array $postTypes
         */
        $postTypes = apply_filters('helick_related_posts_supported_post_types', ['post']);

        $associationTypes = array_map(function (string $postType) {
            return [
                'type'      => 'post',
                'post_type' => $postType,
            ];
        }, $postTypes);

        Container::make('post_meta', __('Related Posts', DOMAIN))
                 ->where('post_type', 'IN', (array)$postTypes)
                 ->add_fields([
                     Field::make('association', 'helick_related_posts', __('Manually selected related posts', DOMAIN))
                          ->set_help_text(__('By default, 10 related posts are dynamically generated.', DOMAIN))
                          ->set_types([$associationTypes]),
                 ]);
    }
}
