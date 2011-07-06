<?php
/**
 * agriflex functions and definitions
 *
 * @package WordPress
 * @subpackage agriflex
 * @since agriflex 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;

/** Tell WordPress to run agriflex_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'agriflex_setup' );

if ( ! function_exists( 'agriflex_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override agriflex_setup() in a child theme, add your own agriflex_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since agriflex 1.0
 */
function agriflex_setup() {

	add_action( 'wp_print_styles', 'add_ie_style_sheet', 200 );
	function add_ie_style_sheet() {
	    wp_enqueue_style( 'ie7', get_bloginfo('stylesheet_directory') . '/css/ie.css', array(), '1.0' );
	}
 
	add_filter( 'style_loader_tag', 'make_ie_style_sheet_conditional', 10, 2 );
	/**
	 * Add conditional comments around IE style sheet.
	 *
	 * @param string $tag Existing style sheet tag
	 * @param string $handle Name of the enqueued style sheet
	 * @return string Amended markup
	 */
	function make_ie_style_sheet_conditional( $tag, $handle ) {
	    if ( 'ie7' == $handle )
	        $tag = '<!--[if lte IE 7]>' . "\n" . $tag . '<![endif]-->' . "\n";
	    return $tag;
	}

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );
	// Add new image sizes
	add_image_size('featured',960,9999);
	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'agriflex', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'agriflex' ),
	) );

	/* -- Add typekit js and css to document head -- */
	add_action('wp_head','typekit_js');
		function typekit_js() { 
			if( !is_admin() ) : ?>
	<script type="text/javascript" src="http://use.typekit.com/thu0wyf.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>	
	<style type="text/css">
	  .wf-loading h1#site-title,
	  .wf-loading .entry-title {
	    /* Hide the blog title and post titles while web fonts are loading */
	    visibility: hidden;
	  }
	</style>				
	<?php
	endif; 
	}	

	// load Slideshow scripts
	function load_js() {
	        // instruction to only load if it is not the admin area
		if ( !is_admin() ) {
			 
		// deregister swfobject js							
		wp_deregister_script('swfobject');
		
		// deregister l10n js			
		wp_deregister_script( 'l10n' );	
			
		// register jquery CDN				
		wp_deregister_script('jquery');
		wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"), false);		
	   	wp_enqueue_script('jquery');
					
		// register script location with wp_register_script	
	   	wp_register_script('my_scripts',
	       	get_bloginfo('template_directory') . '/js/my_scripts.js');	
	       // enqueue the custom jquery js
	   	wp_enqueue_script('my_scripts');	       
		}	         
	}    
	add_action('init', 'load_js');	


	// Disable some widgets so people don't go apeshit
	function remove_some_wp_widgets(){
	  unregister_widget('WP_Widget_Calendar');
	  unregister_widget('WP_Widget_Search');
	  unregister_widget('WP_Widget_Tag_Cloud');
	}

	add_action('widgets_init',remove_some_wp_widgets, 1);	


	// Custom admin styles
	function admin_register_head() {
	    $siteurl = get_option('siteurl');
	    $url = $siteurl . '/wp-content/themes/' . basename(dirname(__FILE__)) . '/css/admin.css';
	    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
	}
	add_action('admin_head', 'admin_register_head');
	
	// Custom Body Classes Based On Agency Selected
	function my_class_names($classes) {
		$classes[] = '';
		
		if (class_exists("AgrilifeCustomizer")) {
			$options = get_option('AgrilifeOptions');

			// Set Header Tabs
			if($options['isResearch']) $classes[] .= 'research';
			if($options['isExtension']) $classes[] .= 'extension';
			if($options['isCollege']) $classes[] .= 'college';
			if($options['isTvmdl']) $classes[] .= 'tvmdl';
			
			// Single Agency Classes
			if($options['isExtension'] && !$options['isResearch'] && !$options['isCollege'] && !$options['isTvmdl']) $classes[] .= 'extensiononly';
			if($options['isResearch'] && !$options['isExtension'] && !$options['isCollege'] && !$options['isTvmdl']) $classes[] .= 'researchonly';
			if($options['isCollege'] && !$options['isExtension'] && !$options['isResearch'] && !$options['isTvmdl']) $classes[] .= 'collegeonly';
			if($options['isTvmdl'] && !$options['isExtension'] && !$options['isResearch'] && !$options['isCollege']) $classes[] .= 'tvmdlonly';
		}	
		return $classes;
	}

	add_filter('body_class','my_class_names');
	
	
}	
endif;


