<?php 
/**
 * Custom functions that act independently of the theme templates.
 *
 * @package Preschool and Kindergarten
 */

if( ! function_exists( 'preschool_and_kindergarten_get_post_meta' ) ) :
/**
 * Post meta info
*/
function preschool_and_kindergarten_get_post_meta(){
    
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	$posted_on = sprintf(
		esc_html_x( 'On %s', 'post date', 'preschool-and-kindergarten' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	$byline = sprintf(
		esc_html_x( 'By %s', 'post author', 'preschool-and-kindergarten' ),
		'<span class="authors vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="byline"> ' . $byline . '</span><span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

		// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'preschool-and-kindergarten' ) );
		if ( $categories_list && preschool_and_kindergarten_categorized_blog() ) {
			printf( '<span class="cat-links">' . esc_html__( 'In %1$s', 'preschool-and-kindergarten' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}
	}

	if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		/* translators: %s: post title */
		comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'preschool-and-kindergarten' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title() ) );
		echo '</span>';
	}

}
endif;


if ( ! function_exists( 'preschool_and_kindergarten_entry_footer' ) ) :
/**
 * Prints edit links
 */
function preschool_and_kindergarten_entry_footer() {	

    // Hide category and tag text for pages.
    if ( 'post' === get_post_type() && is_single() ) {
        /* translators: used between list items, there is a space after the comma */
        $tags_list = get_the_tag_list( '', esc_html__( ', ', 'preschool-and-kindergarten' ) );
        if ( $tags_list ) {
            echo '<span class="tags-links"><i class="fa fa-tags" aria-hidden="true"></i>' . $tags_list . '</span>'; // WPCS: XSS OK.
        }
    }
	
	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'preschool-and-kindergarten' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function preschool_and_kindergarten_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'preschool_and_kindergarten_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'preschool_and_kindergarten_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so preschool_and_kindergarten_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so preschool_and_kindergarten_categorized_blog should return false.
		return false;
	}
}


