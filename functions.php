<?php

/**
 * Content Width
 * 
 * Using this feature you can set the maximum allowed width for any content 
 * in the theme, like oEmbeds and images added to posts. 
 */
if ( ! isset( $content_width ) )
	$content_width = 620;

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
 * Feed Links
 */
add_theme_support( 'automatic-feed-links' );

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
		wp_enqueue_style('comment-style', get_template_directory_uri() . '/css/comments.css');

	//wp_enqueue_script('jquery');
	//wp_enqueue_script('cform', get_template_directory_uri() . '/js/cform.js', array('jquery'), NULL );
	//wp_enqueue_script('custom', get_template_directory_uri() . '/js/custom.js', array('jquery'));

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
* Is Ajax?
* Check if it is an ajax request
*/
function is_ajax() {
  return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
}

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

/**
 * Get First Paragraph
 *
 * @return string
 */
function get_first_paragraph( $str ) {
	$str = substr( $str, 0, strpos( $str, "</p>") + 4 );
	$str = str_replace( "<p>", "", str_replace( "<p/>", "", $str ) );
	return $str;
}

/**
 * The Breadcrumb
 * Adds a simple but highly customizable breadcrumb to your WordPress website
 * @author: Baki Goxhaj of WPlancer.Com 
 */
function the_breadcrumb( $sep = ' / ' ) {
		$out = '<a href="'. home_url() .'">Home</a>';
		if( is_category() ) 
			$out .= $sep . single_cat_title( '', false );
		if( is_single() )
			$out .= $sep . get_the_category_list( $sep, 'multiple') . $sep . single_post_title( '', false );
		if( is_page() )
			$out .= $sep . single_post_title( '', false );
		if( is_singular('event') ) 
			$out .= $sep . '<a href="' . get_permalink(371) . '">Events</a>' . $sep . single_post_title( '', false ); 
		if( is_search() )
			$out .= $sep . 'Search results for "' . get_search_query() . '"';
		if( is_author() )
			$out .= $sep . 'All posts by ' . get_the_author_meta( 'display_name', get_query_var('author') );
			
		echo $out;
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
 * The pagination function
 */
function the_pagination(){

	global $wp_query;
	
	$big = 999999999; // need an unlikely integer

	$nav = paginate_links( array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => '?paged=%#%',
		'current' => max( 1, get_query_var('paged') ),
		'total' => $wp_query->max_num_pages
	) );
	
	echo '<div class="pagination">', $nav, '</div>';
}

/**
 * Update Contact Fields
 */
function base_contact_methods( $contactmethods ) {
 
    // Remove we what we don't want
    unset( $contactmethods['aim'] );
    unset( $contactmethods['yim'] );
    unset( $contactmethods['jabber'] );
 
    // Add some useful ones
    $contactmethods['twitter'] = 'Twitter Username';
    $contactmethods['facebook'] = 'Facebook Profile URL';
    $contactmethods['linkedin'] = 'LinkedIn Public Profile URL';
    $contactmethods['googleplus'] = 'Google+ Profile URL';
 
    return $contactmethods;
}
add_filter( 'user_contactmethods', 'base_contact_methods' );


if ( ! function_exists( 'base_comments' ) ) :

function base_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'base' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'base' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 68;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 39;

						echo get_avatar( $comment, $avatar_size );

						/* translators: 1: comment author, 2: date and time */
						printf( __( '%1$s on %2$s <span class="says">said:</span>', 'base' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s at %2$s', 'base' ), get_comment_date(), get_comment_time() )
							)
						);
					?>

					<?php edit_comment_link( __( 'Edit', 'base' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'base' ); ?></em>
					<br />
				<?php endif; ?>

			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'base' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for base_comments()



