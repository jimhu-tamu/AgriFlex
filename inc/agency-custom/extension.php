<?php
/**
 * Contains the custom filters and actions for the
 * Extension agency type.
 *
 * @package AgriFlex
 */

$a = agriflex_agency();

if ( in_array( 'extension', $a['agencies'] ) ) {
  add_action( 'agriflex_before_header', 'agriflex_ext_logo', 20 );

  if ( $a['single'] ) {
    add_filter( 'agriflex_about', 'extension_about', 10, 1 );
    add_filter( 'footer_links', 'extension_links', 10, 1 );
  }
}

/**
 * Displays the extension logo when selected
 *
 * @author J. Aaron Eaton <aaron@channeleaton.com>
 * @since AgriFlex 2.0
 * @return void
 */
function agriflex_ext_logo() {

  $html = '<li class="top-agency tx-ext-item">';
  $html .= '<a href="http://agrilifeextension.tamu.edu/">';
  $html .= '<span class="top-level-hide">';
  $html .= 'Texas A&amp;M AgriLife Extension Service';
  $html .= '</span>';
  $html .= '<img src="' . get_bloginfo( 'stylesheet_directory') . '/images/extension-branding.png" alt="Texas A&amp;M Extension Logo" />';
  $html .= '</a>';
  $html .= '</li>';

  echo $html;

} // agriflex_ext_logo

/**
 * Replaces the default about to the Extension about
 *
 * @since AgriFlex 2.0
 * @author J. Aaron Eaton <aaron@channeleaton.com>
 * @param string $about The unfiltered, default about information
 * @return string $html The Extension about information
 */
function extension_about( $about ) {

  $html = '<h4>About</h4>';
  $html .= '<a href="http://www.youtube.com/watch?v=q_UsLHl_YDQ"><img src="' . get_bloginfo( 'template_directory' ) . '/images/about_video.jpg?v=100" alt="link to Extension about video" /></a>';
  $html .= '<p>A unique education agency, the Texas A&amp;M AgriLife Extension Service teaches Texans wherever they live, extending research-based knowledge to benefit their families and communities.</p>	';

  return $html;

} // extension_about

/**
 * Replaces the default links with Extension related links
 *
 * @since AgriFlex 2.0
 * @author J. Aaron Eaton <aaron@channeleaton.com>
 * @param string $links The unfiltered, default popular links
 * @return string $html The Extension popular links
 */
function extension_links( $links ) {

  $html = '<h4>Popular Links</h4>';
  $html .= '<a href="http://agrilifeextension.tamu.edu/"><img src="' . get_bloginfo( 'template_directory' ) . '/images/agrilife_ext_logo.png?v=100" alt="Texas A&amp;M AgriLife Extension" /></a>	';
  $html .= '<ul>';
  $html .= '<li><a href="http://county-tx.tamu.edu/">County Extension Offices</a></li>';
  $html .= '<li><a href="http://agrilife.tamu.edu/locations-window/#centers">Research and Extension Centers</a></li>';
  $html .= '      <li><a href="https://agrilifepeople.tamu.edu/">Contact Directory</a></li>';
  $html .= '<li><a href="http://agrilife.org/today/contact-us/">Media Contacts</a></li>					';
  $html .= '<li><a href="http://texas4-h.tamu.edu/">Texas 4-H and Youth Dev.</a></li>					';
  $html .= '<li><a href="http://agrilifeextension.tamu.edu/about/strategyimpact/index.php">Strategic Plan and Impacts</a></li>';
  $html .= '<li class="last"><a href="https://greatjobs.tamu.edu">Employment Opportunities</a></li>										';
  $html .= '</ul>';

  return $html;

} // extension_links
