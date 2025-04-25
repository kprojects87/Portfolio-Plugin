<?php
/*
Plugin Name: Portfolio Manager
Description: A custom plugin to manage portfolio items.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Register Portfolio Custom Post Type
function portfolio_custom_post_type() {
    $args = array(
        'label' => 'Portfolio',
        'public' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'has_archive' => true,
        'menu_icon' => 'dashicons-portfolio',
    );
    register_post_type('portfolio', $args);
}
add_action('init', 'portfolio_custom_post_type');

// Add Custom Meta Fields
function portfolio_meta_boxes() {
    add_meta_box(
        'portfolio_meta_box',
        'Project Details',
        'portfolio_meta_box_callback',
        'portfolio',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'portfolio_meta_boxes');

function portfolio_meta_box_callback($post) {
    $project_name = get_post_meta($post->ID, '_project_name', true);
    $completion_date = get_post_meta($post->ID, '_completion_date', true);
    $project_url = get_post_meta($post->ID, '_project_url', true);
    ?>
    <p>
        <label for="project_name">Project Name:</label>
        <input type="text" id="project_name" name="project_name" value="<?php echo esc_attr($project_name); ?>" />
    </p>
    <p>
        <label for="completion_date">Completion Date:</label>
        <input type="date" id="completion_date" name="completion_date" value="<?php echo esc_attr($completion_date); ?>" />
    </p>
    <p>
        <label for="project_url">Project URL:</label>
        <input type="url" id="project_url" name="project_url" value="<?php echo esc_attr($project_url); ?>" />
    </p>
    <?php
}

// Save Portfolio Meta Fields
function save_portfolio_meta($post_id) {
    if (array_key_exists('project_name', $_POST)) {
        update_post_meta($post_id, '_project_name', sanitize_text_field($_POST['project_name']));
    }
    if (array_key_exists('completion_date', $_POST)) {
        update_post_meta($post_id, '_completion_date', sanitize_text_field($_POST['completion_date']));
    }
    if (array_key_exists('project_url', $_POST)) {
        update_post_meta($post_id, '_project_url', esc_url_raw($_POST['project_url']));
    }
}
add_action('save_post', 'save_portfolio_meta');

// Shortcode to Display Portfolio Items
function portfolio_shortcode() {
    $query = new WP_Query(array('post_type' => 'portfolio', 'posts_per_page' => -1));
    $output = '<div class="portfolio-grid">';
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $project_name = get_post_meta(get_the_ID(), '_project_name', true);
            $completion_date = get_post_meta(get_the_ID(), '_completion_date', true);
            $project_url = get_post_meta(get_the_ID(), '_project_url', true);
            $output .= '<div class="portfolio-item">';
            $output .= '<h3>' . esc_html($project_name) . '</h3>';
            $output .= '<p>Completion Date: ' . esc_html($completion_date) . '</p>';
            $output .= '<p><a href="' . esc_url($project_url) . '" target="_blank">View Project</a></p>';
            $output .= '</div>';
        }
    } else {
        $output .= '<p>No portfolio items found.</p>';
    }
    
    $output .= '</div>';
    wp_reset_postdata();
    
    return $output;
}
add_shortcode('portfolio', 'portfolio_shortcode');
?>
