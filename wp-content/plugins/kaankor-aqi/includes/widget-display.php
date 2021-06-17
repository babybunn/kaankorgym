<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function kaankoraqi_widget_template( $apidata ) {
    
    $aqi_data = json_decode($apidata);
    $last_update = $aqi_data->data->time->s;
    $last_update = date('d-m-y H:i', strtotime($last_update));

    $place = $aqi_data->data->city->name;
    $aqi = $aqi_data->data->aqi;
    ?>

    <div class="aqi-widget-wrapper">
        <div class="aqi-widget-inner">
            <?php 
                $aqi_level_class = '';
                $aqi_status = '';
                $aqi_img = '';
                $aqi_suggestion = '';

                if ( $aqi <= 50 ) {
                    $aqi_level_class = 'aqi-green';
                    $aqi_status      = 'Good';
                    $aqi_img         = 'good';
                    $aqi_suggestion  = 'Enjoy your outdoor activities!';
                } elseif ( $aqi <= 100 ) {
                    $aqi_level_class = 'aqi-yellow';
                    $aqi_status      = 'Moderate';
                    $aqi_img         = 'moderate';
                    $aqi_suggestion  = 'Turn on your humidifier';
                } elseif ( $aqi <= 150 ) {
                    $aqi_level_class = 'aqi-orange';
                    $aqi_status      = 'Poor';
                    $aqi_img         = 'sensitive';
                    $aqi_suggestion  = 'Turn on your humidifier';
                } elseif ( $aqi <= 200 ) {
                    $aqi_level_class = 'aqi-red';
                    $aqi_status      = 'Unhealthy';
                    $aqi_img         = 'unhealthy';
                    $aqi_suggestion  = 'Wear a mask outdoor';
                } elseif ( $aqi <= 300 ) {
                    $aqi_level_class = 'aqi-purple';
                    $aqi_status      = 'Very Unhealthy';
                    $aqi_img         = 'veryunhealthy';
                    $aqi_suggestion  = 'Stay inside!';
                } else {
                    $aqi_level_class = 'aqi-hazard';
                    $aqi_status      = 'Harzardous';
                    $aqi_img         = 'hazardous';
                    $aqi_suggestion  = 'Oh, no! air seems to be deadly';
                } 
            ?>
            <div class="aqi-content <?php echo $aqi_level_class ; ?>">
                <div class="aqi-content--left">
                    <div class="aqi-level <?php echo $aqi_level_class ; ?>">
                        <span class="aqi-status--text"><?php echo $aqi_status; ?></span>
                        <span class="aqi-value"><?php echo $aqi; ?></span>
                    </div>
                    <h3 class="aqi-level-title"><?php echo $aqi_suggestion; ?></h3>
                    <div class="aqi-location"><span class="aqi-location--text"><?php echo $place; ?></span></div>
                    <div class="aqi-update"><span class="aqi-update--text">Last update: <?php echo $last_update; ?></span></div>
                </div>
                <div class="aqi-content--right">
                    <div class="aqi-img">
                        <img src="<?php echo KAANKORAQI_DIR_PATH . 'img/' . $aqi_img  . '.png' ?>" alt="<?php echo $aqi_status; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}

function kaankoraqi_display_widget() {
    $options   = get_option('kaankoraqi_options');
    $latitude  = $options['latitude'];
    $longitude = $options['longitude'];
    $api_key   = $options['waqi_key'];
    
    // var_dump( $options );
    
    if ( empty( $api_key ) || $api_key == '' ) { ?>
        <div class="api-error">
            <p><?= __('No API key provides in KaanKor AQI Settings', 'kaankor-aqi') ?></p>
        </div>
    <?php }
    else {
        // $url = 'http://api.airvisual.com/v2/nearest_city?lat=' . $latitude . '&lon=' . $longitude . '&key=' . $api_key;
        $url = 'http://api.waqi.info/feed/geo:' . $latitude . ';' . $longitude . '/?token=' . $api_key;
        $response = wp_remote_get( $url );

        if ( !is_wp_error( $response ) ) {
            if ( $response['response']['code'] == 200 ) {
                $response_decoded = json_decode( $response['body'] );
                if ( $response_decoded->status == 'error' ) {
                    if ( $response_decoded->data == 'Invalid key' ) {
                        ?>
                        <div class="api-error">
                            <p><?= __( 'Please check your waqi.info api key. It seems that wrong api key was given', 'kaankor-aqi' ) ?></p>
                        </div>
                        <?php
                    }
                    else {
                        ?>
                        <div class="api-error">
                            <p><?= __( 'There was an error during connecting to the API', 'kaankor-aqi' ) ?>: <?= $response_decoded->data ?></p>
                        </div>
                        <?php
                    }
                } else {
                    // set_transient($transient_string, $api_data['body'], 900);
                    kaankoraqi_widget_template($response['body']);
                }
            }
        }
    }
}
