<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://ownerrez.com
 * @since      1.0.0
 *
 * @package    OwnerRez
 * @subpackage OwnerRez/admin/partials
 */

function orez_render_admin($username, $token, $status, $apiRoot, $externalSiteName)
{ ?>

    <div class="wrap">

        <h1><?php _e("OwnerRez Settings", "ownerrez"); ?></h1>

        <?php if ($status == "success") { ?>

            <div class="notice notice-success is-dismissible">
                <p><strong><?php _e("Settings saved.", "ownerrez"); ?></strong></p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text"><?php _e("Dismiss this notice.", "ownerrez"); ?></span>
                </button>
            </div>

        <?php } else if ($status == "connection-failure") { ?>

            <div class="notice notice-error is-dismissible">
                <p><strong><?php _e("We were unable to establish a connection with the username and access token provided. Please ensure the values you entered are correct, and that the Hosted Sites premium feature has been enabled on your account.", "ownerrez"); ?></strong></p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text"><?php _e("Dismiss this notice.", "ownerrez"); ?></span>
                </button>
            </div>

        <?php } ?>

        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input id="ownerrez_action" type="hidden" name="action" value="save_ownerrez_settings" />

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="ownerrez_username"><?php _e("OwnerRez Username (email):", "ownerrez"); ?></label>
                    </th>
                    <td>
                        <input id="ownerrez_username" class="regular-text" type="text" name="ownerrez_username" value="<?php echo $username; ?>" />
                        <p class="description"><?php _e("This is the email address of the primary account holder.", "ownerrez"); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ownerrez_token"><?php _e("Personal Access Token:", "ownerrez"); ?></label>
                    </th>
                    <td>
                        <input id="ownerrez_token" class="regular-text" type="password" name="ownerrez_token" value="<?php echo $token; ?>" />
                        <p class="description"><?php _e("You can generate an access token in OwnerRez under Settings -> WordPress Plugin.", "ownerrez"); ?></p>
                    </td>
                </tr>
                <?php if ($externalSiteName) { ?>
                    <tr>
                        <th scope="row">
                            <label for="ownerrez_externalSiteName"><?php _e("Registered as External Site:", "ownerrez"); ?></label>
                        </th>
                        <td>
                            <input id="ownerrez_token" readonly class="regular-text" type="text" name="ownerrez_externalSiteName" value="<?php echo $externalSiteName; ?>" />
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <th colspan="2">
                        <a href="#" data-toggle="display" data-target=".advanced-settings"><?php _e("Advanced", "ownerrez"); ?> <i class="fa fa-chevron-down"></i></a>
                    </th>
                </tr>
                <tr class="advanced-settings" style="display:none;">
                    <th scope="row">
                        <label for="ownerrez_apiRoot"><?php _e("API Root Url:", "ownerrez"); ?></label>
                    </th>
                    <td>
                        <input id="ownerrez_apiRoot" class="regular-text" type="text" name="ownerrez_apiRoot" value="<?php echo $apiRoot; ?>" />
                        <p class="description"><?php _e("This is for advanced usage. You should not need to change this option.", "ownerrez"); ?></p>

                    </td>
                </tr>
            </table>

            <p class="submit">
                <button class="button button-primary" type="submit"><?php _e("Save & Register", "ownerrez"); ?></button>
                <!-- <button class="button button-default" type="button" id="ownerrez-testconnection"><?php _e("Test Connection", "ownerrez"); ?></button> -->
            </p>
        </form>

    </div>

<?php } ?>