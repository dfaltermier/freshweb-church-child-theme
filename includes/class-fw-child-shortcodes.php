<?php
/**
 * This file adds our theme's shortcodes.
 *
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

class FW_Child_Shortcodes {

    function __construct() {

        // Include our own theme shortcodes.
        add_action( 'init', array( $this, 'add_shortcodes' ) );

    }

    /**
     * Add shortcodes.
     */
    public function add_shortcodes() {

        add_shortcode( 'church_portfolio_list', array( $this, 'church_portfolio_list' ) );

    }

    /**
     * Create portfolio list shortcode
     *
     * @param  array  $atts    Array with shortcode attributes.
     * @param  string $content Content wrapped by shortcode.
     * @return string HTML
     */
    public function church_portfolio_list( $atts, $content = null ) {

        global $wp_query;
        global $portfolio_project_id;
        global $qode_options_proya;
        $portfolio_qode_like = "on";

        if (isset($qode_options_proya['portfolio_qode_like'])) {
            $portfolio_qode_like = $qode_options_proya['portfolio_qode_like'];
        }

        $args = array(
            "type"                   => "standard",
            "box_border"             => "",
            "box_background_color"   => "",
            "box_border_color"       => "",
            "box_border_width"       => "",
            "columns"                => "3",
            "grid_size"              => "",
            "image_size"             => "",
            "show_published_date"    => "no",
            "show_portfolio_category" => "no",
            "show_portfolio_excerpt" => "no",
            "order_by"               => "date",
            "order"                  => "ASC",
            "number"                 => "-1",
            "filter"                 => "no",
            "filter_color"           => "",
            "lightbox"               => "yes",
            "view_button"            => "yes",
            "category"               => "",
            "selected_projects"      => "",
            "show_load_more"         => "yes",
            "title_tag"              => "h5",
            "portfolio_separator"    => "",
			"text_align"			 => ""
        );

        extract(shortcode_atts($args, $atts));

        $headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');

        //get correct heading value. If provided heading isn't valid get the default one
        $title_tag = (in_array($title_tag, $headings_array)) ? $title_tag : $args['title_tag'];

        $html = "";

        $_type_class = '';
        $_portfolio_space_class = '';
        $_portfolio_masonry_with_space_class = '';
        if ($type == "hover_text") {
            $_type_class = " hover_text";
            $_portfolio_space_class = "portfolio_with_space";
        } elseif ($type == "standard" || $type == "masonry_with_space"){
            $_type_class = " standard";
            $_portfolio_space_class = "portfolio_with_space";
            if($type == "masonry_with_space"){
                $_portfolio_masonry_with_space_class = ' masonry_with_space';
            }
        } elseif ($type == "standard_no_space"){
            $_type_class = " standard_no_space";
            $_portfolio_space_class = "portfolio_no_space";
        } elseif ($type == "hover_text_no_space"){
            $_type_class = " hover_text no_space";
            $_portfolio_space_class = "portfolio_no_space";
        }

        $_portfolio_masonry_with_space_class = '';
        if ($type == "hover_text") {
            $_type_class = " hover_text";
            $_portfolio_space_class = "portfolio_with_space portfolio_with_hover_text";
        } elseif ($type == "standard" || $type == "masonry_with_space"){
            $_type_class = " standard";
            $_portfolio_space_class = "portfolio_with_space portfolio_standard";
            if($type == "masonry_with_space"){
                $_portfolio_masonry_with_space_class = ' masonry_with_space';
            }
        } elseif ($type == "standard_no_space"){
            $_type_class = " standard_no_space";
            $_portfolio_space_class = "portfolio_no_space portfolio_standard";
        } elseif ($type == "hover_text_no_space"){
            $_type_class = " hover_text no_space";
            $_portfolio_space_class = "portfolio_no_space portfolio_with_hover_text";
        }


		$portfolio_box_style = "";
		$portfolio_description_class = "";
		if($box_border == "yes" || $box_background_color != ""){

			$portfolio_box_style .= "style=";
			if($box_border == "yes"){
				$portfolio_box_style .= "border-style:solid;";
				if($box_border_color != "" ){
					$portfolio_box_style .= "border-color:" . $box_border_color . ";";
				}
				if($box_border_width != "" ){
					$portfolio_box_style .= "border-width:" . $box_border_width . "px;";
				}
			}
			if($box_background_color != ""){
				$portfolio_box_style .= "background-color:" . $box_background_color . ";";
			}
			$portfolio_box_style .= "'";

		}

		if($text_align !== '') {
			$portfolio_description_class .= 'text_align_'.$text_align;
		}

		$portfolio_separator_aignment = "center";
		if($text_align != ""){
			$portfolio_separator_aignment = $text_align;
		}

		$filter_style = "";
		if($filter_color != ""){
			$filter_style = " style='";
			$filter_style .= "color:$filter_color";
			$filter_style .= "'";
		}

        if($type != 'masonry') {
            $html .= "<div class='projects_holder_outer v$columns $_portfolio_space_class $_portfolio_masonry_with_space_class'>";
            if ($filter == "yes") {

                if($type == 'masonry_with_space'){
                    $html .= "<div class='filter_outer'>";
                    $html .= "<div class='filter_holder'>
						<ul>
						<li class='filter' data-filter='*'><span>" . __('All', 'qode') . "</span></li>";
                    if ($category == "") {
                        $args = array(
                            'parent' => 0
                        );
                        $portfolio_categories = get_terms('portfolio_category', $args);
                    } else {
                        $top_category = get_term_by('slug', $category, 'portfolio_category');
                        $term_id = '';
                        if (isset($top_category->term_id))
                            $term_id = $top_category->term_id;
                        $args = array(
                            'parent' => $term_id
                        );
                        $portfolio_categories = get_terms('portfolio_category', $args);
                    }
                    foreach ($portfolio_categories as $portfolio_category) {
                        $html .= "<li class='filter' data-filter='.portfolio_category_$portfolio_category->term_id'><span>$portfolio_category->name</span>";
                        $args = array(
                            'child_of' => $portfolio_category->term_id
                        );
                        $html .= '</li>';
                    }
                    $html .= "</ul></div>";
                    $html .= "</div>";

                }else{

                    $html .= "<div class='filter_outer'>";
                    $html .= "<div class='filter_holder'>
                            <ul>
                            <li class='filter' data-filter='all'><span". $filter_style .">" . __('All', 'qode') . "</span></li>";
                    if ($category == "") {
                        $args = array(
                            'parent' => 0
                        );
                        $portfolio_categories = get_terms('portfolio_category', $args);
                    } else {
                        $top_category = get_term_by('slug', $category, 'portfolio_category');
                        $term_id = '';
                        if (isset($top_category->term_id))
                            $term_id = $top_category->term_id;
                        $args = array(
                            'parent' => $term_id
                        );
                        $portfolio_categories = get_terms('portfolio_category', $args);
                    }
                    foreach ($portfolio_categories as $portfolio_category) {
                        $html .= "<li class='filter' data-filter='portfolio_category_$portfolio_category->term_id'><span". $filter_style .">$portfolio_category->name</span>";
                        $args = array(
                            'child_of' => $portfolio_category->term_id
                        );
                        $html .= '</li>';
                    }
                    $html .= "</ul></div>";
                    $html .= "</div>";
                }
            }

            $thumb_size_class = "";
            //get proper image size
            switch($image_size) {
                case 'landscape':
                    $thumb_size_class = 'portfolio_landscape_image';
                    break;
                case 'portrait':
                    $thumb_size_class = 'portfolio_portrait_image';
                    break;
                case 'square':
                    $thumb_size_class = 'portfolio_square_image';
                    break;
                default:
                    $thumb_size_class = 'portfolio_full_image';
                    break;
            }

            $html .= "<div class='projects_holder clearfix v$columns$_type_class $thumb_size_class'>\n";
            if (get_query_var('paged')) {
                $paged = get_query_var('paged');
            } elseif (get_query_var('page')) {
                $paged = get_query_var('page');
            } else {
                $paged = 1;
            }

            if ($category == "") {
                $args = array(
                    'post_type' => 'portfolio_page',
                    'orderby' => $order_by,
                    'order' => $order,
                    'posts_per_page' => $number,
                    'paged' => $paged
                );
            } else {
                $args = array(
                    'post_type' => 'portfolio_page',
                    'portfolio_category' => $category,
                    'orderby' => $order_by,
                    'order' => $order,
                    'posts_per_page' => $number,
                    'paged' => $paged
                );
            }
            $project_ids = null;
            if ($selected_projects != "") {
                $project_ids = explode(",", $selected_projects);
                $args['post__in'] = $project_ids;
            }
            query_posts($args);
            if (have_posts()) : while (have_posts()) : the_post();
                $terms = wp_get_post_terms(get_the_ID(), 'portfolio_category');
                $html .= "<article class='mix ";
                foreach ($terms as $term) {
                    $html .= "portfolio_category_$term->term_id ";
                }

                $title = get_the_title();
                $featured_image_array = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); //original size

                if(get_post_meta(get_the_ID(), 'qode_portfolio-lightbox-link', true) != ""){
                    $large_image = get_post_meta(get_the_ID(), 'qode_portfolio-lightbox-link', true);
                } else {
                    $large_image = $featured_image_array[0];
                }

                $slug_list_ = "pretty_photo_gallery";

				//get proper image size
				switch($image_size) {
					case 'landscape':
						$thumb_size = 'portfolio-landscape';
						break;
					case 'portrait':
						$thumb_size = 'portfolio-portrait';
						break;
					case 'square':
						$thumb_size = 'portfolio-square';
						break;
					default:
						$thumb_size = 'full';
						break;
				}

                if($type == "masonry_with_space"){
                    $thumb_size = 'portfolio_masonry_with_space';
                }

                $custom_portfolio_link = get_post_meta(get_the_ID(), 'qode_portfolio-external-link', true);
                $portfolio_link = $custom_portfolio_link != "" ? $custom_portfolio_link : get_permalink();

                if(get_post_meta(get_the_ID(), 'qode_portfolio-external-link-target', true) != ""){
                    $custom_portfolio_link_target = get_post_meta(get_the_ID(), 'qode_portfolio-external-link-target', true);
                } else {
                    $custom_portfolio_link_target = '_blank';
                }

                $target = $custom_portfolio_link != "" ? $custom_portfolio_link_target : '_self';

                $html .="'>";

                $html .= "<div class='image_holder'>";
                $html .= "<a class='portfolio_link_for_touch' href='".$portfolio_link."' target='".$target."'>";
                $html .= "<span class='image'>";
                $html .= get_the_post_thumbnail(get_the_ID(), $thumb_size);
                $html .= "</span>";
                $html .= "</a>";

                if ($type == "standard" || $type == "standard_no_space" || $type == "masonry_with_space") {
                    $html .= "<span class='text_holder'>";
                    $html .= "<span class='text_outer'>";
                    $html .= "<span class='text_inner'>";
                    $html .= "<span class='feature_holder'>";
					if($lightbox == "yes" || $portfolio_qode_like == "on" || $view_button !== "no"){
						$html .= '<span class="feature_holder_icons">';
						if ($lightbox == "yes") {
							$html .= "<a class='lightbox qbutton small white' title='" . $title . "' href='" . $large_image . "' data-rel='prettyPhoto[" . $slug_list_ . "]'>" . __('zoom', 'qode'). "</a>";
						}
						if($view_button !== "no"){
							$html .= "<a class='preview qbutton small white' href='" . $portfolio_link . "' target='".$target."'>" . __('view', 'qode'). "</a>";
						}
						if ($portfolio_qode_like == "on") {
							$html .= "<span class='portfolio_like qbutton small white'>";
							$portfolio_project_id = get_the_ID();

							if (function_exists('qode_like_portfolio_list')) {
								$html .= qode_like_portfolio_list();
							}
							$html .= "</span>";
						}
						$html .= "</span>";
					}
                    $html .= "</span></span></span></span>";


                } else if ($type == "hover_text" || $type == "hover_text_no_space") {

                    $html .= "<span class='text_holder'>";
                    $html .= "<span class='text_outer'>";
                    $html .= "<span class='text_inner'>";
                    $html .= '<div class="hover_feature_holder_title"><div class="hover_feature_holder_title_inner">';
                    $html .= '<'.$title_tag.' class="portfolio_title"><a href="' . $portfolio_link . '" target="'.$target.'">' . get_the_title() . '</a></'.$title_tag.'>';

                    // Add the published date.
                    if ($show_published_date == "yes") {
                        $html .= '<div class="published_date">' . get_the_date( get_option( 'date_format' ) )  . '</div>';
                    }

                    // Add portfolio category.
                    if ($show_portfolio_category == "yes") {
                        $html .= '<span class="project_category">';
                        $k = 1;
                        foreach ($terms as $term) {
                            $html .= "$term->name";
                            if (count($terms) != $k) {
                                $html .= ', ';
                            }
                            $k++;
                        }
                        $html .= '</span>';
                    }

                    // Add portfolio separator.
					if($portfolio_separator == "yes"){
						$html .= '<div class="portfolio_separator separator  small ' . $portfolio_separator_aignment . '"></div>';
					}

                    // Add portfolio excerpt.
                    if ($show_portfolio_excerpt == "yes") {
                        $portfolio_excerpt = trim( get_the_excerpt() );
                        if ( '' != $portfolio_excerpt ) {
                            $html .= '<div class="portfolio_excerpt">' . $portfolio_excerpt . '</div>';
                        }
                    }

                    $html .= '</div></div>';
                    $html .= "<span class='feature_holder'>";
					if($lightbox == "yes" || $portfolio_qode_like == "on" || $view_button !== "no"){
						$html .= '<span class="feature_holder_icons">';
						if ($lightbox == "yes") {
							$html .= "<a class='lightbox qbutton small white' title='" . $title . "' href='" . $large_image . "' data-rel='prettyPhoto[" . $slug_list_ . "]'>" . __('zoom', 'qode'). "</a>";
						}
						if($view_button !== "no"){
							$html .= "<a class='preview qbutton small white' href='" . $portfolio_link . "' target='".$target."'>" . __('view', 'qode'). "</a>";
						}
						if ($portfolio_qode_like == "on") {
							$html .= "<span class='portfolio_like qbutton small white'>";
							$portfolio_project_id = get_the_ID();

							if (function_exists('qode_like_portfolio_list')) {
								$html .= qode_like_portfolio_list();
							}
							$html .= "</span>";
						}
						$html .= "</span>";
					}
                    $html .= "</span></span></span></span>";
                }
                $html .= "</div>";
                if ($type == "standard" || $type == "standard_no_space" || $type == "masonry_with_space") {
                    $html .= "<div class='portfolio_description ".$portfolio_description_class."'". $portfolio_box_style .">";
                    $html .= '<'.$title_tag.' class="portfolio_title"><a href="' . $portfolio_link . '" target="'.$target.'">' . get_the_title() . '</a></'.$title_tag.'>';

                    // Add the published date.
                    if ($show_published_date == "yes") {
                        $html .= '<div class="published_date">' . get_the_date( get_option( 'date_format' ) )  . '</div>';
                    }

                    // Add portfolio category.
                    if ($show_portfolio_category == "yes") {
                        $html .= '<span class="project_category">';
                        $k = 1;
                        foreach ($terms as $term) {
                            $html .= "$term->name";
                            if (count($terms) != $k) {
                                $html .= ', ';
                            }
                            $k++;
                        }
                        $html .= '</span>';
                    }

                    // Add portfolio separator.
					if($portfolio_separator == "yes"){
						$html .= '<div class="portfolio_separator separator  small ' . $portfolio_separator_aignment . '"></div>';
					}

                    // Add portfolio excerpt.
                    if ($show_portfolio_excerpt == "yes") {
                        $portfolio_excerpt = trim( get_the_excerpt() );
                        if ( '' != $portfolio_excerpt ) {
                            $html .= '<div class="portfolio_excerpt">' . $portfolio_excerpt . '</div>';
                        }
                    }

                    $html .= '</div>';
                }

                $html .= "</article>\n";

            endwhile;

                $i = 1;
                while ($i <= $columns) {
                    $i++;
                    if ($columns != 1) {
                        $html .= "<div class='filler'></div>\n";
                    }
                }

            else:
                ?>
                <p><?php _e('Sorry, no posts matched your criteria.', 'qode'); ?></p>
            <?php
            endif;


            $html .= "</div>";
            if (get_next_posts_link()) {
                if ($show_load_more == "yes" || $show_load_more == "") {
                    $html .= '<div class="portfolio_paging"><span rel="' . $wp_query->max_num_pages . '" class="load_more">' . get_next_posts_link(__('Show more', 'qode')) . '</span></div>';
                    $html .= '<div class="portfolio_paging_loading"><a href="javascript: void(0)" class="qbutton">'.__('Loading...', 'qode').'</a></div>';
                }
            }
            $html .= "</div>";
            wp_reset_query();
        } else {
            if ($filter == "yes") {

                $html .= "<div class='filter_outer'>";
                $html .= "<div class='filter_holder'>
						<ul>
						<li class='filter' data-filter='*'><span>" . __('All', 'qode') . "</span></li>";
                if ($category == "") {
                    $args = array(
                        'parent' => 0
                    );
                    $portfolio_categories = get_terms('portfolio_category', $args);
                } else {
                    $top_category = get_term_by('slug', $category, 'portfolio_category');
                    $term_id = '';
                    if (isset($top_category->term_id))
                        $term_id = $top_category->term_id;
                    $args = array(
                        'parent' => $term_id
                    );
                    $portfolio_categories = get_terms('portfolio_category', $args);
                }
                foreach ($portfolio_categories as $portfolio_category) {
                    $html .= "<li class='filter' data-filter='.portfolio_category_$portfolio_category->term_id'><span>$portfolio_category->name</span>";
                    $args = array(
                        'child_of' => $portfolio_category->term_id
                    );
                    $html .= '</li>';
                }
                $html .= "</ul></div>";
                $html .= "</div>";


            }

			$grid_number_of_columns = "gs5";
			if($grid_size == 4){
				$grid_number_of_columns = "gs4";
			}
            $html .= "<div class='projects_masonry_holder ". $grid_number_of_columns ."'>";
            if (get_query_var('paged')) {
                $paged = get_query_var('paged');
            } elseif (get_query_var('page')) {
                $paged = get_query_var('page');
            } else {
                $paged = 1;
            }
            if ($category == "") {
                $args = array(
                    'post_type' => 'portfolio_page',
                    'orderby' => $order_by,
                    'order' => $order,
                    'posts_per_page' => $number,
                    'paged' => $paged
                );
            } else {
                $args = array(
                    'post_type' => 'portfolio_page',
                    'portfolio_category' => $category,
                    'orderby' => $order_by,
                    'order' => $order,
                    'posts_per_page' => $number,
                    'paged' => $paged
                );
            }
            $project_ids = null;
            if ($selected_projects != "") {
                $project_ids = explode(",", $selected_projects);
                $args['post__in'] = $project_ids;
            }
            query_posts($args);
            if (have_posts()) : while (have_posts()) : the_post();
                $terms = wp_get_post_terms(get_the_ID(), 'portfolio_category');
                $featured_image_array = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); //original size

                if(get_post_meta(get_the_ID(), 'qode_portfolio-lightbox-link', true) != ""){
                    $large_image = get_post_meta(get_the_ID(), 'qode_portfolio-lightbox-link', true);
                } else {
                    $large_image = $featured_image_array[0];
                }

                $custom_portfolio_link = get_post_meta(get_the_ID(), 'qode_portfolio-external-link', true);
                $portfolio_link = $custom_portfolio_link != "" ? $custom_portfolio_link : get_permalink();

                if(get_post_meta(get_the_ID(), 'qode_portfolio-external-link-target', true) != ""){
                    $custom_portfolio_link_target = get_post_meta(get_the_ID(), 'qode_portfolio-external-link-target', true);
                } else {
                    $custom_portfolio_link_target = '_blank';
                }

                $target = $custom_portfolio_link != "" ? $custom_portfolio_link_target : '_self';

                $masonry_size = "default";
                $masonry_size =  get_post_meta(get_the_ID(), "qode_portfolio_type_masonry_style", true);
                $image_size="";
                if($masonry_size == "large_width"){
                    $image_size = "portfolio_masonry_wide";
                }elseif($masonry_size == "large_height"){
                    $image_size = "portfolio_masonry_tall";
                }elseif($masonry_size == "large_width_height"){
                    $image_size = "portfolio_masonry_large";
                } else{
                    $image_size = "portfolio_masonry_regular";
                }

                if($type == "masonry_with_space"){
                    $image_size = "portfolio_masonry_with_space";
                }

                $slug_list_ = "pretty_photo_gallery";
                $title = get_the_title();
                $html .= "<article class='portfolio_masonry_item ";
                foreach ($terms as $term) {
                    $html .= "portfolio_category_$term->term_id ";
                }
                $html .=" " . $masonry_size;
                $html .="'>";

                $html .= "<div class='image_holder'>";
                $html .= "<a class='portfolio_link_for_touch' href='".$portfolio_link."' target='".$target."'>";
                $html .= "<span class='image'>";
                $html .= get_the_post_thumbnail(get_the_ID(), $image_size);
                $html .= "</span>";
                $html .= "</a>";
                $html .= "<span class='text_holder'>";
                $html .= "<span class='text_outer'>";
                $html .= "<span class='text_inner'>";
                $html .= '<div class="hover_feature_holder_title"><div class="hover_feature_holder_title_inner">';
                $html .= '<'.$title_tag.' class="portfolio_title"><a href="' . $portfolio_link . '" target="'.$target.'">' . get_the_title() . '</a></'.$title_tag.'>';

                // Add the published date.
                if ($show_published_date == "yes") {
                    $html .= '<div class="published_date">' . get_the_date( get_option( 'date_format' ) )  . '</div>';
                }

                // Add portfolio category.
                if ($show_portfolio_category == "yes") {
                    $html .= '<span class="project_category">';
                    $k = 1;
                    foreach ($terms as $term) {
                        $html .= "$term->name";
                        if (count($terms) != $k) {
                            $html .= ', ';
                        }
                        $k++;
                    }
                    $html .= '</span>';
                }

                // Add the portfolio separator.
				if($portfolio_separator == "yes"){
					$html .= '<div class="portfolio_separator separator  small ' . $portfolio_separator_aignment . '"></div>';
				}

                // Add portfolio excerpt.
                if ($show_portfolio_excerpt == "yes") {
                    $portfolio_excerpt = trim( get_the_excerpt() );
                    if ( '' != $portfolio_excerpt ) {
                        $html .= '<div class="portfolio_excerpt">' . $portfolio_excerpt . '</div>';
                    }
                }

                $html .= '</div></div>';
				if($lightbox == "yes" || $portfolio_qode_like == "on" || $view_button !== "no"){
              	  $html .= "<span class='feature_holder'>";

					$html .= '<span class="feature_holder_icons">';
					if ($lightbox == "yes") {
						$html .= "<a class='lightbox qbutton small white' title='" . $title . "' href='" . $large_image . "' data-rel='prettyPhoto[" . $slug_list_ . "]'>" . __('zoom', 'qode'). "</a>";
					}
					if($view_button !== "no"){
						$html .= "<a class='preview qbutton small white' href='" . $portfolio_link . "' target='".$target."'>" . __('view', 'qode'). "</i></a>";
					}
					if ($portfolio_qode_like == "on") {
						$html .= "<span class='portfolio_like qbutton small white'>";
						$portfolio_project_id = get_the_ID();

						if (function_exists('qode_like_portfolio_list')) {
							$html .= qode_like_portfolio_list();
						}
						$html .= "</span>";
					}
					$html .= "</span>";

                $html .= "</span>";
				}
                $html .= "</span></span></span>";
                $html .= "</div>";
                $html .= "</article>";

            endwhile;
            else:
                ?>
                <p><?php _e('Sorry, no posts matched your criteria.', 'qode'); ?></p>
            <?php
            endif;
            wp_reset_query();
            $html .= "</div>";
        }
        return $html;
    }

}
