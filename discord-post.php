<?php
/*
 * Plugin Name: discord-post
 * Plugin URI: https://swheeler.co
 * Description: posts content from Wordpress to Discord
 * Version: 0.1
 * Author: Scott Wheeler
 * Author URI: https://swheeler.co
*/

function send_post_to_discord($id, $post) {
  if(get_option('discord_url') != null) {
    $WebhookURL = get_option('discord_url');

    $author = $post->post_author;
    $name = get_the_author_meta('display_name', $author);
    $title = $post->post_title;
    $permalink = get_permalink($id);

    $message = $name . " has just posted " . $title . "! Go check it out: " . $permalink;

    $data = array("content" => $message);

    $curl = curl_init($WebhookURL);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_exec($curl);
  }
}

add_action('publish_post', 'send_post_to_discord', 10, 2);

function discord_setting_section_update_callback() {
  echo "<p>Place discord webhook URL below.";
}

function discord_setting_update_callback() {

  echo '<input name="discord_url" id="discord_url" type="text" value="' . get_option('discord_url') . '">';
}

function discord_settings_api_init() {
 add_settings_section(
   'discord_url',
   'Post to Discord',
   'discord_setting_section_update_callback',
   'general'
 );

 add_settings_field(
   'discord_url',
   'Discord URL',
   'discord_setting_update_callback',
   'general',
   'discord_url'
 );

 register_setting( 'general', 'discord_url' );
}

add_action( 'admin_init', 'discord_settings_api_init' );