if( !function_exists( 'preschool_and_kindergarten_breadcrumbs_cb' ) ):
/**
 * Breadcrumb
*/
function preschool_and_kindergarten_breadcrumbs_cb() {    
    global $post;
    
    $post_page   = get_option( 'page_for_posts' ); //The ID of the page that displays posts.
    $show_front  = get_option( 'show_on_front' ); //What to show on the front page
    $showCurrent = get_theme_mod( 'preschool_and_kindergarten_ed_current', '1' ); // 1 - show current post/page title in breadcrumbs, 0 - don't show
    $delimiter   = get_theme_mod( 'preschool_and_kindergarten_breadcrumb_separator', __( '>', 'preschool-and-kindergarten' ) ); // delimiter between crumbs
    $home        = get_theme_mod( 'preschool_and_kindergarten_breadcrumb_home_text', __( 'Home', 'preschool-and-kindergarten' ) ); // text for the 'Home' link
    $before      = '<span class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">'; // tag before the current crumb
    $after       = '</span>'; // tag after the current crumb
      
    $depth = 1; 
    if( get_theme_mod( 'preschool_and_kindergarten_ed_breadcrumb' ) ){
        echo '<div id="crumbs" itemscope itemtype="https://schema.org/BreadcrumbList"><span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="' . esc_url( home_url() ) . '" class="home_crumb"><span itemprop="name">' . esc_html( $home ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . $delimiter . '</span></span>';
            if( is_home() && ! is_front_page() ){            
                $depth = 2;
                if( $showCurrent ) echo $before . '<span itemprop="name">' . esc_html( single_post_title( '', false ) ) .'</span><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;          
            }elseif( is_category() ){            
                $depth = 2;
                $thisCat = get_category( get_query_var( 'cat' ), false );
                if( $show_front === 'page' && $post_page ){ //If static blog post page is set
                    $p = get_post( $post_page );
                    echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_permalink( $post_page ) ) . '"><span itemprop="name">' . esc_html( $p->post_title ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . $delimiter . '</span></span>';
                    $depth ++;  
                }

                if ( $thisCat->parent != 0 ) {
                    $parent_categories = get_category_parents( $thisCat->parent, false, ',' );
                    $parent_categories = explode( ',', $parent_categories );

                    foreach ( $parent_categories as $parent_term ) {
                        $parent_obj = get_term_by( 'name', $parent_term, 'category' );
                        if( is_object( $parent_obj ) ){
                            $term_url    = get_term_link( $parent_obj->term_id );
                            $term_name   = $parent_obj->name;
                            echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="' . esc_url( $term_url ) . '"><span itemprop="name">' . esc_html( $term_name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . $delimiter . '</span></span>';
                            $depth ++;
                        }
                    }
                }

                if( $showCurrent ) echo $before . '<span itemprop="name">' .  esc_html( single_cat_title( '', false ) ) . '</span><meta itemprop="position" content="'. absint( $depth ).'" />' . $after;

            }elseif( is_tag() ){            
                $queried_object = get_queried_object();
                $depth = 2;

                if( $showCurrent ) echo $before . '<span itemprop="name">' . esc_html( single_tag_title( '', false ) ) .'</span><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;    
            }elseif( is_author() ){            
                $depth = 2;
                global $author;
                $userdata = get_userdata( $author );
                if( $showCurrent ) echo $before . '<span itemprop="name">' . esc_html( $userdata->display_name ) .'</span><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;  
            }elseif( is_day() ){            
                $depth = 2;
                echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_year_link( get_the_time( __( 'Y', 'preschool-and-kindergarten' ) ) ) ) . '"><span itemprop="name">' . esc_html( get_the_time( __( 'Y', 'preschool-and-kindergarten' ) ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . $delimiter . '</span></span>';
                $depth ++;
                echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_month_link( get_the_time( __( 'Y', 'preschool-and-kindergarten' ) ), get_the_time( __( 'm', 'preschool-and-kindergarten' ) ) ) ) . '"><span itemprop="name">' . esc_html( get_the_time( __( 'F', 'preschool-and-kindergarten' ) ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . $delimiter . '</span></span>';
                $depth ++;
                if( $showCurrent ) echo $before .'<span itemprop="name">'. esc_html( get_the_time( __( 'd', 'preschool-and-kindergarten' ) ) ) .'</span><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
                 
            }elseif( is_month() ){            
                $depth = 2;
                echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_year_link( get_the_time( __( 'Y', 'preschool-and-kindergarten' ) ) ) ) . '"><span itemprop="name">' . esc_html( get_the_time( __( 'Y', 'preschool-and-kindergarten' ) ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . $delimiter . '</span></span>';
                $depth++;
                if( $showCurrent ) echo $before .'<span itemprop="name">'. esc_html( get_the_time( __( 'F', 'preschool-and-kindergarten' ) ) ) .'</span><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;      
            }elseif( is_year() ){            
                $depth = 2;
                if( $showCurrent ) echo $before .'<span itemprop="name">'. esc_html( get_the_time( __( 'Y', 'preschool-and-kindergarten' ) ) ) .'</span><meta itemprop="position" content="'. absint( $depth ).'" />'. $after; 
            }elseif( is_single() && !is_attachment() ) {
                //For Woocommerce single product            
                if( preschool_and_kindergarten_is_woocommerce_activated() && 'product' === get_post_type() ){ 
                    if ( wc_get_page_id( 'shop' ) ) { 
                        //Displaying Shop link in woocommerce archive page
                        $_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
                        if ( ! $_name ) {
                            $product_post_type = get_post_type_object( 'product' );
                            $_name = $product_post_type->labels->singular_name;
                        }
                        echo ' <a href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $_name) . '</span></a> ' . '<span class="separator">' . $delimiter . '</span>';
                    }
                
                    if ( $terms = wc_get_product_terms( $post->ID, 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) ) ) {
                        $main_term = apply_filters( 'woocommerce_breadcrumb_main_term', $terms[0], $terms );
                        $ancestors = get_ancestors( $main_term->term_id, 'product_cat' );
                        $ancestors = array_reverse( $ancestors );

                        foreach ( $ancestors as $ancestor ) {
                            $ancestor = get_term( $ancestor, 'product_cat' );    
                            if ( ! is_wp_error( $ancestor ) && $ancestor ) {
                                echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a href="' . esc_url( get_term_link( $ancestor ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $ancestor->name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . $delimiter . '</span></span>';
                                $depth++;
                            }
                        }
                        echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a href="' . esc_url( get_term_link( $main_term ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $main_term->name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . $delimiter . '</span></span>';
                    }
                
                    if( $showCurrent ) echo $before .'<span itemprop="name">'. esc_html( get_the_title() ) .'</span><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
                                   
                }else{ 
                    //For Post                
                    $cat_object       = get_the_category();
                    $potential_parent = 0;
                    $depth            = 2;
                    
                    if( $show_front === 'page' && $post_page ){ //If static blog post page is set
                        $p = get_post( $post_page );
                        echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a href="' . esc_url( get_permalink( $post_page ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $p->post_title ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . $delimiter . '</span></span>';  
                        $depth++;
                    }
                    
                    if( is_array( $cat_object ) ){ //Getting category hierarchy if any
            
                        //Now try to find the deepest term of those that we know of
                        $use_term = key( $cat_object );
                        foreach( $cat_object as $key => $object ){
                            //Can't use the next($cat_object) trick since order is unknown
                            if( $object->parent > 0  && ( $potential_parent === 0 || $object->parent === $potential_parent ) ){
                                $use_term = $key;
                                $potential_parent = $object->term_id;
                            }
                        }
                        
                        $cat = $cat_object[$use_term];
                  
                        $cats = get_category_parents( $cat, false, ',' );
                        $cats = explode( ',', $cats );

                        foreach ( $cats as $cat ) {
                            $cat_obj = get_term_by( 'name', $cat, 'category' );
                            if( is_object( $cat_obj ) ){
                                $term_url    = get_term_link( $cat_obj->term_id );
                                $term_name   = $cat_obj->name;
                                echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="' . esc_url( $term_url ) . '"><span itemprop="name">' . esc_html( $term_name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . $delimiter . '</span></span>';
                                $depth ++;
                            }
                        }
                    }
        
                    if ( $showCurrent ) echo $before .'<span itemprop="name">'. esc_html( get_the_title() ) .'</span><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
                                 
                }        
            }elseif( is_page() ){            
                $depth = 2;
                if( $post->post_parent ){            
                    global $post;
                    $depth = 2;
                    $parent_id  = $post->post_parent;
                    $breadcrumbs = array();
                    
                    while( $parent_id ){
                        $current_page  = get_post( $parent_id );
                        $breadcrumbs[] = $current_page->ID;
                        $parent_id     = $current_page->post_parent;
                    }
                    $breadcrumbs = array_reverse( $breadcrumbs );
                    for ( $i = 0; $i < count( $breadcrumbs); $i++ ){
                        echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a href="' . esc_url( get_permalink( $breadcrumbs[$i] ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( get_the_title( $breadcrumbs[$i] ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /></span>';
                        if ( $i != count( $breadcrumbs ) - 1 ) echo ' <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                        $depth++;
                    }

                    if ( $showCurrent ) echo ' <span class="separator">' . esc_html( $delimiter ) . '</span> ' . $before .'<span itemprop="name">'. esc_html( get_the_title() ) .'</span><meta itemprop="position" content="'. absint( $depth ).'" /></span>'. $after;      
                }else{
                    if ( $showCurrent ) echo $before .'<span itemprop="name">'. esc_html( get_the_title() ) .'</span><meta itemprop="position" content="'. absint( $depth ).'" />'. $after; 
                }
            }elseif( is_search() ){            
                $depth = 2;
                if( $showCurrent ) echo $before .'<span itemprop="name">'. esc_html__( 'Search Results for "', 'preschool-and-kindergarten' ) . esc_html( get_search_query() ) . esc_html__( '"', 'preschool-and-kindergarten' ) .'</span><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;      
            }elseif( preschool_and_kindergarten_is_woocommerce_activated() && ( is_product_category() || is_product_tag() ) ){ 
                //For Woocommerce archive page        
                $depth = 2;
                if ( wc_get_page_id( 'shop' ) ) { 
                    //Displaying Shop link in woocommerce archive page
                    $_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
                    if ( ! $_name ) {
                        $product_post_type = get_post_type_object( 'product' );
                        $_name = $product_post_type->labels->singular_name;
                    }
                    echo ' <a href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $_name) . '</span></a> ' . '<span class="separator">' . $delimiter . '</span>';
                }
                $current_term = $GLOBALS['wp_query']->get_queried_object();
                if( is_product_category() ){
                    $ancestors = get_ancestors( $current_term->term_id, 'product_cat' );
                    $ancestors = array_reverse( $ancestors );
                    foreach ( $ancestors as $ancestor ) {
                        $ancestor = get_term( $ancestor, 'product_cat' );    
                        if ( ! is_wp_error( $ancestor ) && $ancestor ) {
                            echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a href="' . esc_url( get_term_link( $ancestor ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $ancestor->name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . $delimiter . '</span></span>';
                            $depth ++;
                        }
                    }
                }           
                if( $showCurrent ) echo $before . '<span itemprop="name">' . esc_html( $current_term->name ) .'</span><meta itemprop="position" content="'. absint( $depth ).'" />' . $after;           
            }elseif( preschool_and_kindergarten_is_woocommerce_activated() && is_shop() ){ //Shop Archive page
                $depth = 2;
                if ( get_option( 'page_on_front' ) == wc_get_page_id( 'shop' ) ) {
                    return;
                }
                $_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
                $shop_url = wc_get_page_id( 'shop' ) && wc_get_page_id( 'shop' ) > 0  ? get_the_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop' );
        
                if ( ! $_name ) {
                    $product_post_type = get_post_type_object( 'product' );
                    $_name = $product_post_type->labels->singular_name;
                }
                if( $showCurrent ) echo $before . '<span itemprop="name">' . esc_html( $_name ) .'</span><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;                    
            }elseif( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {            
                $depth = 2;
                $post_type = get_post_type_object(get_post_type());
                if( get_query_var('paged') ){
                    echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a href="' . esc_url( get_post_type_archive_link( $post_type->name ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $post_type->label ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />';
                    echo ' <span class="separator">' . $delimiter . '</span></span> ' . $before . sprintf( __('Page %s', 'preschool-and-kindergarten'), get_query_var('paged') ) . $after;
                }elseif( is_archive() ){
                    echo $before .'<a itemprop="item" href="' . esc_url( get_post_type_archive_link( $post_type->name ) ) . '"><span itemprop="name">'. esc_html( $post_type->label ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
                }else{
                    echo $before .'<a itemprop="item" href="' . esc_url( get_post_type_archive_link( $post_type->name ) ) . '"><span itemprop="name">'. esc_html( $post_type->label ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
                }              
            }elseif( is_attachment() ){            
                $depth  = 2;
                $parent = get_post( $post->post_parent );
                $cat    = get_the_category( $parent->ID );
                if( $cat ){
                    $cat = $cat[0];
                    echo get_category_parents( $cat, TRUE, ' <span class="separator">' . $delimiter . '</span> ');
                    echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a href="' . esc_url( get_permalink( $parent ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $parent->post_title ) . '<span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . ' <span class="separator">' . $delimiter . '</span></span>';
                }
                if( $showCurrent ) echo $before .'<a itemprop="item" href="' . esc_url( get_the_permalink() ) . '"><span itemprop="name">'. esc_html( get_the_title() ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;   
            }elseif ( is_404() ){
                if( $showCurrent ) echo $before . esc_html__( '404 Error - Page not Found', 'preschool-and-kindergarten' ) . $after;
            }
            if( get_query_var('paged') ) echo __( ' (Page', 'preschool-and-kindergarten' ) . ' ' . get_query_var('paged') . __( ')', 'preschool-and-kindergarten' );        
        echo '</div>';
    }
} 
endif;
add_action( 'preschool_and_kindergarten_breadcrumbs', 'preschool_and_kindergarten_breadcrumbs_cb' );

/**
 * Return sidebar layouts for pages
*/
function preschool_and_kindergarten_sidebar_layout(){
    global $post;
    
    if( get_post_meta( $post->ID, 'preschool_and_kindergarten_sidebar_layout', true ) ){
        return get_post_meta( $post->ID, 'preschool_and_kindergarten_sidebar_layout', true );    
    }else{
        return 'right-sidebar';
    }
}

if( ! function_exists( 'preschool_and_kindergarten_pagination' ) ) :
/**
 * Pagination
*/
function preschool_and_kindergarten_pagination(){    
    if( is_single() ){
        the_post_navigation();
    }else{
        the_posts_pagination( array(
            'prev_text'   => __( ' Prev ', 'preschool-and-kindergarten' ),
            'next_text'   => __( ' Next ', 'preschool-and-kindergarten' ),
            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'preschool-and-kindergarten' ) . ' </span>',
         ) );

    }    
}
endif;

/**
 * Returns Section header
*/
function preschool_and_kindergarten_get_section_header( $title, $description ){
    if( $title || $description ){ ?>
        <header class="header">
            <?php 
                if( $title ) echo '<h2 class="title">' . esc_html( $title ) . '</h2>';
                if( $description ) echo wpautop( wp_kses_post( $description ) ); 
            ?>
        </header>
    <?php
    }
}

/**
 * Query WooCommerce activation
 */
function preschool_and_kindergarten_is_woocommerce_activated() {
    return class_exists( 'woocommerce' ) ? true : false;
}

if( ! function_exists( 'preschool_and_kindergarten_ed_section') ):
    /**
     * Check if home page section are enable or not.
    */
    function preschool_and_kindergarten_ed_section(){
        $preschool_and_kindergarten_page_sections = array( 'banners', 'about', 'lessons', 'services', 'promotional', 'program', 'testimonials', 'staff', 'news' );
        $en_sec = false;
        foreach( $preschool_and_kindergarten_page_sections as $section ){ 
            if( get_theme_mod( 'preschool_and_kindergarten_ed_' . $section . '_section' ) == 1 ){
                $en_sec = true;
            }
        }
        return $en_sec;
    }
endif;

if( ! function_exists( 'preschool_and_kindergarten_change_comment_form_default_fields' ) ) :
/**
 * Change Comment form default fields i.e. author, email & url.
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function preschool_and_kindergarten_change_comment_form_default_fields( $fields ){    
    // get the current commenter if available
    $commenter = wp_get_current_commenter();
 
    // core functionality
    $req      = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );
    $required = ( $req ? " required" : '' );
    $author   = ( $req ? __( 'Name*', 'preschool-and-kindergarten' ) : __( 'Name', 'preschool-and-kindergarten' ) );
    $email    = ( $req ? __( 'Email*', 'preschool-and-kindergarten' ) : __( 'Email', 'preschool-and-kindergarten' ) );
 
    // Change just the author field
    $fields['author'] = '<p class="comment-form-author"><label class="screen-reader-text" for="author">' . esc_html__( 'Name', 'preschool-and-kindergarten' ) . '<span class="required">*</span></label><input id="author" name="author" placeholder="' . esc_attr( $author ) . '" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . $required . ' /></p>';
    
    $fields['email'] = '<p class="comment-form-email"><label class="screen-reader-text" for="email">' . esc_html__( 'Email', 'preschool-and-kindergarten' ) . '<span class="required">*</span></label><input id="email" name="email" placeholder="' . esc_attr( $email ) . '" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . $required. ' /></p>';
    
    $fields['url'] = '<p class="comment-form-url"><label class="screen-reader-text" for="url">' . esc_html__( 'Website', 'preschool-and-kindergarten' ) . '</label><input id="url" name="url" placeholder="' . esc_attr__( 'Website', 'preschool-and-kindergarten' ) . '" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>'; 
    
    return $fields;    
}
endif;
add_filter( 'comment_form_default_fields', 'preschool_and_kindergarten_change_comment_form_default_fields' );

if( ! function_exists( 'preschool_and_kindergarten_change_comment_form_defaults' ) ) :
/**
 * Change Comment Form defaults
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function preschool_and_kindergarten_change_comment_form_defaults( $defaults ){    
    $defaults['comment_field'] = '<p class="comment-form-comment"><label class="screen-reader-text" for="comment">' . esc_html__( 'Comment', 'preschool-and-kindergarten' ) . '</label><textarea id="comment" name="comment" placeholder="' . esc_attr__( 'Comment', 'preschool-and-kindergarten' ) . '" cols="45" rows="8" aria-required="true" required></textarea></p>';
    
    return $defaults;    
}
endif;
add_filter( 'comment_form_defaults', 'preschool_and_kindergarten_change_comment_form_defaults' );

if( ! function_exists( 'preschool_and_kindergarten_escape_text_tags' ) ) :
/**
 * Remove new line tags from string
 *
 * @param $text
 * @return string
 */
function preschool_and_kindergarten_escape_text_tags( $text ) {
    return (string) str_replace( array( "\r", "\n" ), '', strip_tags( $text ) );
}
endif;

if( ! function_exists( 'wp_body_open' ) ) :
/**
 * Fire the wp_body_open action.
 * Added for backwards compatibility to support pre 5.2.0 WordPress versions.
*/
function wp_body_open() {
	/**
	 * Triggered after the opening <body> tag.
    */
	do_action( 'wp_body_open' );
}
endif;

if( ! function_exists( 'preschool_and_kindergarten_get_image_sizes' ) ) :
/**
 * Get information about available image sizes
 */
function preschool_and_kindergarten_get_image_sizes( $size = '' ) {
 
    global $_wp_additional_image_sizes;
 
    $sizes = array();
    $get_intermediate_image_sizes = get_intermediate_image_sizes();
 
    // Create the full array with sizes and crop info
    foreach( $get_intermediate_image_sizes as $_size ) {
        if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
            $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
            $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
            $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
        } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
            $sizes[ $_size ] = array( 
                'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
            );
        }
    } 
    // Get only 1 size if found
    if ( $size ) {
        if( isset( $sizes[ $size ] ) ) {
            return $sizes[ $size ];
        } else {
            return false;
        }
    }
    return $sizes;
}
endif;

if ( ! function_exists( 'preschool_and_kindergarten_get_fallback_svg' ) ) :    
/**
 * Get Fallback SVG
*/
function preschool_and_kindergarten_get_fallback_svg( $post_thumbnail ) {
    if( ! $post_thumbnail ){
        return;
    }
    
    $image_size = preschool_and_kindergarten_get_image_sizes( $post_thumbnail );
     
    if( $image_size ){ ?>
        <div class="svg-holder">
             <svg class="fallback-svg" viewBox="0 0 <?php echo esc_attr( $image_size['width'] ); ?> <?php echo esc_attr( $image_size['height'] ); ?>" preserveAspectRatio="none">
                    <rect width="<?php echo esc_attr( $image_size['width'] ); ?>" height="<?php echo esc_attr( $image_size['height'] ); ?>" style="fill:#eae9e9;"></rect>
            </svg>
        </div>
        <?php
    }
}
endif;

if( ! function_exists( 'preschool_and_kindergarten_fonts_url' ) ) :
/**
 * Register custom fonts.
 */
function preschool_and_kindergarten_fonts_url() {
    $fonts_url = '';

    /*
    * translators: If there are characters in your language that are not supported
    * by Pacifico, translate this to 'off'. Do not translate into your own language.
    */
    $pacifico = _x( 'on', 'Pacifico font: on or off', 'preschool-and-kindergarten' );
    
    /*
    * translators: If there are characters in your language that are not supported
    * by Lato, translate this to 'off'. Do not translate into your own language.
    */
    $lato = _x( 'on', 'Lato font: on or off', 'preschool-and-kindergarten' );

    if ( 'off' !== $pacifico || 'off' !== $lato ) {
        $font_families = array();

        if( 'off' !== $pacifico ){
            $font_families[] = 'Pacifico';
        }

        if( 'off' !== $lato ){
            $font_families[] = 'Lato:400,400i,700';
        }

        $query_args = array(
            'family'  => urlencode( implode( '|', $font_families ) ),
            'display' => urlencode( 'fallback' ),
        );

        $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
    }

    return esc_url( $fonts_url );
}
endif;

/**
 * Get the file preloads.
 *
 * @param string $url    The URL of the remote webfont.
 * @param string $format The font-format. If you need to support IE, change this to "woff".
 */
function preschool_and_kindergarten_load_preload_local_fonts( $url, $format = 'woff2' ) {

	// Check if cached font files data preset present or not. Basically avoiding 'preschool_and_kindergarten_WebFont_Loader' class rendering.
	$local_font_files = get_site_option( 'preschool_and_kindergarten_local_font_files', false );

	if ( is_array( $local_font_files ) && ! empty( $local_font_files ) ) {
		$font_format = apply_filters( 'preschool_and_kindergarten_local_google_fonts_format', $format );
		foreach ( $local_font_files as $key => $local_font ) {
			if ( $local_font ) {
				echo '<link rel="preload" href="' . esc_url( $local_font ) . '" as="font" type="font/' . esc_attr( $font_format ) . '" crossorigin>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}	
		}
		return;
	}

	// Now preload font data after processing it, as we didn't get stored data.
	$font = preschool_and_kindergarten_webfont_loader_instance( $url );
	$font->set_font_format( $format );
	$font->preload_local_fonts();
}

if( ! function_exists( 'preschool_and_kindergarten_flush_local_google_fonts' ) ){
	/**
	 * Ajax Callback for flushing the local font
	 */
  	function preschool_and_kindergarten_flush_local_google_fonts() {

        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'preschool_and_kindergarten_flush_fonts_nonce' ) ) {
			wp_send_json_error( 'Invalid nonce', 403 );
		}

		// Check user capability - only administrators should flush fonts
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Insufficient permissions', 403 );
		}

		$WebFontLoader = new Preschool_And_Kindergarten_WebFont_Loader();
		//deleting the fonts folder using ajax
		$WebFontLoader->delete_fonts_folder();

		wp_send_json_success();
  	}
}
add_action( 'wp_ajax_flush_local_google_fonts', 'preschool_and_kindergarten_flush_local_google_fonts' );