<?php

/**
 * Custom Nav Menus
 */
register_nav_menus( array(
	'head' => 'Header Menu',
	'foot' => 'Footer Menu'
	) );


/**
 * Featured Images
 */
add_theme_support('post-thumbnails' );
	add_image_size('hard', 980, 400, true );
	add_image_size('flex', 560, 999 );

/**
 * Widgetized Sections
 */
function base_widgets_init() {

	register_sidebar( array(
		'name' => __( 'Sidebar', 'base' ),
		'before_title' => '<h3>',
		'after_title' => '</h3>',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer', 'base' ),
		'before_title' => '<h3>',
		'after_title' => '</h3>',
		'before_widget' => '<div id="%1$s" class="footget %2$s">',
		'after_widget' => '</div>',
	) );
}
add_action( 'widgets_init', 'base_widgets_init' );


function scripts_and_styles() {

	if( is_singular() && comments_open() ) 
		wp_enqueue_style('comment-style', get_bloginfo('template_url') . '/css/comments.css');

	//wp_enqueue_script('jquery');
	//wp_enqueue_script('cform', get_stylesheet_directory_uri() . '/js/cform.js', array('jquery'), NULL );
	//wp_enqueue_script('custom', get_bloginfo('template_url') . '/js/custom.js', array('jquery'));

}
add_action('wp_enqueue_scripts', 'scripts_and_styles');

function columns_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'no' => '1/3',
	), $atts ) );
		
	list( $no, $all ) = explode( '/', $no );

	if( $all % 2 == 0 ) $class = 'two';		
	if( $all % 3 == 0 ) $class = 'three';		
	if( $all % 4 == 0 ) $class = 'four';		
	if( $all % 5 == 0 ) $class = 'five';		
	if( $all % 6 == 0 ) $class = 'six';		
	
	if( $no == 1 )
		$col = '<div class="cols"><div class="col col-'. $no .' '. $class .'  first">' . $content . '</div>'; 		
	elseif( $no > 1 && $no != $all )
		$col = '<div class="col col-'. $no .' '. $class .'">' . $content . '</div>';
	else 
		$col = '<div class="col col-'. $no .' '. $class .' last">' . $content . '</div></div>'; 		

	return $col;
}
add_shortcode( 'column', 'columns_shortcode' );


/**
* Shorten
* Get part of the content by defining the number of characters.
*
* @author: Baki Goxhaj
* @author URI: http://www.wplancer.com/
*
* @param int $len Required. Number of characters to show
* @param string $str Optional, default is empty. Full text to be shortened.
* @param string $more Optional. Read more link text.
* @param bool $cut Optional, default is true. Remove half-cut words from the shortened text. 
* @return string HTML content.
*/
function shorten( $len, $str = '', $more = 'Read more &rarr;', $cut = true ) {
	if( $str == '' ) $str = get_the_content(); 
	
	$str = strip_tags( strip_shortcodes( $str ) );
	
	$read = '<a href="' . get_permalink() . '" title="' . get_the_title() . '">' . $more . '</a>';
   
	if( strlen( $str ) <= $len ) 
		echo $str;
	else
		echo ( $cut ? substr( $str, 0, strrpos( substr( $str, 0, $len ), ' ' ) ) : substr( $str, 0, $len ) ) . '... ' . $read;

}

function the_location() {
	global $post;
	
	if( is_archive() )
		$where = 'Browsing Website <em>Archive</em>';
	if( is_category() )
		$where = 'Browsing <em>' . single_cat_title('', false) . '</em> Category';
	if( is_tag() )
		$where = 'Browsing <em>' . single_tag_title('', false) . '</em> Tag';
	if( is_tax() )
		$where = 'Browsing <em>' . single_term_title('', false) . '</em> Archive';
	if( is_author() ) 
		$where = 'Browsing all posts by <em>' . get_the_author_meta( 'display_name', get_query_var('author') ) . '</em>' ;
	if( is_search() ) 
		$where = 'Browsing <em>' . get_search_query() . '</em> Search Results';
	if( is_404() ) 
		$where = 'Nothing Found - <em>Sorry!<em>';

	echo $where;
}