/**
 * Makes some changes to the <title> tag, by filtering the output of wp_title().
 *
 * If we have a site description and we're viewing the home page or a blog posts
 * page (when using a static front page), then we will add the site description.
 *
 * If we're viewing a search result, then we're going to recreate the title entirely.
 * We're going to add page numbers to all titles as well, to the middle of a search
 * result title and the end of all other titles.
 *
 * The site title also gets added to all titles.
 *
 * @since agriflex 1.0
 *
 * @param string $title Title generated by wp_title()
 * @param string $separator The separator passed to wp_title(). Twenty Ten uses a
 * 	vertical bar, "|", as a separator in header.php.
 * @return string The new title, ready for the <title> tag.
 */
function agriflex_filter_wp_title( $title, $separator ) {
	// Don't affect wp_title() calls in feeds.
	if ( is_feed() )
		return $title;

	// The $paged global variable contains the page number of a listing of posts.
	// The $page global variable contains the page number of a single post that is paged.
	// We'll display whichever one applies, if we're not looking at the first page.
	global $paged, $page;

	if ( is_search() ) {
		// If we're a search, let's start over:
		$title = sprintf( __( 'Search results for %s', 'agriflex' ), '"' . get_search_query() . '"' );
		// Add a page number if we're on page 2 or more:
		if ( $paged >= 2 )
			$title .= " $separator " . sprintf( __( 'Page %s', 'agriflex' ), $paged );
		// Add the site name to the end:
		$title .= " $separator " . get_bloginfo( 'name', 'display' );
		// We're done. Let's send the new title back to wp_title():
		return $title;
	}

	// Otherwise, let's start by adding the site name to the end:
	$title .= get_bloginfo( 'name', 'display' );

	// If we have a site description and we're on the home/front page, add the description:
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $separator " . $site_description;

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $separator " . sprintf( __( 'Page %s', 'agriflex' ), max( $paged, $page ) );

	// Return the new title to wp_title():
	return $title;
}
add_filter( 'wp_title', 'agriflex_filter_wp_title', 10, 2 );


/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 * @since agriflex 1.0
 */
function agriflex_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'agriflex_page_menu_args' );


function agriflex_nav_menu_args( $args = 'sf-menu' )
{
	$args['menu_class'] = 'sf-menu menu';
	return $args;
} // function

add_filter( 'wp_nav_menu_args', 'agriflex_nav_menu_args' );


/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since agriflex 1.0
 * @return int
 */
