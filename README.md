# Helick Related Posts

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE.md)
[![Quality Score][ico-code-quality]][link-code-quality]

The plugin provides a simple related posts functionality with the following features:
- Admin UI for manually overriding related posts
- ElasticPress integration

## Requirements

Make sure all dependencies have been installed before moving on:

* [PHP](http://php.net/manual/en/install.php) >= 7.1
* [Composer](https://getcomposer.org/download/)

## Install

Via Composer:

``` bash
$ composer require helick/related-posts
```

## Usage

The plugin exposes a single function that returns a list of post IDs.

``` php
$postIds = Helick\RelatedPosts\get(int $postId, array $args = []);
```

**$postId** is the ID of the post to get related content for.

**$args** allows you some control over the posts that are returned:

- int **limit**: defaults to `10`
- array **post_types**: array of post types to limit results to, defaults to `['post']`
- array **taxonomies**: array of taxonomies to compare against, defaults to `['category']`
- array **terms**: array of `WP_Term` objects, results will match these terms
- array **terms_not_in**: array of `WP_Term` objects, results will not match these terms
- bool **ep_integrate**: if true then ElasticPress is used to get the results, defaults to `defined('EP_VERSION')`

### Custom post type support

Control supported post types:

``` php
add_filter('helick_related_posts_supported_post_types', function (array $postTypes) {
    $postTypes[] = 'your-custom-post-type';

    return $postTypes;
});
```

Control associated post types:

``` php
add_filter('helick_related_posts_associated_post_types', function (array $postTypes) {
    $postTypes[] = 'your-custom-post-type';

    return $postTypes;
});
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email evgenii@helick.io instead of using the issue tracker.

## Credits

- [Evgenii Nasyrov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/helick/related-posts.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/helick/related-posts.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/helick/related-posts.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/helick/related-posts
[link-code-quality]: https://scrutinizer-ci.com/g/helick/related-posts
[link-downloads]: https://packagist.org/packages/helick/related-posts
[link-author]: https://github.com/nasyrov
[link-contributors]: ../../contributors
