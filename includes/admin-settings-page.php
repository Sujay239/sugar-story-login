<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <h2 class="nav-tab-wrapper">
        <a href="?page=sugar-story-login&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
        <a href="?page=sugar-story-login&tab=google" class="nav-tab <?php echo $active_tab == 'google' ? 'nav-tab-active' : ''; ?>">Google Login</a>
    </h2>
    
    <form action="options.php" method="post">
        <?php
        settings_fields( 'sugar_story_login_options' );
        do_settings_sections( 'sugar_story_login_options' );
        
        if ( $active_tab == 'general' ) {
            $primary_color = get_option('sugar_story_primary_color');
            if ( empty( $primary_color ) ) $primary_color = '#c3a669';

            $bg_color = get_option('sugar_story_bg_color');
            if ( empty( $bg_color ) ) $bg_color = '#FEFBF6';
            
            $image_1 = get_option('sugar_story_image_1');
            if ( empty( $image_1 ) ) $image_1 = 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&q=80';

            $image_2 = get_option('sugar_story_image_2');
            if ( empty( $image_2 ) ) $image_2 = 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=400&q=80';

            $image_3 = get_option('sugar_story_image_3');
            if ( empty( $image_3 ) ) $image_3 = 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=400&q=80';

            $image_4 = get_option('sugar_story_image_4');
            if ( empty( $image_4 ) ) $image_4 = 'https://images.unsplash.com/photo-1576618148400-f54bed99fcfd?w=400&q=80';
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Primary/Accent Color</th>
                    <td><input type="text" name="sugar_story_primary_color" value="<?php echo esc_attr($primary_color); ?>" class="sugar-color-picker" /></td>
                </tr>
                <tr>
                    <th scope="row">Background Color</th>
                    <td><input type="text" name="sugar_story_bg_color" value="<?php echo esc_attr($bg_color); ?>" class="sugar-color-picker" /></td>
                </tr>
                
                <tr>
                    <th scope="row" colspan="2"><hr style="margin-top: 20px; border: 0; border-top: 1px solid #ccc;"/><h2>Floating Images</h2></th>
                </tr>
                
                <?php for($i=1; $i<=4; $i++): 
                    $img_var = ${"image_$i"}; 
                ?>
                <tr>
                    <th scope="row">Floating Image <?php echo $i; ?></th>
                    <td>
                        <input type="hidden" name="sugar_story_image_<?php echo $i; ?>" id="sugar_story_image_<?php echo $i; ?>" value="<?php echo esc_attr($img_var); ?>" />
                        <div class="image-preview-wrapper" style="margin-bottom: 10px;">
                            <img id="image-preview-<?php echo $i; ?>" src="<?php echo esc_attr($img_var); ?>" style="max-width: 150px; max-height: 150px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" />
                        </div>
                        <input type="button" class="button button-secondary upload_image_button" data-target="sugar_story_image_<?php echo $i; ?>" value="Select Image <?php echo $i; ?>" />
                        <input type="button" class="button button-secondary clear_image_button" data-target="sugar_story_image_<?php echo $i; ?>" value="Clear" />
                    </td>
                </tr>
                <?php endfor; ?>
                
            </table>
            <?php 
        } else if ( $active_tab == 'google' ) {
            $google_client_id = get_option('sugar_story_google_client_id', '');
            $google_client_secret = get_option('sugar_story_google_client_secret', '');
            $redirect_uri = site_url( '?sugar_story_google_auth=callback' );
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Authorized Redirect URI</th>
                    <td>
                        <input type="text" value="<?php echo esc_attr($redirect_uri); ?>" class="regular-text" style="width: 400px; background-color: #f0f0f1;" readonly onclick="this.select();" />
                        <p class="description">Copy this exact URL and paste it into the "Authorized redirect URIs" field in your Google Cloud Console.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Google Client ID</th>
                    <td>
                        <input type="text" name="sugar_story_google_client_id" value="<?php echo esc_attr($google_client_id); ?>" class="regular-text" style="width: 400px;" />
                        <p class="description">Your OAuth Client ID from Google Cloud Console.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Google Client Secret</th>
                    <td>
                        <input type="password" name="sugar_story_google_client_secret" value="<?php echo esc_attr($google_client_secret); ?>" class="regular-text" style="width: 400px;" />
                        <p class="description">Your OAuth Client Secret from Google Cloud Console.</p>
                    </td>
                </tr>
            </table>
            <?php
        }
        submit_button(); 
        ?>
    </form>
</div>
