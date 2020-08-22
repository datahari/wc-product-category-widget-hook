<?php
function dh_top_category_of( $what ) {

    $what = get_term( $what );

    if( $what->parent == 0 ) {
        return $what->term_id;
    }

    return dh_top_category_of( $what->parent );
}

function dh_top_category_of_current() {
    global $wp_query;

    $current = $wp_query->get_queried_object();

    if( is_product( $current ) ) {
        $terms = get_the_terms( $current->ID, 'product_cat' );
        $current = array_shift( array_slice( $terms, 0, 1 ) );
    }

    if( $current->parent == 0 ) {
        return $current->term_id;
    }

    return dh_top_category_of( $current->parent );
}

function dh_product_categories_widget_show_only_children_of_top_category( $args ) {
    $top_category = dh_top_category_of_current();

    $categories = explode( ',', $args['include'] );
    $include = [];

    foreach( $categories as $category ) {
        // show only one topmost category
        if( dh_top_category_of( $category ) != $top_category ) {
            continue;
        }

        // dont show topmost category
        if( $category == $top_category ) {
            continue;
        }

        $include[] = $category;
    }

    $args['include'] = implode( ',', $include );

    return $args;
}

add_filter( 'woocommerce_product_categories_widget_args', 'dh_product_categories_widget_show_only_children_of_top_category' );
