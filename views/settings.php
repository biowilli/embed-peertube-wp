<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="wrap">
<h2>Embed Peertube settings</h2>

<form method="post" action="">
    <?php wp_nonce_field( 'pl_peertube_settings' ); ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Peertube instance url:</th>
            <td><input type="text" name="pl_peertube_url" value="<?php echo esc_attr( get_option('pl_peertube_url') ); ?>" required />
            </td>
        </tr>
    </table>
    <h3>Playbutton</h3>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Playbutton Grid:</th>
            <td>
                <select name="pl_playbutton_style_grid">
                    <option value="playbutton_black_grid" <?php selected(get_option('pl_playbutton_style_grid'), 'playbutton_black_grid'); ?>>Black</option>
                    <option value="playbutton_white_grid" <?php selected(get_option('pl_playbutton_style_grid'), 'playbutton_white_grid'); ?>>White</option>
                    <option value="playbutton_fs1_grid" <?php selected(get_option('pl_playbutton_style_grid'), 'playbutton_fs1_grid'); ?>>FS1</option>
                    <option value="playbutton_fs1_2_grid" <?php selected(get_option('pl_playbutton_style_grid'), 'playbutton_fs1_2_grid'); ?>>FS1_2</option>
                </select>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Playbutton Playlist:</th>
            <td>
                <select name="pl_playbutton_style_playlist">
                    <option value="playbutton_black_playlist" <?php selected(get_option('pl_playbutton_style_playlist'), 'playbutton_black_playlist'); ?>>Black</option>
                    <option value="playbutton_white_playlist" <?php selected(get_option('pl_playbutton_style_playlist'), 'playbutton_white_playlist'); ?>>White</option>
                    <option value="playbutton_fs1_playlist" <?php selected(get_option('pl_playbutton_style_playlist'), 'playbutton_fs1_playlist'); ?>>FS1</option>
                    <option value="playbutton_fs1_2_playlist" <?php selected(get_option('pl_playbutton_style_playlist'), 'playbutton_fs1_2_playlist'); ?>>FS1_2</option>
                </select>
            </td>
        </tr>
    </table>
    <h3>Grid-Color</h3>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Grid-Background-Color:</th>
            <td>
                <input type="color" name="pl_grid_backgroundcolor" value="<?php echo esc_attr( get_option('pl_grid_backgroundcolor') ); ?>" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Grid-Text-Color:</th>
            <td>
                <input type="color" name="pl_grid_textcolor" value="<?php echo esc_attr( get_option('pl_grid_textcolor') ); ?>" />
            </td>
        </tr>
    </table>
    <table class="form-table">
        <tr valign="top">
                <th scope="row">Grid-Size-Header:</th>
                <td>
                    <input type="text" name="pl_grid_textsize_header" value="<?php echo esc_attr( get_option('pl_grid_textsize_header') ); ?>" />
                    <span>px</span> 
                </td>
            </tr>
        <tr valign="top">
            <th scope="row">Grid-Size-Description:</th>
            <td>
                <input type="text" name="pl_grid_textsize_description" value="<?php echo esc_attr( get_option('pl_grid_textsize_description') ); ?>" />
                <span>px</span> 
            </td>
        </tr>
    </table>
    <h3>Grid-Margin</h3>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Grid-Margin-top:</th>
            <td>
                <input type="text" name="pl_grid_margin_top" value="<?php echo esc_attr( get_option('pl_grid_margin_top') ); ?>" />
                <span>px</span> 
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Grid-Margin-bottom:</th>
            <td>
                <input type="text" name="pl_grid_margin_bottom" value="<?php echo esc_attr( get_option('pl_grid_margin_bottom') ); ?>" />
                <span>px</span> 
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Grid-Margin-right:</th>
            <td>
                <input type="text" name="pl_grid_margin_right" value="<?php echo esc_attr( get_option('pl_grid_margin_right') ); ?>" />
                <span>px</span> 
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Grid-Margin-left:</th>
            <td>
                <input type="text" name="pl_grid_margin_left" value="<?php echo esc_attr( get_option('pl_grid_margin_left') ); ?>" />
                <span>px</span> 
            </td>
        </tr>

    </table>
<h3>Grid-Border-Radius</h3>
    <table class="form-table">
    <tr valign="top">
        <th scope="row">Grid-Border-radius (Top-left):</th>
        <td>
            <input type="text" name="pl_grid_borderradius_top_left" value="<?php echo esc_attr( get_option('pl_grid_borderradius_top_left') ); ?>" />
            <span>px</span> 
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">Grid-Border-radius (Top-right):</th>
        <td>
            <input type="text" name="pl_grid_borderradius_top_right" value="<?php echo esc_attr( get_option('pl_grid_borderradius_top_right') ); ?>" />
            <span>px</span> 
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">Grid-Border-radius (Bottom-left):</th>
        <td>
            <input type="text" name="pl_grid_borderradius_bottom_left" value="<?php echo esc_attr( get_option('pl_grid_borderradius_bottom_left') ); ?>" />
            <span>px</span> 
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">Grid-Border-radius (Bottom-right):</th>
        <td>
            <input type="text" name="pl_grid_borderradius_bottom_right" value="<?php echo esc_attr( get_option('pl_grid_borderradius_bottom_right') ); ?>" />
            <span>px</span> 
        </td>
    </tr>
</table>
<h3>Hover-Grid-Border-Radius</h3>
    <table class="form-table">
    <tr valign="top">
    <th scope="row">Hover-Grid-Border-radius (Top-left):</th>
        <td>
            <input type="text" name="pl_hover_grid_borderradius_top_left" value="<?php echo esc_attr( get_option('pl_hover_grid_borderradius_top_left') ); ?>" />
            <span>px</span> 
        </td>
    </tr>
    <tr valign="top">
    <th scope="row">Hover-Grid-Border-radius (Top-right):</th>
        <td>
            <input type="text" name="pl_hover_grid_borderradius_top_right" value="<?php echo esc_attr( get_option('pl_hover_grid_borderradius_top_right') ); ?>" />
            <span>px</span> 
        </td>
    </tr>
    <tr valign="top">
    <th scope="row">Hover-Grid-Border-radius (Bottom-left):</th>
        <td>
            <input type="text" name="pl_hover_grid_borderradius_bottom_left" value="<?php echo esc_attr( get_option('pl_hover_grid_borderradius_bottom_left') ); ?>" />
            <span>px</span> 
        </td>
    </tr>
    <tr valign="top">
    <th scope="row">Hover-Grid-Border-radius (Bottom-right):</th>
        <td>
            <input type="text" name="pl_hover_grid_borderradius_bottom_right" value="<?php echo esc_attr( get_option('pl_hover_grid_borderradius_bottom_right') ); ?>" />
            <span>px</span>       
        </td>
    </tr>
</table>
<h3>Hover-Grid-Delay</h3>
    <table class="form-table">
    <tr valign="top">
    <th scope="row">Hover-Grid-Deslay:</th>
        <td>
            <input type="text" name="pl_hover_delay" value="<?php echo esc_attr( get_option('pl_hover_delay') ); ?>" />
            <span>ms</span>
        </td>
    </tr>
</table>

    <?php submit_button(); ?>

</form>
</div>