/**
* A pagination function
* @param integer $range: The range of the slider, works best with even numbers
* Used WP functions:
* get_pagenum_link($i) - creates the link, e.g. http://site.com/page/4
* previous_posts_link(' « '); - returns the Previous page link
* next_posts_link(' » '); - returns the Next page link
*/
function the_pagination( $range = 4, $wrap = true ){
	// $paged - number of the current page
	global $paged, $wp_query;

	// How many pages do we have?  
	if( !isset( $max_page ) )
		$max_page = $wp_query->max_num_pages;

	// We need the pagination only if there is more than 1 page
	if( $max_page > 1 ) :

		if( $wrap == true ) echo '<div class="pagination">';		
		
		if ( !$paged ) $paged = 1;
	
		// On the first page, don't put the First page link
		if( $paged != 1 ) {
			echo '<a class="jump first" href=' . get_pagenum_link( 1 ) . '>First</a>';
			// To the previous page
			echo '<a href="'. previous_posts( false ) .'" class="prev"> « </a>';
			//previous_posts_link(' « ');
		}
	
		// We need the sliding effect only if there are more pages than is the sliding range
		if ( $max_page > $range ) {
			// When closer to the beginning
			if ( $paged < $range ) {
				for( $i = 1; $i <= ( $range + 1 ); $i++ ) :
					echo '<a href="' . get_pagenum_link( $i ) .'"';
					if( $i == $paged ) echo 'class="current"';
					echo ">$i</a>";
				endfor;
		}

		// When closer to the end
		elseif( $paged >= ( $max_page - ceil(( $range/2 )) ) ) {
			for( $i = $max_page - $range; $i <= $max_page; $i++ ) :
				echo "<a href='" . get_pagenum_link($i) ."'";
				if( $i==$paged) echo "class='current'";
				echo ">$i</a>";
			endfor;
		}

		// Somewhere in the middle
		elseif( $paged >= $range && $paged < ($max_page - ceil(($range/2)))){
			for($i = ($paged - ceil($range/2)); $i <= ($paged + ceil(($range/2))); $i++):
				echo "<a href='" . get_pagenum_link($i) ."'";
				if($i==$paged) echo "class='current'";
				echo ">$i</a>";
			endfor;
		}
	}

	// Less pages than the range, no sliding effect needed
	else {
		for( $i = 1; $i <= $max_page; $i++ ) :
			echo "<a href='" . get_pagenum_link($i) ."'";
			if($i==$paged) echo "class='current'";
			echo ">$i</a>";
		endfor;
	}

	// Next page
	echo '<a href="'. next_posts( 0, false ) .'" class="next"> » </a>';
	//next_posts_link(' » '); 
	
	// On the last page, don't put the Last page link
	if( $paged != $max_page )
		echo '<a class="jump last" href=' . get_pagenum_link( $max_page ) . '>Last</a>';

	if( $wrap == true ) echo '</div>';		

	endif;
}

function curl( $url ) {
	$ch = curl_init( $url );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0 );
	//curl_setopt($ch, CURLOPT_USERAGENT, "yourdomain.com");
	curl_setopt($ch, CURLOPT_TIMEOUT, 10 );
	$data = curl_exec( $ch );
	curl_close( $ch );
	return $data;
}


/**
 * Fetch the number of fans from Twitter XML API
 *
 * @author Baki Goxhaj
 * @copyright MonsterThemes
 * @version 0.1
 *
 * @param string $user
 * @return string
 */
function followers( $user ) {
	
	$xml = curl('http://twitter.com/users/show/' . $user ); 
	
	if( $xml ) :
		$obj = simplexml_load_string( $xml );
		return $obj->followers_count;
	else :
	 return 0; 
	endif;
}

/**
 * Fetch the number of fans from Facebook Graph API
 *
 * @author Baki Goxhaj
 * @copyright MonsterThemes
 * @version 0.1
 *
 * @param string $page
 * @return string
 */
 function fans( $page ) {
	
	$json = curl('https://graph.facebook.com/' . $page );	
	
	if( $json ) :
		$obj = json_decode( $json );
		return $obj->likes;
	else :
	 return 0; 
	endif;

}	
	
/**
 * Fetch the number of subscribers from Feedburner Awareness API
 *
 * @author Baki Goxhaj
 * @copyright MonsterThemes
 * @version 0.1
 *
 * @param string $website
 * @return string
 */
function subscribers( $website ) {
	
	$xml = curl('https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=' . $website );	
	
	if( $xml ) :
		$obj = simplexml_load_string( $xml );
		return $obj->feed->entry['circulation'];
	else :
	 return 0; 
	endif;

}

function related_posts( $tax = 'post_tag', $n = 3 ) {
	global $post;
	
	$terms = wp_get_post_terms( $post->ID, $tax );
	
	if( $terms ) : 
		foreach( $terms as $term ) $term_ids[] = $term->term_id;

		$args = array(
			//'tag__in' => array( implode( ',', $tag_ids ) ),
			'post__not_in' => array( $post->ID ),
			'posts_per_page' => $n,
			'ignore_sticky_posts' => 1,
			'tax_query' => array(
				array(
					'taxonomy' => $tax,
					'field' => 'id',
					'terms' => array( implode( ',', $term_ids ) )
					)
				)
			);

	$rel_posts = new WP_Query( $args );
	while( $rel_posts -> have_posts() ) : $rel_posts -> the_post(); ?> 	

				<div class="related">
					<a href="<?php the_permalink(); ?>" rel="lightbox" title="<?php the_title_attribute(); ?>">
						<?php the_post_thumbnail('folio'); ?>
					</a>
				
					<div class="reltent">
						<h2><?php the_title(); ?></h2>
						<p><?php shorten( 110, '', '' ); ?></p>
						<p><a class="more" href="<?php the_permalink(); ?>" >View Project</a></p>
					</div>
				</div><!-- related-post -->

	<?php endwhile; wp_reset_query();

endif; // if( $terms ) :
}
