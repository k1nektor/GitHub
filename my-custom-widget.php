<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class My_Custom_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'my_custom_widget';
    }

    public function get_title() {
        return __( 'My Custom Widget', 'my-elementor-extension' );
    }

    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'my-elementor-extension' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'post_type',
            [
                'label' => __( 'Select Post Type', 'my-elementor-extension' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_post_types(),
                'default' => 'post',
            ]
        );

        $this->add_control(
            'taxonomy',
            [
                'label' => __( 'Select Taxonomy', 'my-elementor-extension' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_taxonomies(),
                'default' => '',
                'label_block' => true,
                'multiple' => false,
            ]
        );

        $this->add_control(
            'terms',
            [
                'label' => __( 'Select Terms', 'my-elementor-extension' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => [], // Значення будуть динамічно завантажуватись через AJAX
                'multiple' => true,
                'label_block' => true,
                'condition' => [
                    'taxonomy!' => '',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
    
        // Переконайтеся, що користувач вибрав таксономію та терміни
        if (!empty($settings['taxonomy']) && !empty($settings['terms'])) {
            $args = [
                'post_type' => $settings['post_type'],
                'tax_query' => [
                    [
                        'taxonomy' => $settings['taxonomy'],
                        'field' => 'term_id',
                        'terms' => $settings['terms'],
                    ],
                ],
            ];
    
            $query = new WP_Query($args);
    
            if ($query->have_posts()) {
                echo '<div class="elementor-posts-container">';
                while ($query->have_posts()) {
                    $query->the_post();
                    // HTML-структура для кожного поста
                    echo '<div class="elementor-post">';
                    echo '<h2>' . get_the_title() . '</h2>';
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('medium');
                    }
                    echo '<div>' . get_the_excerpt() . '</div>';
                    echo '<a href="' . get_permalink() . '">Read More</a>';
                    echo '</div>';
                }
                echo '</div>';
                wp_reset_postdata();
            } else {
                echo '<p>No posts found.</p>';
            }
        }
    }    

    private function get_post_types() {
        $post_types = get_post_types(['public' => true], 'objects');
        $options = [];
        foreach ($post_types as $post_type) {
            $options[$post_type->name] = $post_type->label;
        }
        return $options;
    }

    private function get_taxonomies() {
        $taxonomies = get_taxonomies(['public' => true], 'objects');
        $options = [];
        foreach ($taxonomies as $taxonomy) {
            $options[$taxonomy->name] = $taxonomy->label;
        }
        return $options;
    }
}
