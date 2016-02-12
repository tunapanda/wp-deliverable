<div class="wrap">
    <h2>Deliverable xAPI Settings</h2>

    <?php if ($_GET['settings-updated']==true) { ?>
        <div id="message" class="updated">
            <p>
            xAPI settings updated.
            </p>
        </div>
    <?php } ?>

    <p>
        The <tt>wp-deliverable</tt> plugin can optionally send statements to 
        an xAPI Learning Record Store.<br/><br/>
        To enable this, put the connection information in the form below.
    </p>

    <h3>xAPI Endpoint Settings</h3>
    <form method="post" action="options.php">
        <?php settings_fields('deliverable'); ?>
        <?php do_settings_sections('deliverable'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">xAPI Endpoint URL</th>
                <td>
                    <input type="text" name="deliverable_xapi_endpoint_url" 
                        value="<?php echo esc_attr(get_option("deliverable_xapi_endpoint_url")); ?>" 
                        class="regular-text"/>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">xAPI Username</th>
                <td>
                    <input type="text" name="deliverable_xapi_username" 
                        value="<?php echo esc_attr(get_option("deliverable_xapi_username")); ?>" 
                        class="regular-text"/>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">xAPI Password</th>
                <td>
                    <input type="text" name="deliverable_xapi_password" 
                        value="<?php echo esc_attr(get_option("deliverable_xapi_password")); ?>" 
                        class="regular-text"/>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>