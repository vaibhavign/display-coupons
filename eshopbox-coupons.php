<?php
/*
Plugin Name: Eshopbox display coupons
Plugin URI: http://www.eshopbox.com
Description: You can add extra fee for any payment gateways
Version: 0.9
Author: vaibhav sharma
Author URI: http://www.eshopbox.com
*/

/**
 * Copyright (c) `date "+%Y"` Vaibhav Sharma. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class eshopAddcoupons{
    public function __construct(){

        add_action( 'woocommerce_after_cart_table', array($this, 'add_coupons_template'));
        add_action( 'woocommerce_after_my_account', array($this, 'add_coupons_template'));
               
        
    }

  function add_coupons_template(){
      global $woocommerce;
      
       global $wpdb;
    $args = func_get_args();
    $customer_id = get_current_user_id();
    if($customer_id>0){
        $current_user = wp_get_current_user();

$euserEmail = $current_user->data->user_email;
$userDisplayName = $current_user->data->display_name;
$userMobile = $current_user->data->contactno;
$querystr = " SELECT wp_postmeta.*,wp_posts.* FROM wp_postmeta left join wp_posts on wp_posts.ID = wp_postmeta.post_id WHERE meta_key= 'customer_email' and meta_value like '%".$euserEmail."%' and wp_posts.post_type='shop_coupon' 
    ";

 $pageposts = $wpdb->get_results($querystr, OBJECT);  
 foreach($pageposts as $key=>$val){
   // echo '<pre>'; 
   // print_r($val); 
    $querystrc = " SELECT wp_postmeta.*  FROM wp_postmeta where  post_id = ".$val->post_id;
  
     $coupposts = $wpdb->get_results($querystrc, OBJECT);
    // $coup[]= $coupposts[0]->meta_value;
   //  echo '<pre>';
   //  print_r($coupposts); 
     foreach($coupposts as $key1=>$val1){
        $totArray[$val->post_title][$val1->meta_key] = $val1->meta_value; 
     }
      $totArray[$val->post_title]['pcontent'] = $val->post_content; 
 //  $totArray[$val->post_title] = $coupposts; 
     
    }
// return $totArray;
     //   shop_coupon
       // echo 'eureka';
    }
    $ax = false;
    foreach($totArray as $key=>$val){
        if($val['usage_count']< $val['usage_limit']){
            $ax = true;
        }
    }  
    if($ax){
        $displ = true;
    $ab =  '<div><div style="float:left;"><div style="float:left;">Coupon Code</div><div style="float:left;">Expiry Date</div></div>
    <div style="clear:both;">';
      foreach($totArray as $key=>$val){
          
          
          if($val['usage_count']> $val['usage_limit']){
              $displ = false;
          }
          
          if($val['expiry_date']==''){
                $expDate = 'N/A';
                $displ = true;
            } else {
                    
                $expDate = $val['expiry_date'];
                $expl = explode('-',$expDate);
                $dates = mktime(0,0,0,$expl[1],$expl[2],$expl[0]);
                if(time()>$dates){
                    $displ = false;
                }
            }
        if($displ){
           $ab .= '<div style="clear:both;"><div style="float:left;">'.$key.'</div><div style="float:left;">'.$val['expiry_date'].'</div></div>' ; 
        }
      }  
    $ab .='<div>
    </div>';
    
    echo $ab;
    
    
    }  
      
  }  
    





/**
     * Get the plugin url.
     *
     * @access public
     * @return string
     */
    public function plugin_url() {
        if ( $this->plugin_url ) return $this->plugin_url;
        return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
    }


    /**
     * Get the plugin path.
     *
     * @access public
     * @return string
     */
    public function plugin_path() {
        if ( $this->plugin_path ) return $this->plugin_path;

        return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

}
new eshopAddcoupons();
