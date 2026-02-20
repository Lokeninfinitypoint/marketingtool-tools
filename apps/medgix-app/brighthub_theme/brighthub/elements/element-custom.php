<?php 
use Elementor\Element_Base;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Schemes\Color;
use Elementor\Schemes\Typography;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;
use Elementor\Includes\Elements\PXL_Container;

defined('ABSPATH') || die();

class PXL_Elementor_Custom_Controls {
    public static function init() {
        // Section Fixed and Sticky Inner
        add_filter('elementor/frontend/container/should_render', '__return_true');
        add_action('elementor/element/after_section_end', function($element, $section_id) {
            if ($element->get_name() === 'container' && 'section_layout_additional_options' === $section_id) {
                $element->start_controls_section(
                    'pxl_container_sticky',
                    [
                        'label' => __('Container Fixed', 'brighthub'),
                        'tab' => \Elementor\Controls_Manager::TAB_LAYOUT,
                    ]
                );
                
                $element->add_control(
                    'pxl_container_sticky_top',
                    [
                        'label' => esc_html__('Enable Fixed?', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'default' => '',
                        'label_on' => esc_html__('Yes', 'brighthub'),
                        'label_off' => esc_html__('No', 'brighthub'),
                    ]
                );
                $element->add_control(
                    'pxl_container_sticky_position_mode',
                    [
                        'label' => esc_html__('Fixed Position', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'default' => 'top',
                        'options' => [
                            'top' => esc_html__('Top', 'brighthub'),
                            'bottom' => esc_html__('Bottom', 'brighthub'),
                        ],
                        'condition' => [
                            'pxl_container_sticky_top' => 'yes',
                        ],
                    ]
                );
                
                $element->add_control(
                    'pxl_container_sticky_top_position',
                    [
                        'label' => esc_html__('Offset (px)', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'default' => 30,
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 2000,
                            ],
                        ],
                        'condition' => [
                            'pxl_container_sticky_top' => 'yes',
                        ],
                        'description' => esc_html__('Distance from top when fixed is active', 'brighthub'),
                    ]
                );
                
                $element->add_control(
                    'pxl_container_sticky_position',    
                    [
                        'label' => esc_html__('Trigger Position (px)', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'default' => 300,
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 20000,
                            ],
                        ],
                        'description' => esc_html__('Position from top where sticky effect starts', 'brighthub'),
                        'condition' => [
                            'pxl_container_sticky_top' => 'yes',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_container_sticky_footer_position',    
                    [
                        'label' => esc_html__('Hide On Footer (px)', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'default' => 300,
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 20000,
                            ],
                        ],
                        'description' => esc_html__('Hide on scroll to footer', 'brighthub'),
                        'condition' => [
                            'pxl_container_sticky_top' => 'yes',
                        ],
                    ]
                );
        
                $element->add_control(
                    'pxl_container_sticky_z_index',
                    [
                        'label' => esc_html__('Z-Index', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'default' => 999,
                        'condition' => [
                            'pxl_container_sticky_top' => 'yes', 
                        ],
                        'selectors' => [
                            '{{WRAPPER}}.pxl-sticky-active' => 'z-index: {{VALUE}};',
                        ],
                    ]
                );
        
                $element->end_controls_section();
            }
        }, 10, 2);

        add_action('elementor/frontend/container/before_render', function($element) {
            $settings = $element->get_settings_for_display();
            if (!empty($settings['pxl_container_sticky_top']) && $settings['pxl_container_sticky_top'] === 'yes') {
                $sticky_offset = !empty($settings['pxl_container_sticky_top_position']) ? $settings['pxl_container_sticky_top_position'] : 30;
                $sticky_mode = !empty($settings['pxl_container_sticky_position_mode']) ? $settings['pxl_container_sticky_position_mode'] : 'top';
                $trigger_position = !empty($settings['pxl_container_sticky_position']) ? $settings['pxl_container_sticky_position'] : 300;
                $hide_on_footer = !empty($settings['pxl_container_sticky_footer_position']) ? $settings['pxl_container_sticky_footer_position'] : 300;

                $element->add_render_attribute('_wrapper', [
                    'data-pxl-sticky' => 'true',
                    'data-sticky-mode' => $sticky_mode,
                    'data-sticky-offset' => $sticky_offset,
                    'data-trigger-position' => $trigger_position,
                    'data-ft-hide' => $hide_on_footer,
                    'class' => 'pxl-sticky-container' 
                ]);
            }
        });

        add_action('elementor/element/after_section_end', function($element, $section_id) {
            if ($element->get_name() === 'container' && 'section_layout_additional_options' === $section_id) {
                $element->start_controls_section(
                    'pxl_container_sticky_inner',
                    [
                        'label' => __('Container Sticky', 'brighthub'),
                        'tab' => \Elementor\Controls_Manager::TAB_LAYOUT,
                    ]
                );
        
                $element->add_control(
                    'pxl_container_sticky_inner_top',
                    [
                        'label' => esc_html__('Enable Sticky?', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'default' => '',
                        'label_on' => esc_html__('Yes', 'brighthub'),
                        'label_off' => esc_html__('No', 'brighthub'),
                    ]
                );
                $element->add_control(
                    'pxl_container_sticky_inner_type',
                    [
                        'label' => esc_html__('Sticky Type', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'top' => esc_html__('Top', 'brighthub'),
                            'bottom' => esc_html__('Bottom', 'brighthub'),
                        ],
                        'default' => 'top',
                        'condition' => [
                            'pxl_container_sticky_inner_top' => 'yes',
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_sticky_inner_top_position',
                    [
                        'label' => esc_html__('Sticky Top Space (px)', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'default' => 30,
                        'condition' => [
                            'pxl_container_sticky_inner_type' => 'top',
                        ],
                        'selectors' => [
                            '{{WRAPPER}}.pxl-sticky-inner-container' => 'position: sticky; top: {{VALUE}}px; bottom: unset;',
                        ],
                        'description' => esc_html__('Distance from top when sticky is active', 'brighthub'),
                    ]
                );

                $element->add_control(
                    'pxl_container_sticky_inner_bottom_position',
                    [
                        'label' => esc_html__('Sticky Bottom Space (px)', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'default' => 30,
                        'condition' => [
                            'pxl_container_sticky_inner_type' => 'bottom',
                        ],
                        'selectors' => [
                            '{{WRAPPER}}.pxl-sticky-inner-container' => 'position: sticky; top: unset; bottom: {{VALUE}}px; top: {{VALUE}}px;',
                        ],
                        'description' => esc_html__('Distance from top when sticky is active', 'brighthub'),
                    ]
                );

                $element->add_control(
                    'pxl_container_sticky_inner_scale',
                    [
                        'label' => esc_html__('Scale', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'default' => '',
                        'label_on' => esc_html__('Yes', 'brighthub'),
                        'label_off' => esc_html__('No', 'brighthub'),
                        'description' => esc_html__('Scale sibling item sticky before in same the container', 'brighthub'),
                    ]
                );

                $element->add_control(
                    'pxl_container_sticky_inner_scale_size',
                    [
                        'label' => esc_html__('Scale', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'description' => esc_html__('Scale width of the sibling item ex: from 100% to 90% follow default value', 'brighthub'),
                        'default' => 90,
                        'condition' => [
                            'pxl_container_sticky_inner_scale' => 'yes',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_container_sticky_inner_blur',
                    [
                        'label' => esc_html__('Blur', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'default' => '',
                        'label_on' => esc_html__('Yes', 'brighthub'),
                        'label_off' => esc_html__('No', 'brighthub'),
                        'description' => esc_html__('Blur sibling item sticky before in same the container', 'brighthub'),
                    ]
                );

                $element->add_control(
                    'pxl_container_sticky_inner_scale_width',
                    [
                        'label' => esc_html__('Scale', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'description' => esc_html__('Scale width of the sibling item ex: from 100% to 90% follow default value', 'brighthub'),
                        'default' => 10,
                        'condition' => [
                            'pxl_container_sticky_inner_blur' => 'yes',
                        ],
                    ]
                );
        
                $element->end_controls_section();
            }
        }, 10, 2);
        
        add_action('elementor/frontend/container/before_render', function($element) {
            $settings = $element->get_settings_for_display();
            $scale_size = !empty($settings['pxl_container_sticky_inner_scale_size']) ? $settings['pxl_container_sticky_inner_scale_size'] : 90;
            $blur_width = !empty($settings['pxl_container_sticky_inner_blur_width']) ? $settings['pxl_container_sticky_inner_blur_width'] : 10;
        
            if (!empty($settings['pxl_container_sticky_inner_top']) && $settings['pxl_container_sticky_inner_top'] === 'yes') {
                $element->add_render_attribute('_wrapper', [
                    'class' => 'pxl-sticky-inner-container',
                    'data-pxl-sticky-inner-scale-size' => $scale_size,
                    'data-pxl-sticky-inner-blur-width' => $blur_width,
                ]);
            }
        });
        

        // Container Border Running
        add_action('elementor/element/after_section_end', function($element, $section_id) {
            if ($element->get_name() === 'container' && 'section_layout_additional_options' === $section_id) {
                $element->start_controls_section(
                    'pxl_container_max_width_title',
                    [
                        'label' => __('Container Max Width', 'brighthub'),
                        'tab' => \Elementor\Controls_Manager::TAB_LAYOUT,
                    ]
                );
    
                $element->add_control(
                    'pxl_container_max_width',
                    [
                        'label' => esc_html__('Max Width', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'control_type' => 'responsive',
                        'size_units' => [ 'px' ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 2000,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} ' => 'max-width: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );
    
                $element->end_controls_section();
            }
        }, 10, 2);
        add_action('elementor/element/after_section_end', function($element, $section_id) {
            if ($element->get_name() === 'container' && 'section_layout_additional_options' === $section_id) {
                $element->start_controls_section(
                    'pxl_container_border_running',
                    [
                        'label' => __('Border Container Running', 'brighthub'),
                        'tab' => \Elementor\Controls_Manager::TAB_LAYOUT,
                    ]
                );
    
                $element->add_control(
                    'pxl_container_border_options',
                    [
                        'label' => esc_html__('Container Border Sides', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT2,
                        'multiple' => true,
                        'options' => [
                            'top'    => esc_html__('Top', 'brighthub'),
                            'bottom' => esc_html__('Bottom', 'brighthub'),
                            'left' => esc_html__('Left', 'brighthub'),
                            'right' => esc_html__('Right', 'brighthub'),
                        ],
                        'default' => [],
                    ]
                );
    
                $element->add_control(
                    'pxl_container_border_style',
                    [
                        'label' => esc_html__('Container Border Style', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            '1' => esc_html__('Style Dashed', 'brighthub'),
                            '2' => esc_html__('Style Solid', 'brighthub'),
                        ],
                        'default' => '1',
                        'condition' => [
                            'pxl_container_border_options!' => ''
                        ]
                    ]
                );

                $element->add_control(
                    'pxl_container_border_height',
                    [
                        'label' => esc_html__('Container Border Height', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px' ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 2000,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .pxl-container-border__item' => 'height: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );

                
                $element->add_control(
                    'pxl_container_border_color',
                    [
                        'label' => esc_html__('Container Border Color', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .pxl-container-border__item' => '--border-color: {{VALUE}};',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_container_border_dot_color',
                    [
                        'label' => esc_html__('Container Border Dot Color', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .pxl-container-border__item' => '--border-dot-color: {{VALUE}};',
                        ],
                        'separator' => 'before',
                    ]
                );
                $element->add_control(
                    'pxl_container_border_dot_width',
                    [
                        'label' => esc_html__('Container Border Dot Width', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px' ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 2000,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .pxl-container-border__item:before, {{WRAPPER}} .pxl-container-border__item:after' => 'width: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_border_dot_height',
                    [
                        'label' => esc_html__('Container Border Dot Height', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px' ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 2000,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .pxl-container-border__item:before, {{WRAPPER}} .pxl-container-border__item:after' => 'height: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );

                $element->end_controls_section();
            }
        }, 10, 2);
    
        add_filter('pxl_element_container/before-render', function($html, $settings) {
            if (!empty($settings['pxl_container_border_options']) && is_array($settings['pxl_container_border_options'])) {
                $sides = $settings['pxl_container_border_options'];
                $style = !empty($settings['pxl_container_border_style']) ? $settings['pxl_container_border_style'] : '1';
                $style_class = is_array($style) ? implode('-', $style) : $style;
    
                $html .= '<div class="pxl-container-border pxl-container-border__style-' . esc_attr($style_class) . '">';
                foreach ($sides as $side) {
                    $html .= '<span class="pxl-container-border__item pxl-container-border__item-' . esc_attr($side) . '"></span>';
                }
                $html .= '</div>';
            }
    
            return $html;
        }, 10, 2);

        add_action('elementor/element/after_section_end', function($element, $section_id) {
            if ($element->get_name() === 'container' && 'section_layout_additional_options' === $section_id) {
                $element->start_controls_section(
                    'pxl_container_line',
                    [
                        'label' => __('Line Container', 'brighthub'),
                        'tab' => \Elementor\Controls_Manager::TAB_LAYOUT,
                    ]
                );

                $element->add_control(
                    'pxl_container_line_o',
                    [
                        'label' => esc_html__('Container Line Position', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT2,
                        'multiple' => true,
                        'options' => [
                            'top'    => esc_html__('Top', 'brighthub'),
                            'bottom' => esc_html__('Bottom', 'brighthub'),
                            'right' => esc_html__('Right', 'brighthub'),
                            'left' => esc_html__('Left', 'brighthub'),
                            'horizontal' => esc_html__('Horizontal', 'brighthub'),
                            'vertical' => esc_html__('Vertical 1', 'brighthub'),
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_container_line_styles_heading',
                    [
                        'label' => esc_html__('Responsive Line Styles', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $element->add_control(
                    'pxl_container_line_laptop_heading',
                    [
                        'label' => esc_html__('Laptop Styles (>1366px)', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $element->add_control(
                    'pxl_container_line_s_laptop',
                    [
                        'label' => esc_html__('Laptop - Line Left/Right Style', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'gr-df'    => esc_html__('Default', 'brighthub'),
                            'gr-df-bold'    => esc_html__('Bold Than Default', 'brighthub'),
                            'gr-btt'    => esc_html__('Gradient Bottom To Top', 'brighthub'),
                            'gr-ttb' => esc_html__('Gradient Top To Bottom', 'brighthub'),
                            'clr' => esc_html__('A Color', 'brighthub'),
                        ],
                        'default' => 'gr-df'
                    ]
                );

                $element->add_control(
                    'pxl_container_line_srl_laptop',
                    [
                        'label' => esc_html__('Laptop - Line Top/Bottom Style', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'gr-df'    => esc_html__('Default', 'brighthub'),
                            'gr-df-bold'    => esc_html__('Bold Than Default', 'brighthub'),
                            'gr-rtl' => esc_html__('Gradient Right To Left', 'brighthub'),
                            'gr-ltr' => esc_html__('Gradient Left To Right', 'brighthub'),
                            'clr' => esc_html__('A Color', 'brighthub'),
                        ],
                        'default' => 'gr-df'
                    ]
                );

                $element->add_control(
                    'pxl_container_line_c_laptop',
                    [
                        'label' => esc_html__('Laptop - Line Color', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                    ]
                );

                $element->add_control(
                    'pxl_container_line_tablet_landscape_heading',
                    [
                        'label' => esc_html__('Tablet Landscape Styles (1025px - 1366px)', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $element->add_control(
                    'pxl_container_line_s_tablet_landscape',
                    [
                        'label' => esc_html__('Tablet Landscape - Line Left/Right Style', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'inherit' => esc_html__('Inherit', 'brighthub'),
                            'gr-df'    => esc_html__('Default', 'brighthub'),
                            'gr-df-bold'    => esc_html__('Bold Than Default', 'brighthub'),
                            'gr-btt'    => esc_html__('Gradient Bottom To Top', 'brighthub'),
                            'gr-ttb' => esc_html__('Gradient Top To Bottom', 'brighthub'),
                            'clr' => esc_html__('A Color', 'brighthub'),
                        ],
                        'default' => 'inherit'
                    ]
                );

                $element->add_control(
                    'pxl_container_line_srl_tablet_landscape',
                    [
                        'label' => esc_html__('Tablet Landscape - Line Top/Bottom Style', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'inherit' => esc_html__('Inherit', 'brighthub'),
                            'gr-df'    => esc_html__('Default', 'brighthub'),
                            'gr-df-bold'    => esc_html__('Bold Than Default', 'brighthub'),
                            'gr-rtl' => esc_html__('Gradient Right To Left', 'brighthub'),
                            'gr-ltr' => esc_html__('Gradient Left To Right', 'brighthub'),
                            'clr' => esc_html__('A Color', 'brighthub'),
                        ],
                        'default' => 'inherit'
                    ]
                );

                $element->add_control(
                    'pxl_container_line_c_tablet_landscape',
                    [
                        'label' => esc_html__('Tablet Landscape - Line Color', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'description' => esc_html__('Leave empty to inherit', 'brighthub'),
                    ]
                );

                $element->add_control(
                    'pxl_container_line_tablet_portrait_heading',
                    [
                        'label' => esc_html__('Tablet Portrait Styles (768px - 1024px)', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $element->add_control(
                    'pxl_container_line_s_tablet_portrait',
                    [
                        'label' => esc_html__('Tablet Portrait - Line Left/Right Style', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'inherit' => esc_html__('Inherit', 'brighthub'),
                            'gr-df'    => esc_html__('Default', 'brighthub'),
                            'gr-df-bold'    => esc_html__('Bold Than Default', 'brighthub'),
                            'gr-btt'    => esc_html__('Gradient Bottom To Top', 'brighthub'),
                            'gr-ttb' => esc_html__('Gradient Top To Bottom', 'brighthub'),
                            'clr' => esc_html__('A Color', 'brighthub'),
                        ],
                        'default' => 'inherit'
                    ]
                );

                $element->add_control(
                    'pxl_container_line_srl_tablet_portrait',
                    [
                        'label' => esc_html__('Tablet Portrait - Line Top/Bottom Style', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'inherit' => esc_html__('Inherit', 'brighthub'),
                            'gr-df'    => esc_html__('Default', 'brighthub'),
                            'gr-df-bold'    => esc_html__('Bold Than Default', 'brighthub'),
                            'gr-rtl' => esc_html__('Gradient Right To Left', 'brighthub'),
                            'gr-ltr' => esc_html__('Gradient Left To Right', 'brighthub'),
                            'clr' => esc_html__('A Color', 'brighthub'),
                        ],
                        'default' => 'inherit'
                    ]
                );

                $element->add_control(
                    'pxl_container_line_c_tablet_portrait',
                    [
                        'label' => esc_html__('Tablet Portrait - Line Color', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'description' => esc_html__('Leave empty to inherit', 'brighthub'),
                    ]
                );

                $element->add_control(
                    'pxl_container_line_mobile_landscape_heading',
                    [
                        'label' => esc_html__('Mobile Landscape Styles (576px - 767px)', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $element->add_control(
                    'pxl_container_line_s_mobile_landscape',
                    [
                        'label' => esc_html__('Mobile Landscape - Line Left/Right Style', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'inherit' => esc_html__('Inherit', 'brighthub'),
                            'gr-df'    => esc_html__('Default', 'brighthub'),
                            'gr-df-bold'    => esc_html__('Bold Than Default', 'brighthub'),
                            'gr-btt'    => esc_html__('Gradient Bottom To Top', 'brighthub'),
                            'gr-ttb' => esc_html__('Gradient Top To Bottom', 'brighthub'),
                            'clr' => esc_html__('A Color', 'brighthub'),
                        ],
                        'default' => 'inherit'
                    ]
                );

                $element->add_control(
                    'pxl_container_line_srl_mobile_landscape',
                    [
                        'label' => esc_html__('Mobile Landscape - Line Top/Bottom Style', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'inherit' => esc_html__('Inherit', 'brighthub'),
                            'gr-df'    => esc_html__('Default', 'brighthub'),
                            'gr-df-bold'    => esc_html__('Bold Than Default', 'brighthub'),
                            'gr-rtl' => esc_html__('Gradient Right To Left', 'brighthub'),
                            'gr-ltr' => esc_html__('Gradient Left To Right', 'brighthub'),
                            'clr' => esc_html__('A Color', 'brighthub'),
                        ],
                        'default' => 'inherit'
                    ]
                );

                $element->add_control(
                    'pxl_container_line_c_mobile_landscape',
                    [
                        'label' => esc_html__('Mobile Landscape - Line Color', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'description' => esc_html__('Leave empty to inherit', 'brighthub'),
                    ]
                );

                $element->add_control(
                    'pxl_container_line_mobile_portrait_heading',
                    [
                        'label' => esc_html__('Mobile Portrait Styles (≤575px)', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );

                $element->add_control(
                    'pxl_container_line_s_mobile_portrait',
                    [
                        'label' => esc_html__('Mobile Portrait - Line Left/Right Style', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'inherit' => esc_html__('Inherit', 'brighthub'),
                            'gr-df'    => esc_html__('Default', 'brighthub'),
                            'gr-df-bold'    => esc_html__('Bold Than Default', 'brighthub'),
                            'gr-btt'    => esc_html__('Gradient Bottom To Top', 'brighthub'),
                            'gr-ttb' => esc_html__('Gradient Top To Bottom', 'brighthub'),
                            'clr' => esc_html__('A Color', 'brighthub'),
                        ],
                        'default' => 'inherit'
                    ]
                );

                $element->add_control(
                    'pxl_container_line_srl_mobile_portrait',
                    [
                        'label' => esc_html__('Mobile Portrait - Line Top/Bottom Style', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'inherit' => esc_html__('Inherit', 'brighthub'),
                            'gr-df'    => esc_html__('Default', 'brighthub'),
                            'gr-df-bold'    => esc_html__('Bold Than Default', 'brighthub'),
                            'gr-rtl' => esc_html__('Gradient Right To Left', 'brighthub'),
                            'gr-ltr' => esc_html__('Gradient Left To Right', 'brighthub'),
                            'clr' => esc_html__('A Color', 'brighthub'),
                        ],
                        'default' => 'inherit'
                    ]
                );

                $element->add_control(
                    'pxl_container_line_c_mobile_portrait',
                    [
                        'label' => esc_html__('Mobile Portrait - Line Color', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'description' => esc_html__('Leave empty to inherit', 'brighthub'),
                    ]
                );

                // Responsive visibility controls for each position
                $all_positions = [
                    'top' => 'Top',
                    'bottom' => 'Bottom', 
                    'right' => 'Right',
                    'left' => 'Left',
                    'horizontal' => 'Horizontal',
                    'vertical' => 'Vertical',    
                ];

                foreach ($all_positions as $position_key => $position_label) {
                    $element->add_control(
                        'pxl_container_line_responsive_heading_' . str_replace('-', '_', $position_key),
                        [
                            'label' => sprintf(esc_html__('Responsive Visibility - %s', 'brighthub'), $position_label),
                            'type' => \Elementor\Controls_Manager::HEADING,
                            'separator' => 'before',
                            'condition' => [
                                'pxl_container_line_o' => $position_key,
                            ],
                        ]
                    );

                    $element->add_control(
                        'pxl_container_line_hide_laptop_' . str_replace('-', '_', $position_key),
                        [
                            'label' => esc_html__('Hide on Laptop (>1366px)', 'brighthub'),
                            'type' => \Elementor\Controls_Manager::SWITCHER,
                            'label_on' => esc_html__('Hide', 'brighthub'),
                            'label_off' => esc_html__('Show', 'brighthub'),
                            'return_value' => 'yes',
                            'default' => '',
                            'condition' => [
                                'pxl_container_line_o' => $position_key,
                            ],
                        ]
                    );

                    $element->add_control(
                        'pxl_container_line_hide_tablet_landscape_' . str_replace('-', '_', $position_key),
                        [
                            'label' => esc_html__('Hide on Tablet Landscape (1025px - 1366px)', 'brighthub'),
                            'type' => \Elementor\Controls_Manager::SWITCHER,
                            'label_on' => esc_html__('Hide', 'brighthub'),
                            'label_off' => esc_html__('Show', 'brighthub'),
                            'return_value' => 'yes',
                            'default' => '',
                            'condition' => [
                                'pxl_container_line_o' => $position_key,
                            ],
                        ]
                    );

                    $element->add_control(
                        'pxl_container_line_hide_tablet_portrait_' . str_replace('-', '_', $position_key),
                        [
                            'label' => esc_html__('Hide on Tablet Portrait (768px - 1024px)', 'brighthub'),
                            'type' => \Elementor\Controls_Manager::SWITCHER,
                            'label_on' => esc_html__('Hide', 'brighthub'),
                            'label_off' => esc_html__('Show', 'brighthub'),
                            'return_value' => 'yes',
                            'default' => '',
                            'condition' => [
                                'pxl_container_line_o' => $position_key,
                            ],
                        ]
                    );

                    $element->add_control(
                        'pxl_container_line_hide_mobile_landscape_' . str_replace('-', '_', $position_key),
                        [
                            'label' => esc_html__('Hide on Mobile Landscape (576px - 767px)', 'brighthub'),
                            'type' => \Elementor\Controls_Manager::SWITCHER,
                            'label_on' => esc_html__('Hide', 'brighthub'),
                            'label_off' => esc_html__('Show', 'brighthub'),
                            'return_value' => 'yes',
                            'default' => '',
                            'condition' => [
                                'pxl_container_line_o' => $position_key,
                            ],
                        ]
                    );

                    $element->add_control(
                        'pxl_container_line_hide_mobile_portrait_' . str_replace('-', '_', $position_key),
                        [
                            'label' => esc_html__('Hide on Mobile Portrait (≤575px)', 'brighthub'),
                            'type' => \Elementor\Controls_Manager::SWITCHER,
                            'label_on' => esc_html__('Hide', 'brighthub'),
                            'label_off' => esc_html__('Show', 'brighthub'),
                            'return_value' => 'yes',
                            'default' => '',
                            'condition' => [
                                'pxl_container_line_o' => $position_key,
                            ],
                        ]
                    );
                }

                $element->add_control(
                    'pxl_container_line_z',
                    [
                        'label' => esc_html__('Container Line z-index', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'selectors' => [
                            '{{WRAPPER}} .pxl-line' => 'z-index: {{VALUE}};',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_container_line_w',
                    [
                        'label' => esc_html__('Line Width/Height', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%' ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 3000,
                            ],
                            '%' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .pxl-line__top' => 'height: {{SIZE}}{{UNIT}};',
                            '{{WRAPPER}} .pxl-line__bottom' => 'height: {{SIZE}}{{UNIT}};',
                            '{{WRAPPER}} .pxl-line__right' => 'width: {{SIZE}}{{UNIT}};',
                            '{{WRAPPER}} .pxl-line__left' => 'width: {{SIZE}}{{UNIT}};',
                            '{{WRAPPER}} .pxl-line__vertical' => 'width: {{SIZE}}{{UNIT}};',
                            '{{WRAPPER}} .pxl-line__horizontal' => 'height: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );

                $element->end_controls_section();
            }
        }, 10, 2);

        if (!function_exists('pxl_get_line_responsive_classes')) {
            function pxl_get_line_responsive_classes($settings, $position) {
                $responsive_classes = [];
                $position_key = str_replace('-', '_', $position);
                
                if (!empty($settings['pxl_container_line_hide_laptop_' . $position_key])) {
                    $responsive_classes[] = 'pxl-hide-laptop';
                }
                if (!empty($settings['pxl_container_line_hide_tablet_landscape_' . $position_key])) {
                    $responsive_classes[] = 'pxl-hide-tablet-landscape';
                }
                if (!empty($settings['pxl_container_line_hide_tablet_portrait_' . $position_key])) {
                    $responsive_classes[] = 'pxl-hide-tablet-portrait';
                }
                if (!empty($settings['pxl_container_line_hide_mobile_landscape_' . $position_key])) {
                    $responsive_classes[] = 'pxl-hide-mobile-landscape';
                }
                if (!empty($settings['pxl_container_line_hide_mobile_portrait_' . $position_key])) {
                    $responsive_classes[] = 'pxl-hide-mobile-portrait';
                }
                
                return !empty($responsive_classes) ? ' ' . implode(' ', $responsive_classes) : '';
            }
        }

        if (!function_exists('pxl_get_responsive_line_styles')) {
            function pxl_get_responsive_line_styles($settings, $is_vertical = false) {
                $laptop_style = !empty($settings['pxl_container_line_s_laptop']) ? $settings['pxl_container_line_s_laptop'] : 'gr-df';
                $laptop_style_lr = !empty($settings['pxl_container_line_srl_laptop']) ? $settings['pxl_container_line_srl_laptop'] : 'gr-df';
                $laptop_color = !empty($settings['pxl_container_line_c_laptop']) ? $settings['pxl_container_line_c_laptop'] : '';
                
                $tablet_landscape_style = (!empty($settings['pxl_container_line_s_tablet_landscape']) && $settings['pxl_container_line_s_tablet_landscape'] !== 'inherit') 
                            ? $settings['pxl_container_line_s_tablet_landscape'] : $laptop_style;
                $tablet_landscape_style_lr = (!empty($settings['pxl_container_line_srl_tablet_landscape']) && $settings['pxl_container_line_srl_tablet_landscape'] !== 'inherit') 
                                ? $settings['pxl_container_line_srl_tablet_landscape'] : $laptop_style_lr;
                $tablet_landscape_color = !empty($settings['pxl_container_line_c_tablet_landscape']) ? $settings['pxl_container_line_c_tablet_landscape'] : $laptop_color;
                
                $tablet_portrait_style = (!empty($settings['pxl_container_line_s_tablet_portrait']) && $settings['pxl_container_line_s_tablet_portrait'] !== 'inherit') 
                            ? $settings['pxl_container_line_s_tablet_portrait'] : $tablet_landscape_style;
                $tablet_portrait_style_lr = (!empty($settings['pxl_container_line_srl_tablet_portrait']) && $settings['pxl_container_line_srl_tablet_portrait'] !== 'inherit') 
                                ? $settings['pxl_container_line_srl_tablet_portrait'] : $tablet_landscape_style_lr;
                $tablet_portrait_color = !empty($settings['pxl_container_line_c_tablet_portrait']) ? $settings['pxl_container_line_c_tablet_portrait'] : $tablet_landscape_color;
                
                $mobile_landscape_style = (!empty($settings['pxl_container_line_s_mobile_landscape']) && $settings['pxl_container_line_s_mobile_landscape'] !== 'inherit') 
                            ? $settings['pxl_container_line_s_mobile_landscape'] : $tablet_portrait_style;
                $mobile_landscape_style_lr = (!empty($settings['pxl_container_line_srl_mobile_landscape']) && $settings['pxl_container_line_srl_mobile_landscape'] !== 'inherit') 
                                ? $settings['pxl_container_line_srl_mobile_landscape'] : $tablet_portrait_style_lr;
                $mobile_landscape_color = !empty($settings['pxl_container_line_c_mobile_landscape']) ? $settings['pxl_container_line_c_mobile_landscape'] : $tablet_portrait_color;
                
                $mobile_portrait_style = (!empty($settings['pxl_container_line_s_mobile_portrait']) && $settings['pxl_container_line_s_mobile_portrait'] !== 'inherit') 
                            ? $settings['pxl_container_line_s_mobile_portrait'] : $mobile_landscape_style;
                $mobile_portrait_style_lr = (!empty($settings['pxl_container_line_srl_mobile_portrait']) && $settings['pxl_container_line_srl_mobile_portrait'] !== 'inherit') 
                                ? $settings['pxl_container_line_srl_mobile_portrait'] : $mobile_landscape_style_lr;
                $mobile_portrait_color = !empty($settings['pxl_container_line_c_mobile_portrait']) ? $settings['pxl_container_line_c_mobile_portrait'] : $mobile_landscape_color;
                
                return [
                    'laptop' => [
                        'style' => $is_vertical ? $laptop_style : $laptop_style_lr,
                        'color' => $laptop_color
                    ],
                    'tablet_landscape' => [
                        'style' => $is_vertical ? $tablet_landscape_style : $tablet_landscape_style_lr,
                        'color' => $tablet_landscape_color
                    ],
                    'tablet_portrait' => [
                        'style' => $is_vertical ? $tablet_portrait_style : $tablet_portrait_style_lr,
                        'color' => $tablet_portrait_color
                    ],
                    'mobile_landscape' => [
                        'style' => $is_vertical ? $mobile_landscape_style : $mobile_landscape_style_lr,
                        'color' => $mobile_landscape_color
                    ],
                    'mobile_portrait' => [
                        'style' => $is_vertical ? $mobile_portrait_style : $mobile_portrait_style_lr,
                        'color' => $mobile_portrait_color
                    ]
                ];
            }
        }

        if (!function_exists('hex2rgba')) {
            function hex2rgba($color, $opacity = 1) {
                if (empty($color)) return 'transparent';
                
                $hex = str_replace('#', '', $color);
                
                if (strlen($hex) == 3) {
                    $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
                }
                
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                
                return "rgba($r, $g, $b, $opacity)";
            }
        }

        if (!function_exists('pxl_generate_line_css')) {
            function pxl_generate_line_css($style, $color, $is_vertical = false) {
                if (empty($color)) return '';
                
                if ($is_vertical) {
                    switch ($style) {
                        case 'gr-btt': 
                            return 'background: linear-gradient(180deg, ' 
                                . hex2rgba($color, 0) . ' 0%, '
                                . hex2rgba($color, 0.88) . ' 50%, '
                                . $color . ' 100%);';
                        case 'gr-ttb': 
                            return 'background: linear-gradient(180deg, ' 
                                . $color . ' 0%, ' 
                                . hex2rgba($color, 0.88) . ' 50%, '
                                . hex2rgba($color, 0) . ' 100%);';
                        case 'gr-df': 
                            return 'background: linear-gradient(180deg, ' 
                                . hex2rgba($color, 0) . ' 0%, ' 
                                . $color . ' 25%, ' 
                                . $color . ' 50%, ' 
                                . $color . ' 75%, ' 
                                . hex2rgba($color, 0) . ' 100%);';
                        case 'gr-df-bold': 
                            return 'background: linear-gradient(180deg, ' 
                                . hex2rgba($color, 0) . ' 0%, ' 
                                . hex2rgba($color, 0.8) . ' 25%, ' 
                                . $color . ' 50%, ' 
                                . hex2rgba($color, 0.8) . ' 75%, ' 
                                . hex2rgba($color, 0) . ' 100%);';
                        case 'clr': 
                            return 'background: ' . $color . ';';
                        default:
                            return 'background: ' . $color . ';';
                    }
                } else {
                    switch ($style) {
                        case 'gr-rtl': 
                            return 'background: linear-gradient(90deg, ' 
                                . $color . ' 0%, '
                                . hex2rgba($color, 0.88) . ' 50%, '
                                . hex2rgba($color, 0) . ' 100%);';
                        case 'gr-ltr': 
                            return 'background: linear-gradient(90deg, ' 
                                . hex2rgba($color, 0) . ' 0%, ' 
                                . hex2rgba($color, 0.88) . ' 50%, '
                                . $color . ' 100%);';
                        case 'gr-df': 
                            return 'background: linear-gradient(90deg, ' 
                                . hex2rgba($color, 0) . ' 0%, ' 
                                . hex2rgba($color, 0.13) . ' 25%, ' 
                                . hex2rgba($color, 0.16) . ' 50%, ' 
                                . hex2rgba($color, 0.13) . ' 75%, ' 
                                . hex2rgba($color, 0) . ' 100%);';
                        case 'gr-df-bold': 
                            return 'background: linear-gradient(90deg, ' 
                                . hex2rgba($color, 0) . ' 0%, ' 
                                . hex2rgba($color, 0.8) . ' 25%, ' 
                                . hex2rgba($color, 1) . ' 50%, ' 
                                . hex2rgba($color, 0.8) . ' 75%, ' 
                                . hex2rgba($color, 0) . ' 100%);';
                        case 'clr': 
                            return 'background: ' . $color . ';';
                        default:
                            return 'background: ' . $color . ';';
                    }
                }
            }
        }

        add_filter('pxl_element_container/before-render', function ($html, $settings) {
            if (!empty($settings['pxl_container_line_o']) && is_array($settings['pxl_container_line_o'])) {

                foreach ($settings['pxl_container_line_o'] as $item) {
                    $responsive_class_string = pxl_get_line_responsive_classes($settings, $item);
                    
                    $is_vertical = ($item === 'left' || $item === 'right' || strpos($item, 'vertical') !== false);
                    
                    $responsive_styles = pxl_get_responsive_line_styles($settings, $is_vertical);
                    
                    $laptop_css = pxl_generate_line_css(
                        $responsive_styles['laptop']['style'], 
                        $responsive_styles['laptop']['color'], 
                        $is_vertical
                    );
                    $tablet_landscape_css = pxl_generate_line_css(
                        $responsive_styles['tablet_landscape']['style'], 
                        $responsive_styles['tablet_landscape']['color'], 
                        $is_vertical
                    );
                    $tablet_portrait_css = pxl_generate_line_css(
                        $responsive_styles['tablet_portrait']['style'], 
                        $responsive_styles['tablet_portrait']['color'], 
                        $is_vertical
                    );
                    $mobile_landscape_css = pxl_generate_line_css(
                        $responsive_styles['mobile_landscape']['style'], 
                        $responsive_styles['mobile_landscape']['color'], 
                        $is_vertical
                    );
                    $mobile_portrait_css = pxl_generate_line_css(
                        $responsive_styles['mobile_portrait']['style'], 
                        $responsive_styles['mobile_portrait']['color'], 
                        $is_vertical
                    );

                    $html .= '<span class="pxl-line pxl-line__' . esc_attr($item) . $responsive_class_string . '"
                    data-laptop-style="' . esc_attr($laptop_css) . '"
                    data-tablet-landscape-style="' . esc_attr($tablet_landscape_css) . '"
                    data-tablet-portrait-style="' . esc_attr($tablet_portrait_css) . '"
                    data-mobile-landscape-style="' . esc_attr($mobile_landscape_css) . '"
                    data-mobile-portrait-style="' . esc_attr($mobile_portrait_css) . '"
                    style="' . $laptop_css . '"></span>';
                }
            }
            return $html;
        }, 10, 2);

        add_action('wp_footer', function() {
            ?>
            <script>
            (function() {
                function applyResponsiveLineStyles() {
                    const lines = document.querySelectorAll('.pxl-line[data-laptop-style]');
                    const windowWidth = window.innerWidth;
                    
                    lines.forEach(line => {
                        let targetStyle = '';
                        
                        if (windowWidth <= 575) {
                            targetStyle = line.getAttribute('data-mobile-portrait-style') || 
                                        line.getAttribute('data-mobile-landscape-style') ||
                                        line.getAttribute('data-tablet-portrait-style') ||
                                        line.getAttribute('data-tablet-landscape-style') ||
                                        line.getAttribute('data-laptop-style');
                        } else if (windowWidth >= 576 && windowWidth <= 767) {
                            targetStyle = line.getAttribute('data-mobile-landscape-style') || 
                                        line.getAttribute('data-tablet-portrait-style') ||
                                        line.getAttribute('data-tablet-landscape-style') ||
                                        line.getAttribute('data-laptop-style');
                        } else if (windowWidth >= 768 && windowWidth <= 1024) {
                            targetStyle = line.getAttribute('data-tablet-portrait-style') || 
                                        line.getAttribute('data-tablet-landscape-style') ||
                                        line.getAttribute('data-laptop-style');
                        } else if (windowWidth >= 1025 && windowWidth <= 1366) {
                            targetStyle = line.getAttribute('data-tablet-landscape-style') || 
                                        line.getAttribute('data-laptop-style');
                        } else {
                            targetStyle = line.getAttribute('data-laptop-style');
                        }
                        
                        if (targetStyle) {
                            const currentStyles = line.style.cssText
                                .replace(/background[^;]*;?/g, '')
                                .replace(/;\s*;/g, ';')
                                .replace(/^;|;$/g, '')
                                .trim();
                            
                            line.style.cssText = targetStyle + (currentStyles ? '; ' + currentStyles : '');
                        }
                    });
                }
                
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', applyResponsiveLineStyles);
                } else {
                    applyResponsiveLineStyles();
                }
                
                let resizeTimeout;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(applyResponsiveLineStyles, 100);
                });
                
                if (typeof elementorFrontend !== 'undefined') {
                    elementorFrontend.hooks.addAction('frontend/element_ready/global', function() {
                        setTimeout(applyResponsiveLineStyles, 100);
                    });
                    
                    elementorFrontend.hooks.addAction('frontend/element_ready/container', function() {
                        setTimeout(applyResponsiveLineStyles, 50);
                    });
                }
                
                window.addEventListener('load', function() {
                    setTimeout(applyResponsiveLineStyles, 200);
                });
                
                window.debugResponsiveLines = function() {
                    console.log('Current window width:', window.innerWidth);
                    const lines = document.querySelectorAll('.pxl-line[data-laptop-style]');
                    lines.forEach((line, index) => {
                        console.log(`Line ${index + 1} - Inheritance Chain:`, {
                            classes: line.className,
                            inheritance: {
                                '5_laptop': line.getAttribute('data-laptop-style'),
                                '4_tablet_landscape': line.getAttribute('data-tablet-landscape-style'),
                                '3_tablet_portrait': line.getAttribute('data-tablet-portrait-style'),
                                '2_mobile_landscape': line.getAttribute('data-mobile-landscape-style'),
                                '1_mobile_portrait': line.getAttribute('data-mobile-portrait-style')
                            },
                            currentStyle: line.style.cssText
                        });
                    });
                };
                
            })();
            </script>
            <?php
        });
    
        // Container Overlay
        add_action('elementor/element/after_section_end', function($element, $section_id) {
            if ($element->get_name() === 'container' && 'section_layout_additional_options' === $section_id) {
                $element->start_controls_section(
                    'pxl_container_overlay',
                    [
                        'label' => __('Background Container Overlay', 'brighthub'),
                        'tab' => \Elementor\Controls_Manager::TAB_LAYOUT,
                    ]
                );
    
                $element->add_control(
                    'pxl_container_overlay_sides',
                    [
                        'label' => esc_html__('Container Overlay Sides', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT2,
                        'multiple' => true,
                        'options' => [
                            'top'    => esc_html__('Top', 'brighthub'),
                            'left'   => esc_html__('Left', 'brighthub'),
                            'right'  => esc_html__('Right', 'brighthub'),
                            'bottom' => esc_html__('Bottom', 'brighthub'),
                            'full' => esc_html__('Full Dark', 'brighthub'),
                            'full-light' => esc_html__('Full Light', 'brighthub'),
                        ],
                        'default' => [],
                    ]
                );

                $element->add_control(
                    'pxl_container_overlay_type',
                    [
                        'label' => esc_html__('Container Overlay Type', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'multiple' => true,
                        'options' => [
                            'default'    => esc_html__('Default', 'brighthub'),
                            '3d'   => esc_html__('3D Grid', 'brighthub'),
                            '3d-line'   => esc_html__('3D Grid Has Line', 'brighthub'),
                            'upload'   => esc_html__('Upload', 'brighthub'),
                        ],
                        'default' => 'default',
                    ]
                );
                $element->add_control(
                    'pxl_container_overlay_zindex',
                    [
                        'label' => esc_html__('Container Overlay z-index', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item' => 'z-index: {{VALUE}}',
                        ],
                    ]
                );
                
                $element->add_responsive_control(
                    'pxl_container_overlay_lw',
                    [
                        'label' => esc_html__('Container Overlay Left Width', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%' ],
                        'control_type' => 'responsive',
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 300,
                            ],
                            '%' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item-left' => 'width: {{SIZE}}{{UNIT}};',
                        ],
                        'condition' => [
                            'pxl_container_overlay_sides' => 'left',
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_overlay_limg',
                    [
                        'label' => esc_html__('Container Overlay Left Image', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::MEDIA,
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item-left' => 'background: url({{pxl_container_overlay_limg.url}}) no-repeat center;',
                        ],
                        'condition' => [
                            'pxl_container_overlay_sides' => 'left',
                            'pxl_container_overlay_type' => 'upload',
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_overlay_bgpl',
                    [
                        'label' => esc_html__('Container Overlay Left Position', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'top left'    => esc_html__('Top Left', 'brighthub'),
                            'top right'    => esc_html__('Top Right', 'brighthub'),
                            'bottom right'   => esc_html__('Bottom Right', 'brighthub'),
                            'bottom left'   => esc_html__('Bottom Left', 'brighthub'),
                            'center'   => esc_html__('Center', 'brighthub'),
                            'center top'   => esc_html__('Center Top', 'brighthub'),
                            'center bottom'   => esc_html__('Center Bottom', 'brighthub'),
                            'center left'   => esc_html__('Center Left', 'brighthub'),
                            'center right'   => esc_html__('Center Right', 'brighthub'),
                        ],
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item-left' => 'background-position: {{VALUE}}',
                        ],
                        'condition' => [
                            'pxl_container_overlay_sides' => 'left',
                        ],
                    ]
                );
                $element->add_responsive_control(
                    'pxl_container_overlay_rw',
                    [
                        'label' => esc_html__('Container Overlay Right Width', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%' ],
                        'control_type' => 'responsive',
                        'separator' => 'before',
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 300,
                            ],
                            '%' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item-right' => 'width: {{SIZE}}{{UNIT}};',
                        ],
                        'condition' => [
                            'pxl_container_overlay_sides' => 'right',
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_overlay_rimg',
                    [
                        'label' => esc_html__('Container Overlay Right Image', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::MEDIA,
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item-right' => 'background: url({{pxl_container_overlay_rimg.url}}) no-repeat center;',
                        ],
                        'condition' => [
                            'pxl_container_overlay_sides' => 'right',
                            'pxl_container_overlay_type' => 'upload',
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_overlay_bgpr',
                    [
                        'label' => esc_html__('Container Overlay Right Position', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'top left'    => esc_html__('Top Left', 'brighthub'),
                            'top right'    => esc_html__('Top Right', 'brighthub'),
                            'bottom right'   => esc_html__('Bottom Right', 'brighthub'),
                            'bottom left'   => esc_html__('Bottom Left', 'brighthub'),
                            'center'   => esc_html__('Center', 'brighthub'),
                            'center top'   => esc_html__('Center Top', 'brighthub'),
                            'center bottom'   => esc_html__('Center Bottom', 'brighthub'),
                            'center left'   => esc_html__('Center Left', 'brighthub'),
                            'center right'   => esc_html__('Center Right', 'brighthub'),
                        ],
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item-right' => 'background-position: {{VALUE}}',
                        ],
                        'condition' => [
                            'pxl_container_overlay_sides' => 'right',
                        ],
                    ]
                );
                
                $element->add_responsive_control(
                    'pxl_container_overlay_tw',
                    [
                        'label' => esc_html__('Container Overlay Top Height', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'control_type' => 'responsive',
                        'size_units' => [ 'px', '%' ],
                        'separator' => 'before',
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 300,
                            ],
                            '%' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item-top' => 'height: {{SIZE}}{{UNIT}};',
                        ],
                        'condition' => [
                            'pxl_container_overlay_sides' => 'top',
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_overlay_timg',
                    [
                        'label' => esc_html__('Container Overlay Top Image', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::MEDIA,
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item-top' => 'background: url({{pxl_container_overlay_timg.url}}) no-repeat center;',
                        ],
                        'condition' => [
                            'pxl_container_overlay_sides' => 'top',
                            'pxl_container_overlay_type' => 'upload',
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_overlay_bgpt',
                    [
                        'label' => esc_html__('Container Overlay Top Position', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'top left'    => esc_html__('Top Left', 'brighthub'),
                            'top right'    => esc_html__('Top Right', 'brighthub'),
                            'bottom right'   => esc_html__('Bottom Right', 'brighthub'),
                            'bottom left'   => esc_html__('Bottom Left', 'brighthub'),
                            'center'   => esc_html__('Center', 'brighthub'),
                            'center top'   => esc_html__('Center Top', 'brighthub'),
                            'center bottom'   => esc_html__('Center Bottom', 'brighthub'),
                            'center left'   => esc_html__('Center Left', 'brighthub'),
                            'center right'   => esc_html__('Center Right', 'brighthub'),
                        ],
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item-top' => 'background-position: {{VALUE}}',
                        ],
                       'condition' => [
                            'pxl_container_overlay_sides' => 'top',
                        ],
                    ]
                );
                
                $element->add_responsive_control(
                    'pxl_container_overlay_bw',
                    [
                        'label' => esc_html__('Container Overlay Bottom Height', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%' ],
                        'control_type' => 'responsive',
                        'separator' => 'before',
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 300,
                            ],
                            '%' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item-bottom' => 'height: {{SIZE}}{{UNIT}};',
                        ],
                        'condition' => [
                            'pxl_container_overlay_sides' => 'bottom',
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_overlay_bimg',
                    [
                        'label' => esc_html__('Container Overlay Bottom Image', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::MEDIA,
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item-bottom' => 'background: url({{pxl_container_overlay_bimg.url}}) no-repeat center;',
                        ],
                        'condition' => [
                            'pxl_container_overlay_sides' => 'bottom',
                            'pxl_container_overlay_type' => 'upload',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_container_overlay_bgbp',
                    [
                        'label' => esc_html__('Container Overlay Bottom Position', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'top left'    => esc_html__('Top Left', 'brighthub'),
                            'top right'    => esc_html__('Top Right', 'brighthub'),
                            'bottom right'   => esc_html__('Bottom Right', 'brighthub'),
                            'bottom left'   => esc_html__('Bottom Left', 'brighthub'),
                            'center'   => esc_html__('Center', 'brighthub'),
                            'center top'   => esc_html__('Center Top', 'brighthub'),
                            'center bottom'   => esc_html__('Center Bottom', 'brighthub'),
                            'center left'   => esc_html__('Center Left', 'brighthub'),
                            'center right'   => esc_html__('Center Right', 'brighthub'),
                        ],
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item-bottom' => 'background-position: {{VALUE}}',
                        ],
                        'condition' => [
                            'pxl_container_overlay_sides' => 'bottom',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_container_overlay_bgs',
                    [
                        'label' => esc_html__('Container Overlay Size', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'auto'    => esc_html__('Auto', 'brighthub'),
                            'cover'    => esc_html__('Cover', 'brighthub'),
                            'contain'   => esc_html__('Contain', 'brighthub'),
                            'percent'   => esc_html__('Percent', 'brighthub'),
                            'pixel'   => esc_html__('Pixel', 'brighthub'),
                        ],
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item' => 'background-size: {{VALUE}}',
                        ],
                        'condition' => [
                            'pxl_container_overlay_type' => 'upload',
                        ],
                    ]
                );
                
                $element->add_control(
                    'pxl_container_overlay_bgsvalue',
                    [
                        'label' => esc_html__('Container Overlay Background Size', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%' ],
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item' => 'background-size: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}};',
                        ],
                        'condition' => [
                            'pxl_container_overlay_bgs' => [
                                'percent',
                                'pixel',
                            ],
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_overlay_bgsize',
                    [
                        'label' => esc_html__('Container Overlay Size', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::DIMENSIONS,
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item' => 'background-size: {{VALUE}}',
                        ],
                        'condition' => [
                            'pxl_container_overlay_bgs' => ['percent', 'pixel'],
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_overlay_color',
                    [
                        'label' => esc_html__('Container Overlay Color', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} > .pxl-container-overlay__item-left' => 'background: linear-gradient(90deg, {{VALUE}} 0%, rgba(0, 0, 0, 0.00) 100%);',
                            '{{WRAPPER}} > .pxl-container-overlay__item-right' => 'background: linear-gradient(270deg, {{VALUE}} 0%, rgba(0, 0, 0, 0.00) 100%);',
                            '{{WRAPPER}} > .pxl-container-overlay__item-top' => 'background: linear-gradient(180deg, {{VALUE}} 0%, rgba(0, 0, 0, 0.00) 100%);',
                            '{{WRAPPER}} > .pxl-container-overlay__item-bottom' => 'background: linear-gradient(0deg, {{VALUE}} 0%, rgba(0, 0, 0, 0.00) 100%);',
                        ],
                        'condition' => [
                            'pxl_container_overlay_type' => 'default',
                        ],
                    ]
                );
    
                $element->end_controls_section();
            }
        }, 10, 2);
    
        add_filter('pxl_element_container/before-render', function($html, $settings) {
            if (!empty($settings['pxl_container_overlay_sides']) && is_array($settings['pxl_container_overlay_sides'])) {
                $sides = $settings['pxl_container_overlay_sides'];
                $type = $settings['pxl_container_overlay_type'];
                foreach ($sides as $side) {
                    $html .= '<span class="pxl-container-overlay__item pxl-container-overlay__item-' . esc_attr($side) . ' pxl-container-overlay__item-' . esc_attr($type) . '"></span>';
                }
            }
    
            return $html;
        }, 10, 2);

        // Dot
        add_action('elementor/element/after_section_end', function($element, $section_id) {
            if ($element->get_name() === 'container' && 'section_layout_additional_options' === $section_id) {
                $element->start_controls_section(
                    'pxl_container_dot',
                    [
                        'label' => __('Dot Container', 'brighthub'),
                        'tab' => \Elementor\Controls_Manager::TAB_LAYOUT,
                    ]
                );

                $element->add_control(
                    'pxl_dot_container_pos',
                    [
                        'label' => esc_html__('Dot Container Position', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT2,
                        'multiple' => true,
                        'options' => [
                            'top'    => esc_html__('Top Left', 'brighthub'),
                            'left'   => esc_html__('Top Right', 'brighthub'),
                            'right'  => esc_html__('Bottom Right', 'brighthub'),
                            'bottom' => esc_html__('Bottom Left', 'brighthub'),
                        ],
                        'default' => [],
                    ]
                );
                $element->add_control(
                    'pxl_dot_container_type',
                    [
                        'label' => esc_html__('Dot Container Type', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'default' => esc_html__('Default', 'brighthub'),
                            'cut-corner' => esc_html__('Cut Corner', 'brighthub'),
                        ],
                        'default' => 'default',
                    ]
                );

                $element->add_control(
                    'pxl_dot_container_w',
                    [
                        'label' => esc_html__('Dot Container Width', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px'],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 300,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .pxl-dot' => 'width: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_dot_container_h',
                    [
                        'label' => esc_html__('Dot Container Height', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px'],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 300,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .pxl-dot' => 'height: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_dot_container_r',
                    [
                        'label' => esc_html__('Dot Border Radius', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%' ],
                        'selectors' => [
                            '{{WRAPPER}} .pxl-dot' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );
    
                $element->add_control(
                    'pxl_dot_container_clr',
                    [
                        'label' => esc_html__('Dot Color', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .pxl-dot.pxl-dot__default' => 'background-color: {{VALUE}};',
                            '{{WRAPPER}} .pxl-dot.pxl-dot__cut-corner' => 'border-color: {{VALUE}};',
                        ],
                    ]
                );
    
                $element->end_controls_section();
            }
        }, 10, 2);
    
        add_filter('pxl_element_container/before-render', function($html, $settings) {
            if (!empty($settings['pxl_dot_container_pos']) && is_array($settings['pxl_dot_container_pos'])) {
                $items = $settings['pxl_dot_container_pos'];
    
                foreach ($items as $item) {
                    $html .= '<span class="pxl-dot pxl-dot__' . esc_attr($item) . ' pxl-dot__' . esc_attr($settings['pxl_dot_container_type']) . '"></span>';
                }
            }
    
            return $html;
        }, 10, 2);

        // Container Link
        add_action('elementor/element/after_section_end', function($element, $section_id) {
            if ($element->get_name() === 'container' && 'section_layout_additional_options' === $section_id) {
                $element->start_controls_section(
                    'pxl_container_url',
                    [
                        'label' => __('Container Link', 'brighthub'),
                        'tab' => \Elementor\Controls_Manager::TAB_LAYOUT,
                    ]
                );
    
                $element->add_control(
                    'pxl_container_link',
                    [
                        'label' => esc_html__('Link', 'brighthub'),
                        'type' => \Elementor\Controls_Manager::URL,
                    ]
                );
    
                $element->end_controls_section();
            }
        }, 10, 2);

        add_filter('pxl_element_container/before-render', function($html, $settings) {
            if (!empty($settings['pxl_container_link']['url']) && is_array($settings['pxl_container_link'])) {
                
                $link_attributes = [];
                
                if (!empty($settings['pxl_container_link']['url'])) {
                    $link_attributes['href'] = esc_url($settings['pxl_container_link']['url']);
                }
                
                if (!empty($settings['pxl_container_link']['is_external'])) {
                    $link_attributes['target'] = '_blank';
                }
                
                if (!empty($settings['pxl_container_link']['nofollow'])) {
                    $link_attributes['rel'] = 'nofollow';
                }
                
                $attributes_string = '';
                foreach ($link_attributes as $key => $value) {
                    $attributes_string .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
                }
                
                $html .= '<a' . $attributes_string . ' class="pxl-container pxl-container__link"></a>';
            }
        
            return $html;
        }, 10, 2);

        // Container star
        add_action( 'elementor/element/after_section_end', function ( $element, $section_id ) {

            if ( $element->get_name() === 'container' && $section_id === 'section_layout_additional_options' ) {

                $element->start_controls_section(
                    'pxl_container_star',
                    [
                        'label' => __( 'Star & Light', 'brighthub' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_LAYOUT,
                    ]
                );
                $element->add_control(
                    'pxl_container_star_color_option',
                    [
                        'label' => __( 'Star Option', 'brighthub' ),
                        'type'  => \Elementor\Controls_Manager::SWITCHER,
                        'default' => 'no',
                        'options' => [
                            'yes' => __( 'Yes', 'brighthub' ),
                            'no' => __( 'No', 'brighthub' ),
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_star_color',
                    [
                        'label' => __( 'Star Color', 'brighthub' ),
                        'type'  => \Elementor\Controls_Manager::COLOR,
                        'condition' => [
                            'pxl_container_star_color_option' => 'yes',
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_star_number',
                    [
                        'label' => __( 'Star Number', 'brighthub' ),
                        'type'  => \Elementor\Controls_Manager::NUMBER,
                        'condition' => [
                            'pxl_container_star_color_option' => 'yes',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_container_star_width',
                    [
                        'label' => __( 'Box Width', 'brighthub' ),
                        'type'  => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%', 'vw' ],
                        'range' => [
                            'px' => [ 'min' => 0,  'max' => 2000 ],
                            '%'  => [ 'min' => 0,  'max' => 100 ],
                            'vw' => [ 'min' => 0,  'max' => 100 ],
                        ],
                        'condition' => [
                            'pxl_container_star_color_option' => 'yes',
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_star_height',
                    [
                        'label' => __( 'Box Height', 'brighthub' ),
                        'type'  => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%', 'vh' ],
                        'range' => [
                            'px' => [ 'min' => 0,  'max' => 5000 ],
                            '%'  => [ 'min' => 0,  'max' => 100 ],
                            'vh' => [ 'min' => 0,  'max' => 100 ],
                        ],
                        'condition' => [
                            'pxl_container_star_color_option' => 'yes',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_container_light_color_option',
                    [
                        'label' => __( 'Light Option', 'brighthub' ),
                        'type'  => \Elementor\Controls_Manager::SWITCHER,
                        'default' => 'no',
                        'separator' => 'before',
                        'options' => [
                            'yes' => __( 'Yes', 'brighthub' ),
                            'no' => __( 'No', 'brighthub' ),
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_container_light_color',
                    [
                        'label' => __( 'Light Color', 'brighthub' ),
                        'type'  => \Elementor\Controls_Manager::COLOR,
                        'condition' => [
                            'pxl_container_light_color_option' => 'yes',
                        ],
                    ]
                );
                $element->add_control(
                    'pxl_container_light_width',
                    [
                        'label' => __( 'Light Width', 'brighthub' ),
                        'type'  => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%', 'vw' ],
                        'range' => [
                            'px' => [ 'min' => 0,  'max' => 2000 ],
                            '%'  => [ 'min' => 0,  'max' => 100 ],
                            'vw' => [ 'min' => 0,  'max' => 100 ],
                        ],
                        'condition' => [
                            'pxl_container_light_color_option' => 'yes',
                        ],
                    ]
                );
                
                $element->add_control(
                    'pxl_container_light_height',
                    [
                        'label' => __( 'Light Height', 'brighthub' ),
                        'type'  => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%', 'vh' ],
                        'range' => [
                            'px' => [ 'min' => 0,  'max' => 5000 ],
                            '%'  => [ 'min' => 0,  'max' => 100 ],
                            'vh' => [ 'min' => 0,  'max' => 100 ],
                        ],
                        'condition' => [
                            'pxl_container_light_color_option' => 'yes',
                        ],
                    ]
                );
                
                $element->add_control(
                    'pxl_container_light_blur',
                    [
                        'label' => __( 'Blur Option', 'brighthub' ),
                        'type'  => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px' ],
                        'range' => [
                            'px'  => [ 'min' => 0,  'max' => 1000 ],
                        ],
                    ]
                );
                
                $element->end_controls_section();
            }
        }, 10, 2 );

        add_filter( 'pxl_element_container/before-render', function ( $html, $settings ) {
            $star_w      = $settings['pxl_container_star_width']['size']  ?? null;
            $star_w_unit = $settings['pxl_container_star_width']['unit']  ?? 'px';
            $star_h      = $settings['pxl_container_star_height']['size'] ?? null;
            $star_h_unit = $settings['pxl_container_star_height']['unit'] ?? 'px';
            $star_number = $settings['pxl_container_star_number']         ?? null;
        
            $star_style  = '';
            if ( is_numeric( $star_w ) ) $star_style .= "width:{$star_w}{$star_w_unit};";
            if ( is_numeric( $star_h ) ) $star_style .= "height:{$star_h}{$star_h_unit};";
        
            if (
                ! empty( $settings['pxl_container_star_color'] ) &&
                ( $settings['pxl_container_star_color_option'] ?? 'no' ) === 'yes'
            ) {
                $html .= sprintf(
                    '<canvas class="pxl-star" data-color="%s" data-star="%s" style="%s"%s></canvas>',
                    esc_attr( $settings['pxl_container_star_color'] ),
                    esc_attr( $settings['pxl_container_star_number'] ), 
                    esc_attr( $star_style ),
                    is_numeric( $star_number ) ? ' data-star="' . intval( $star_number ) . '"' : ''
                );
            }
        
            if (
                ! empty( $settings['pxl_container_light_color'] ) &&
                ( $settings['pxl_container_light_color_option'] ?? 'no' ) === 'yes'
            ) {
                $light_w      = $settings['pxl_container_light_width']['size']  ?? $star_w;
                $light_w_unit = $settings['pxl_container_light_width']['unit']  ?? $star_w_unit;
                $light_h      = $settings['pxl_container_light_height']['size'] ?? $star_h;
                $light_h_unit = $settings['pxl_container_light_height']['unit'] ?? $star_h_unit;
        
                $light_style  = '';
                if ( is_numeric( $light_w ) ) $light_style .= "width:{$light_w}{$light_w_unit};";
                if ( is_numeric( $light_h ) ) $light_style .= "height:{$light_h}{$light_h_unit};";
        
                $blur_raw     = $settings['pxl_container_light_blur']['size'] ?? 0;
                $blur         = is_numeric( $blur_raw ) ? intval( $blur_raw ) : 0;
        
                $light_style .= "background:{$settings['pxl_container_light_color']};";
                if ( $blur > 0 ) $light_style .= "filter:blur({$blur}px);";
        
                $html .= sprintf(
                    '<div class="pxl-light" style="%s"></div>',
                    esc_attr( $light_style )
                );
            }
        
            return $html;
        }, 10, 2 );
        

        // Custom css
        add_action( 'elementor/element/parse_css', function( $post_css, $element ) {
            if ( $post_css instanceof \Elementor\Core\DynamicTags\Dynamic_CSS ) {
                return;
            }
            
            $element_settings = $element->get_settings();
            
            if ( empty( $element_settings['pxl_custom_css'] ) ) {
                return;
            }
            
            $css = trim( $element_settings['pxl_custom_css'] );
            
            if ( empty( $css ) ) {
                return;
            }
            
            $css = str_replace( 'selector', $post_css->get_element_unique_selector( $element ), $css );
            
            $post_css->get_stylesheet()->add_raw_css( $css );
            
        }, 10, 2 );

        add_action( 'elementor/element/after_section_end', function( $element, $section_id ) {
            if (
                $section_id === 'section_layout'  ||
                $section_id === 'section_advanced' ||
                $section_id === '_section_style'
            ) {
                
                if ( $element->get_controls( 'pxl_custom_css_section' ) ) {
                    return;
                }
                
                $element->start_controls_section(
                    'pxl_custom_css_section',
                    [
                        'label' => __( 'BrightHub Custom CSS 🖊 and Effect Scroll', 'brighthub' ),
                        'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
                    ]
                );
                
                $element->add_control(
                    'pxl_custom_css',
                    [
                        'type' => \Elementor\Controls_Manager::CODE,
                        'language' => 'css',
                        'render_type' => 'template',
                        'raw' => true,
                        'show_label' => false,
                        'separator' => 'none',
                        'description' => __('Add your custom CSS here. Use "selector" to target this element.', 'brighthub'),
                    ]
                );

                $element->add_control(
                    'pxl_scroll_effect',
                    [
                        'label' => __( 'BrightHub Scroll Effect Type', 'brighthub' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'none' => __( 'None', 'brighthub' ),
                            'scroll-item-run' => __( 'Scroll Item Run', 'brighthub' ),
                            'scroll-item-zoom' => __( 'Scroll Item Zoom', 'brighthub' ),
                        ],
                        'default' => 'none',
                    ]
                );

                $element->add_control(
                    'pxl_scroll_top_zoom',
                    [
                        'label' => __( 'Scroll Effect Item Trigger Top (px)', 'brighthub' ),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'description' => __( 'Trigger top when zoom effect starts (px)', 'brighthub' ),
                        'size_units' => [ 'px' ],
                        'range' => [
                            'px' => [ 'min' => -2000,  'max' => 2000 ],
                        ],
                        'default' => [
                            'size' => -504,
                            'unit' => 'px',
                        ],
                        'condition' => [
                            'pxl_scroll_effect' => 'scroll-item-zoom',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_scroll_zoom_start',
                    [
                        'label' => __( 'Scroll Effect Item Trigger Top Start %', 'brighthub' ),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'description' => __( 'Trigger top when zoom effect starts from % value to 100%', 'brighthub' ),
                        'size_units' => [ '%' ],
                        'default' => [
                            'size' => 15,
                            'unit' => '%',
                        ],
                        'range' => [
                            '%' => [ 'min' => 0,  'max' => 100 ],
                        ],
                        'condition' => [
                            'pxl_scroll_effect' => 'scroll-item-zoom',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_scroll_zoom_start_screen',
                    [
                        'label' => __( 'Scroll Effect Item Trigger Top Start Screen %', 'brighthub' ),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'description' => __( 'Trigger top when scroll to start screen', 'brighthub' ),
                        'size_units' => [ '%' ],
                        'default' => [
                            'size' => 80,
                            'unit' => '%',
                        ],
                        'range' => [
                            '%' => [ 'min' => 0,  'max' => 100 ],
                        ],
                        'condition' => [
                            'pxl_scroll_effect' => 'scroll-item-zoom',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_scroll_zoom_end_screen',
                    [
                        'label' => __( 'Scroll Effect Item Trigger Top End Screen %', 'brighthub' ),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'description' => __( 'Trigger top when scroll end screen', 'brighthub' ),
                        'size_units' => [ '%' ],
                        'default' => [
                            'size' => 0,
                            'unit' => '%',
                        ],
                        'range' => [
                            '%' => [ 'min' => 0,  'max' => 100 ],
                        ],
                        'condition' => [
                            'pxl_scroll_effect' => 'scroll-item-zoom',
                        ],
                    ]
                );
                
                $element->add_control(
                    'pxl_scroll_zoom_max_screen',
                    [
                        'label' => __( 'Scroll Effect Max Screen (px)', 'brighthub' ),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'description' => __( 'When scroll to max screen, the effect will be disabled', 'brighthub' ),
                        'size_units' => [ 'px' ],
                        'default' => [
                            'size' => 1199,
                            'unit' => 'px',
                        ],
                        'range' => [
                            'px' => [ 'min' => 0,  'max' => 1920 ],
                        ],
                        'condition' => [
                            'pxl_scroll_effect' => 'scroll-item-zoom',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_scroll_effect_item',
                    [
                        'label' => __( 'Scroll Effect Item', 'brighthub' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'scroll-item-run-left-to-right' => __( 'Scroll Item Run Left To Right', 'brighthub' ),
                            'scroll-item-run-right-to-left' => __( 'Scroll Item Run Right To Left', 'brighthub' ),
                        ],
                        'default' => 'scroll-item-run-left-to-right',
                        'condition' => [
                            'pxl_scroll_effect' => 'scroll-item-run',
                        ],
                    ]
                );

                $element->add_control(
                    'pxl_scroll_effect_item_trigger_top',
                    [
                        'label' => __( 'Scroll Effect Item Trigger Top', 'brighthub' ),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%', 'vh' ],
                        'range' => [
                            'px' => [ 'min' => 0,  'max' => 5000 ],
                            '%'  => [ 'min' => 0,  'max' => 100 ],
                            'vh' => [ 'min' => 0,  'max' => 100 ],
                        ],
                        'default' => [
                            'size' => 0,
                            'unit' => 'px',
                        ],
                        'condition' => [
                            'pxl_scroll_effect' => 'scroll-item-run',
                        ],
                    ]
                );
                
                $element->end_controls_section();
            }
        }, 10, 3 );

        function brighthub_add_scroll_attributes( $element ) {
            $settings = $element->get_settings_for_display();
        
            if ( empty( $settings['pxl_scroll_effect'] ) || $settings['pxl_scroll_effect'] === 'none' ) {
                return;
            }
        
            $effect = $settings['pxl_scroll_effect'];
        
            $element->add_render_attribute( '_wrapper', 'class', sanitize_html_class( $effect ) );
        
            // ——— RUN (left/right) ————————————————————————————
            if ( $effect === 'scroll-item-run' ) {
                if ( ! empty( $settings['pxl_scroll_effect_item'] ) ) {
                    $element->add_render_attribute(
                        '_wrapper',
                        'class',
                        sanitize_html_class( $settings['pxl_scroll_effect_item'] )
                    );
                }
        
                if ( ! empty( $settings['pxl_scroll_effect_item_trigger_top']['size'] ) ) {
                    $size = floatval( $settings['pxl_scroll_effect_item_trigger_top']['size'] );
                    $unit = ! empty( $settings['pxl_scroll_effect_item_trigger_top']['unit'] ) ? $settings['pxl_scroll_effect_item_trigger_top']['unit'] : 'px';
        
                    $element->add_render_attribute( '_wrapper', 'data-trigger-top', $size . $unit );
                }
            }
        
            // ——— ZOOM ——————————————————————————————
            if ( $effect === 'scroll-item-zoom' ) {
                if ( isset( $settings['pxl_scroll_top_zoom']['size'] ) ) {
                    $size = floatval( $settings['pxl_scroll_top_zoom']['size'] );
                    $unit = ! empty( $settings['pxl_scroll_top_zoom']['unit'] ) ? $settings['pxl_scroll_top_zoom']['unit'] : 'px';
                    $element->add_render_attribute( '_wrapper', 'data-zoom-top', $size . $unit );
                }
        
                if ( isset( $settings['pxl_scroll_zoom_start']['size'] ) ) {
                    $element->add_render_attribute( '_wrapper', 'data-zoom-start', floatval( $settings['pxl_scroll_zoom_start']['size'] ) );
                }
        
                if ( isset( $settings['pxl_scroll_zoom_start_screen']['size'] ) ) {
                    $element->add_render_attribute( '_wrapper', 'data-zoom-start-screen', floatval( $settings['pxl_scroll_zoom_start_screen']['size'] ) );
                }
        
                if ( isset( $settings['pxl_scroll_zoom_end_screen']['size'] ) ) {
                    $element->add_render_attribute( '_wrapper', 'data-zoom-end-screen', floatval( $settings['pxl_scroll_zoom_end_screen']['size'] ) );
                }

                if ( isset( $settings['pxl_scroll_zoom_max_screen']['size'] ) ) {
                    $element->add_render_attribute( '_wrapper', 'data-zoom-max', floatval( $settings['pxl_scroll_zoom_max_screen']['size'] ) );
                }
            }
        
            $element->add_render_attribute( '_wrapper', 'data-scroll-effect', esc_attr( $effect ) );
            $element->add_render_attribute( '_wrapper', 'data-element-type', esc_attr( $element->get_type() ) );
        }
        
        add_action( 'elementor/frontend/section/before_render', 'brighthub_add_scroll_attributes' );
        add_action( 'elementor/frontend/container/before_render', 'brighthub_add_scroll_attributes' );
        add_action( 'elementor/frontend/widget/before_render', 'brighthub_add_scroll_attributes' );
        add_action( 'elementor/frontend/column/before_render', 'brighthub_add_scroll_attributes' );


        add_action( 'elementor/widget/render_content', 'pxl_render_element_custom_css', 10, 2 );
        add_action( 'elementor/section/render_content', 'pxl_render_element_custom_css', 10, 2 );
        add_action( 'elementor/container/render_content', 'pxl_render_element_custom_css', 10, 2 );

        function pxl_render_element_custom_css( $content, $element ) {
            $settings = $element->get_settings();
            
            if ( empty( $settings['pxl_custom_css'] ) ) {
                return $content;
            }
            
            $css = trim( $settings['pxl_custom_css'] );
            
            if ( empty( $css ) ) {
                return $content;
            }
            
            $element_id = $element->get_id();
            $css = str_replace( 'selector', '.elementor-element-' . $element_id, $css );
            
            $style_tag = '<style id="pxl-custom-css-' . $element_id . '">' . $css . '</style>';
            
            return $style_tag . $content;
        }

        add_action( 'elementor/frontend/before_render', function( $element ) {
            $settings = $element->get_settings();
            
            if ( ! empty( $settings['pxl_custom_css'] ) ) {
                $css = trim( $settings['pxl_custom_css'] );
                
                if ( ! empty( $css ) ) {
                    $element_id = $element->get_id();
                    $css = str_replace( 'selector', '.elementor-element-' . $element_id, $css );
                    
                    add_action( 'wp_head', function() use ( $element_id, $css ) {
                        echo '<style id="pxl-custom-css-' . $element_id . '">' . $css . '</style>';
                    }, 999 );
                    
                    if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                        ?>
                        <style id="pxl-editor-css-<?php echo esc_attr($element_id); ?>">
                            <?php echo esc_html($css); ?>
                        </style>
                        <?php
                    }
                }
            }
        });
    }
}

PXL_Elementor_Custom_Controls::init();