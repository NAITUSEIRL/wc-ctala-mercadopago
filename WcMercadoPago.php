<?php
/*
  Plugin Name: Pagos con Mercado Pago
  Plugin URI:  https://github.com/NAITUSEIRL/wc-ctala-mercadopago
  Description: Utiliza Mercado Pagos para Woocommerce
  Version:     0.1
  Author:      Cristian Tala Sánchez
  Author URI:  http://www.cristiantala.cl
  License:     MIT
  License URI: http://opensource.org/licenses/MIT
  Domain Path: /languages
  Text Domain: ctala-text_domain
 */
//include_once 'helpers/debug.php';
require_once( 'vendor/autoload.php' );
require_once ('classes/WOOMPApi.php');

define("CTALA_MP_CLIENTID", "3456644172902315");
define("CTALA_MP_CLIENTSCRETET", "eZHN1ladm87NneOypJqp91iZqesN82nt");

function theme_slug_filter_the_content($content) {
    $custom_content = probandoMP();
    $custom_content .= $content;
    return $custom_content;
}

add_filter('the_content', 'theme_slug_filter_the_content');

function probandoMP() {
    $sessionid = "1234567890";

    $payer = array(
        "name" => "Cristian ",
        "surname" => "Tala S.",
        "email" => "naito.neko@gmail.com",
        "date_created" => "",
        "phone" => array(
            "area_code" => "-",
            "number" => "+56991629602"
        ),
        "address" => array(
            "zip_code" => "00000",
            "street_name" => "Presidente Kennedy",
            "street_number" => "5933"
        ),
        "identification" => array(
            "number" => "null",
            "type" => "null"
        )
    );

    $shipments = array(
        "receiver_address" => array(
            "floor" => "-",
            "zip_code" => "000000",
            "street_name" => "DESTINO",
            "apartment" => "-",
            "street_number" => "-"
        )
    );

    $items = array(
        array(
            "id" => $sessionid,
            "title" => "Producto",
            "description" => "x",
            "quantity" => 1,
            "unit_price" => 10, //decimal
            "currency_id" => "CLP", // string Argentina: ARS (peso argentino) � USD (D�lar estadounidense); Brasil: BRL (Real).,
            "picture_url" => "",
            "category_id" => ""
        )
    );

    //set back url
    $back_urls = array(
        "pending" => "URLPENDING", // string
        "success" => "URLSUCCESS"  // string
    );

    //mount array pref
    $pref = array();
    $pref['external_reference'] = $sessionid;
    $pref['payer'] = $payer;
    $pref['shipments'] = $shipments;
    $pref['items'] = $items;
    $pref['back_urls'] = $back_urls;
    $pref['payment_methods'] = $payment_methods;

    $mp = new MP(CTALA_MP_CLIENTID, CTALA_MP_CLIENTSCRETET);
    $preferenceResult = $mp->create_preference($pref);


    $result = "";
    $result.="<pre>";

    if ($preferenceResult['status'] == 201):
        if (true):
            $link = $preferenceResult['response']['sandbox_init_point'];
        else:
            $link = $preferenceResult['response']['init_point'];
        endif;
    else:
        $result.= "Error: " . $preferenceResult['status'];
    endif;

    $result.=print_r($preferenceResult, true);

    $filters = array(
        "id" => null,
        "site_id" => null,
        "external_reference" => null
    );

    $searchResult = $mp->search_payment($filters);

    $result.= print_r($searchResult, true);
    $result.= "</pre>";
    return $result;
}

//add_action('current_screen', 'probandoMP');
// Registramos los menus correspondientes


function ctala_setup_admin_menu() {
    add_menu_page('CTala', 'CTala', 'manage_options', 'ctala', 'ctala_view_admin');
    add_submenu_page('ctala', 'SubMen', 'Admin Page', 'manage_options', 'myplugin-top-level-admin-menu', 'myplugin_admin_page');
}

function ctala_view_admin() {
    include_once 'views/admin/viewAdmin.php';
}

add_action('admin_menu', 'ctala_setup_admin_menu');

// Creating the widget 
class wpb_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
// Base ID of your widget
                'wpb_widget',
// Widget name will appear in UI
                __('WPBeginner CTALA Widget', 'wpb_widget_domain'),
// Widget description
                array('description' => __('Sample widget based on WPBeginner Tutorial', 'wpb_widget_domain'),)
        );
    }

// Creating widget front-end
// This is where the action happens
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
// before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];

// This is where you run the code and display the output
        probandoMP();
        echo $args['after_widget'];
    }

// Widget Backend 
    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('New title', 'wpb_widget_domain');
        }
// Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

// Updating widget replacing old instances with new
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }

}

// Class wpb_widget ends here
// Register and load the widget
function wpb_load_widget() {
    register_widget('wpb_widget');
}

add_action('widgets_init', 'wpb_load_widget');
?>
