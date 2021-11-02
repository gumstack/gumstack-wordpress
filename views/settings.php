<div class="wrap">
	        <h2><?php echo $this->plugin->displayName; ?> &raquo; <?php esc_html_e( 'Settings', 'gumstack' ); ?></h2>
	
	        <?php
	        if ( isset( $this->message ) ) {
	                ?>
	                <div class="updated fade"><p><?php echo $this->message; ?></p></div>
	                <?php
	        }
	        if ( isset( $this->errorMessage ) ) {
	                ?>
	                <div class="error fade"><p><?php echo $this->errorMessage; ?></p></div>
	                <?php
	        }
	        ?>
	
	        <div id="poststuff">
	                <div id="post-body" class="metabox-holder columns-2">
	                        <!-- Content -->
	                        <div id="post-body-content">
	                                <div id="normal-sortables" class="meta-box-sortables ui-sortable">
	                                        <div class="postbox">
	                                                <h3 class="hndle"><?php esc_html_e( 'Settings', 'gumstack' ); ?></h3>
	
	                                                <div class="inside">
	                                                        <form action="options-general.php?page=<?php echo $this->plugin->name; ?>" method="post">
	                                                                <p>
	                                                                        <label for="gumstack_api_token"><strong><?php esc_html_e( 'API Token', 'gumstack' ); ?></strong></label>
	                                                                        <input type="text" name="gumstack_api_token" id="gumstack_api_token" style="font-family:Courier New;" <?php echo ( ! current_user_can( 'unfiltered_html' ) ) ? ' disabled="disabled" ' : ''; ?>><?php echo $this->settings['gumstack_api_token']; ?></input>
	                                                                        <?php
	                                                                        printf(
	                                                                                /* translators: %s: The `<head>` tag */
	                                                                                esc_html__( 'You can find this token from your Gumstack account settings section.', 'gumstack' ),
	                                                                                '<code>&lt;head&gt;</code>'
	                                                                        );
	                                                                        ?>
	                                                                </p>
	                                                                <?php if ( current_user_can( 'unfiltered_html' ) ) : ?>
	                                                                        <?php wp_nonce_field( $this->plugin->name, $this->plugin->name . '_nonce' ); ?>
	                                                                        <p>
	                                                                                <input name="submit" type="submit" name="Submit" class="button button-primary" value="<?php esc_attr_e( 'Save', 'gumstack' ); ?>" />
	                                                                        </p>
	                                                                <?php endif; ?>
	                                                        </form>
	                                                </div>
	                                        </div>
	                                        <!-- /postbox -->
	                                </div>
	                                <!-- /normal-sortables -->
	                        </div>
	                        <!-- /post-body-content -->
                            <!-- /postbox-container -->
	                </div>
	        </div>
	</div>