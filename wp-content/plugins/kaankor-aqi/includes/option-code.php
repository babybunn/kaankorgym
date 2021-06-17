<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function kaankoraqi_options_code() {
	$options_values = get_option('kaankoraqi_options');
    
    if( $_POST['action'] == 'save' ){
        if( ! empty( $_POST['latitude'] ) && ! empty( $_POST['longitude'] ) ) {
            $options_values_old = get_option('kaankoraqi_options');
            $options_values['longitude'] = $_POST['longitude'];
            $options_values['latitude']  = $_POST['latitude'];
            $options_values['waqi_key']  = $_POST['waqi-key'];
            update_option('kaankoraqi_options', $options_values);
        }
    }
?>
    <div class="wrap">
        <h2><?= __('AQI Settings', 'kaankor-aqi') ?></h2>
        <form method="post">
            <input type="hidden" name="action" value="save">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?=__('Latitude', 'kaankor-aqi')?></th>
                        <td><input type="text" name="latitude" value="<?=esc_attr($options_values['latitude'])?>"></td>
                    </tr>
                    <tr>
                        <th><?=__('Longitude', 'kaankor-aqi')?></th>
                        <td><input type="text" name="longitude" value="<?=esc_attr($options_values['longitude'])?>"></td>
                    </tr>
                    <tr>
                        <th><?=__('WAQI Info API KEY', 'kaankor-aqi')?></th>
                        <td><input type="text" name="waqi-key" value="<?=esc_attr($options_values['waqi_key'])?>"></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <input type="submit" name="Submit" class="button-primary" value="<?=__('Save Settings', 'kaankor-aqi')?>">
        </form>
    </div>

<?php
}