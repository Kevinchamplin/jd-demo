<?php

declare(strict_types=1);

namespace Xebia\AuthorityEngine\PostTypes;

/**
 * Xebia Insight Custom Post Type
 *
 * Registers and configures the xebia_insight custom post type
 * for storing AI-enhanced content insights.
 *
 * @package Xebia\AuthorityEngine\PostTypes
 */
final class InsightPostType
{
    /**
     * Post type slug
     */
    public const POST_TYPE = 'xebia_insight';

    /**
     * Post type singular name
     */
    public const SINGULAR_NAME = 'Insight';

    /**
     * Post type plural name
     */
    public const PLURAL_NAME = 'Insights';

    /**
     * Taxonomy slug for insight categories
     */
    public const TAXONOMY = 'insight_category';

    /**
     * Register the custom post type and taxonomy
     */
    public function register(): void
    {
        $this->registerPostType();
        $this->registerTaxonomy();
    }

    /**
     * Register the xebia_insight post type
     */
    private function registerPostType(): void
    {
        $labels = [
            'name' => __(self::PLURAL_NAME, 'xebia-authority-engine'),
            'singular_name' => __(self::SINGULAR_NAME, 'xebia-authority-engine'),
            'menu_name' => __('Authority Insights', 'xebia-authority-engine'),
            'name_admin_bar' => __(self::SINGULAR_NAME, 'xebia-authority-engine'),
            'add_new' => __('Add New', 'xebia-authority-engine'),
            'add_new_item' => __('Add New Insight', 'xebia-authority-engine'),
            'new_item' => __('New Insight', 'xebia-authority-engine'),
            'edit_item' => __('Edit Insight', 'xebia-authority-engine'),
            'view_item' => __('View Insight', 'xebia-authority-engine'),
            'all_items' => __('All Insights', 'xebia-authority-engine'),
            'search_items' => __('Search Insights', 'xebia-authority-engine'),
            'parent_item_colon' => __('Parent Insights:', 'xebia-authority-engine'),
            'not_found' => __('No insights found.', 'xebia-authority-engine'),
            'not_found_in_trash' => __('No insights found in Trash.', 'xebia-authority-engine'),
            'featured_image' => __('Insight Cover Image', 'xebia-authority-engine'),
            'set_featured_image' => __('Set cover image', 'xebia-authority-engine'),
            'remove_featured_image' => __('Remove cover image', 'xebia-authority-engine'),
            'use_featured_image' => __('Use as cover image', 'xebia-authority-engine'),
            'archives' => __('Insight archives', 'xebia-authority-engine'),
            'insert_into_item' => __('Insert into insight', 'xebia-authority-engine'),
            'uploaded_to_this_item' => __('Uploaded to this insight', 'xebia-authority-engine'),
            'filter_items_list' => __('Filter insights list', 'xebia-authority-engine'),
            'items_list_navigation' => __('Insights list navigation', 'xebia-authority-engine'),
            'items_list' => __('Insights list', 'xebia-authority-engine'),
        ];

        $args = [
            'labels' => $labels,
            'description' => __('AI-enhanced authority insights for Xebia.', 'xebia-authority-engine'),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-lightbulb',
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => [
                'title',
                'editor',
                'author',
                'thumbnail',
                'excerpt',
                'comments',
                'revisions',
                'custom-fields',
            ],
            'has_archive' => true,
            'rewrite' => [
                'slug' => 'insights',
                'with_front' => true,
                'feeds' => true,
                'pages' => true,
            ],
            'query_var' => true,
            'can_export' => true,
            'delete_with_user' => false,
            'show_in_rest' => true, // Enable Gutenberg editor
            'rest_base' => 'insights',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'show_in_graphql' => true,
            'graphql_single_name' => 'insight',
            'graphql_plural_name' => 'insights',
        ];

        register_post_type(self::POST_TYPE, $args);
    }

    /**
     * Register the insight_category taxonomy
     */
    private function registerTaxonomy(): void
    {
        $labels = [
            'name' => __('Insight Categories', 'xebia-authority-engine'),
            'singular_name' => __('Insight Category', 'xebia-authority-engine'),
            'search_items' => __('Search Categories', 'xebia-authority-engine'),
            'all_items' => __('All Categories', 'xebia-authority-engine'),
            'parent_item' => __('Parent Category', 'xebia-authority-engine'),
            'parent_item_colon' => __('Parent Category:', 'xebia-authority-engine'),
            'edit_item' => __('Edit Category', 'xebia-authority-engine'),
            'update_item' => __('Update Category', 'xebia-authority-engine'),
            'add_new_item' => __('Add New Category', 'xebia-authority-engine'),
            'new_item_name' => __('New Category Name', 'xebia-authority-engine'),
            'menu_name' => __('Categories', 'xebia-authority-engine'),
            'view_item' => __('View Category', 'xebia-authority-engine'),
            'popular_items' => __('Popular Categories', 'xebia-authority-engine'),
            'separate_items_with_commas' => __('Separate categories with commas', 'xebia-authority-engine'),
            'add_or_remove_items' => __('Add or remove categories', 'xebia-authority-engine'),
            'choose_from_most_used' => __('Choose from the most used categories', 'xebia-authority-engine'),
            'not_found' => __('No categories found.', 'xebia-authority-engine'),
            'no_terms' => __('No categories', 'xebia-authority-engine'),
            'items_list_navigation' => __('Categories list navigation', 'xebia-authority-engine'),
            'items_list' => __('Categories list', 'xebia-authority-engine'),
            'back_to_items' => __('&larr; Back to Categories', 'xebia-authority-engine'),
        ];

        $args = [
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
            'show_in_rest' => true,
            'rest_base' => 'insight-categories',
            'rewrite' => [
                'slug' => 'insight-category',
                'with_front' => true,
                'hierarchical' => true,
            ],
            'show_in_graphql' => true,
            'graphql_single_name' => 'insightCategory',
            'graphql_plural_name' => 'insightCategories',
        ];

        register_taxonomy(self::TAXONOMY, [self::POST_TYPE], $args);
    }

    /**
     * Get the post type slug
     */
    public function getPostType(): string
    {
        return self::POST_TYPE;
    }

    /**
     * Get the taxonomy slug
     */
    public function getTaxonomy(): string
    {
        return self::TAXONOMY;
    }
}