function agriflex_excerpt_length( $length ) {
	return 88;
}
add_filter( 'excerpt_length', 'agriflex_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since agriflex 1.0
 * @return string "Continue Reading" link
 */
function agriflex_continue_reading_link() {
	return ' <span class="read-more"><a href="'. get_permalink() . '">' . __( 'Read Article &rarr;', 'agriflex' ) . '</a></span>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and agriflex_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since agriflex 1.0
 * @return string An ellipsis
 */
function agriflex_auto_excerpt_more( $more ) {
	return '...' . agriflex_continue_reading_link();
}
add_filter( 'excerpt_more', 'agriflex_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since agriflex 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function agriflex_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= agriflex_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'agriflex_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in Twenty Ten's style.css.
 *
 * @since agriflex 1.0
 * @return string The gallery style filter, with the styles themselves removed.
 */
function agriflex_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'agriflex_remove_gallery_css' );

if ( ! function_exists( 'agriflex_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own agriflex_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since agriflex 1.0
 */
function agriflex_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'agriflex' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your comment is awaiting moderation.', 'agriflex' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'agriflex' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'agriflex' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'agriflex' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'agriflex'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

// Custom search 
add_filter('get_search_form', 'custom_search_form');
function custom_search_form() {

	$search_text = get_search_query() ? esc_attr( apply_filters( 'the_search_query', get_search_query() ) ) : apply_filters('agriflex_search_text', esc_attr__('Search', 'agriflex'));
	$button_text = apply_filters( 'agriflex_search_button_text', esc_attr__( 'Go', 'agriflex' ) );

	$onfocus = " onfocus=\"if (this.value == '$search_text') {this.value = '';}\"";
	$onblur = " onblur=\"if (this.value == '') {this.value = '$search_text';}\"";

	$form = '
		<form method="get" class="searchform" action="' . get_option('home') . '/" >
			<input type="text" value="'. $search_text .'" name="s" class="s"'. $onfocus . $onblur .' />
			<input type="submit" class="searchsubmit" value="'. $button_text .'" />
		</form>
	';

	return apply_filters('custom_search_form', $form, $search_text, $button_text);
}

/**
 * Register widgetized areas, including two sidebars and four widget-ready areas in the sidebar.
 *
 * To override agriflex_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @uses register_sidebar
 */
function agriflex_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Right Column', 'agriflex' ),
		'id' => 'right-column-widget-area',
		'description' => __( 'The right column area', 'agriflex' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2, located in the second sidebar.
	register_sidebar( array(
		'name' => __( 'Right Column Bottom', 'agriflex' ),
		'id' => 'right-column-bottom-widget-area',
		'description' => __( 'The right column bottom widget area', 'agriflex' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 3
	register_sidebar( array(
		'name' => __( 'Home Page Bottom', 'agriflex' ),
		'id' => 'home-middle-1',
		'description' => __( 'Home Middle #1', 'agriflex' ),
		'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );	
	
	// Area 4, located in the sidebar.
	register_sidebar( array(
		'name' => __( 'Sidebar Navigation', 'agriflex' ),
		'id' => 'sidebar-widget-navigation',
		'description' => __( 'Sidebar Navigation', 'agriflex' ),
		'before_title' => '<h3 class="widget-title"><a>',
		'after_title' => '</a></h3>',
	) );	
}

/** Register sidebars by running agriflex_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'agriflex_widgets_init' );








/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * To override this in a child theme, remove the filter and optionally add your own
 * function tied to the widgets_init action hook.
 *
 */
function agriflex_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'agriflex_remove_recent_comments_style' );

if ( ! function_exists( 'agriflex_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post—date/time and author.
 *
 * @since agriflex 1.0
 */
function agriflex_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'agriflex' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'agriflex' ), get_the_author() ),
			get_the_author()
		)
	);
}
endif;

if ( ! function_exists( 'agriflex_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 */
function agriflex_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'agriflex' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'agriflex' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'agriflex' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;







// add asynchronous google analytics code
add_action('wp_head','analytics_code',0);
	function analytics_code() { 
		if( !is_admin() ) : ?>
<script type="text/javascript">//<![CDATA[
// Google Analytics asynchronous
var _gaq = _gaq || [];
_gaq.push(['_setAccount','UA-7414081-1']); 	//county-co
_gaq.push(['_trackPageview'],['_trackPageLoadTime']);
<?php 
if (class_exists("AgriLifeCounties")) {
  $agrilifeOptions	= get_option('AgrilifeCountyOptions');
  if($agrilifeOptions['googleAnalytics']<>''){
    echo "_gaq.push(['_setAccount','".$agrilifeOptions['googleAnalytics']."']);	//local-co\n";
    echo "_gaq.push(['_trackPageview'],['_trackPageLoadTime']);";
  }
}
?> 
(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
//]]>
</script>
<?php
endif; 
}	

//Function: Get flickr media and display based on user id
function getFlickrPhotos($id, $limit=9) {
    require_once("includes/phpFlickr.php");
    $f = new phpFlickr("c15fd416e1273128b7c85bb58fa01dc7");
    $photos = $f->people_getPublicPhotos($id, NULL, NULL, 12);
    $return.='<ul class="flickrPhotos">';
    foreach ($photos['photos']['photo'] as $photo) {
        $return.='<li><a href="' . $f->buildPhotoURL($photo, 'medium') . '" title="' . $photo['title'] . '"><img src="' . $f->buildPhotoURL($photo, 'square') . '" alt="' . $photo['title'] . '" title="' . $photo['title'] . '" /></a></li>';
    }
    echo $return.='</ul>';
} 


// Set path to function files
$includes_path = TEMPLATEPATH . '/includes/';


// Admin Pages
require_once ($includes_path . 'admin.php');
// Remove Admin Menus and Dashboards
require_once ($includes_path . 'admin-remove.php');
// Custom Shortcodes
require_once ($includes_path . 'shortcodes.php');
// Auto-configure plugins
require_once ($includes_path . 'plugin-config.php');
// Add Custom Widgets
require_once ($includes_path . 'widgets.php');









