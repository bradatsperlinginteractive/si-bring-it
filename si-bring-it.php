<?php
/**
 * @package si-bring-it
 * @version 1.1.1
 */
/*
Plugin Name: SI Bring It
Plugin URI: https://github.com/bradatsperlinginteractive/si-bring-it
Description: This is not just a plugin, it symbolizes the hope and enthusiasm of a single person.
Author: Bradford Knowlton
Version: 1.1.1
Author URI: http://bradknowlton.com/
*/

define('BRING_IT_COUNTER', 'si-bring-it-counter');

function bring_it_ring() {
		if ( get_option( BRING_IT_COUNTER ) !== false ) {
			
			$new_value = get_option( BRING_IT_COUNTER ) + 1;
			
		    // The option already exists, so we just update it.
		    update_option( BRING_IT_COUNTER, $new_value );
		
		} else {
		
		    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
		    $deprecated = null;
		    $autoload = 'yes';
		    add_option( BRING_IT_COUNTER, 1, $deprecated, $autoload );
		}
		
        return get_option( BRING_IT_COUNTER );
}

add_action( 'rest_api_init', function () {
        register_rest_route( 'si-bring-it/v1', '/ring/', array(
                'methods' => 'GET',
                'callback' => 'bring_it_ring'
        ) );
} );

// [bartag foo="foo-value"]
function bring_it_func( $atts ) {
	
	$counter_value = 1;
	if ( get_option( BRING_IT_COUNTER ) !== false ) {	
			$counter_value = get_option( BRING_IT_COUNTER );
		}
	
	return '<script type="text/javascript">
					jQuery(document).ready(function( $ ) {
						$("#button").click(function() {
							$(".doorbell-section").addClass("rung");
							$.ajax({
								type: "get",
								url: "'.rest_url('si-bring-it/v1/ring').'",
								success: function(data) {
									console.log(data);
									$(".count h1").html(data);
								}

							});
							var audio = $("#mysoundclip")[0];
							audio.play();
							$("#button").prop("onclick", null).off("click");
						});
					});

				</script><div class="doorbell-section columns">
							<div id="doorbell" class="column">
								<div id="button"></div>
								<audio id="mysoundclip" preload="auto"><source src="'.plugins_url( 'media/doorbell.mp3', __FILE__ ).'"></audio>
							</div>
							<div id="rings-container"><span class="count"><h1>'.$counter_value.'</h1></span>
								<h2>Rings of Support</h2>

								<div class="first-panel">
									<h3>Ring the Doorbell right now to show your support for affordable housing.</h3>
								</div>
								<div class="second-panel">
									<h3>Your ring has been heard loud and clear. Now get others to ring it too.</h3>
									<ul>
										<li>
											<a target="_blank" href="https://twitter.com/home?status=Ring%20the%20doorbell%20for%20affordable%20housing!%20http%3A//www.harborlightcp.org/"><img src="https://www.harborlightcp.org/wp-content/themes/twentyten/images/twitter.png" border="0" class="oppseal" width="45"></a>
										</li>
										<li>
											<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=http%3A//www.harborlightcp.org/doorbell-test/"><img src="https://www.harborlightcp.org/wp-content/themes/twentyten/images/facebook.png" border="0" class="oppseal" width="45"></a>
										</li>
										<li>
											<a href="mailto:?to=&amp;body=http%3A//www.harborlightcp.org/&amp;subject=Ring%20the%20doorbell%20for%20affordable%20housing!"><img src="https://www.harborlightcp.org/wp-content/themes/twentyten/images/mail.png" border="0" class="oppseal" width="45"></a>
										</li>
										<li><a class="stay-informed" target="_blank" href="http://www.giftworkslive.com/Capture/Capture/Form/MRLA-9PNU-UECG-AR6P">Stay Informed</a></li>
									</ul>
								</div>
							</div>
						</div>';
}
add_shortcode( 'bring_it', 'bring_it_func' );




add_action('init', 'si_bring_it_add_rewrite_rule', 11);

function si_bring_it_add_rewrite_rule() {
    add_rewrite_rule(
        '^api/ring.php$',
        'index.php?action=ring',
        'top'
    );
}

add_action( 'parse_query', 'si_bring_it_url_redirect' );
function si_bring_it_url_redirect( $wp_query ) {
	if( isset( $wp_query->query_vars['action'] ) && 'ring' == $wp_query->query_vars['action'] ){
        // output file here
        header('Content-Type: application/json');
        echo json_encode( array( 'id'=>1, 'hits'=> bring_it_ring() ) );
        die();
    }
}

function themeslug_query_vars( $qvars ) {
  $qvars[] = 'action';
  return $qvars;
}
add_filter( 'query_vars', 'themeslug_query_vars' );