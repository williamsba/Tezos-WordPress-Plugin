<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://strangework.com
 * @since      0.1
 *
 * @package    Hen
 * @subpackage Hen/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Hen
 * @subpackage Hen/admin
 * @author     Brad Williams <bradw@illiams.com>
 */
class Hen_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.1
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Hen_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Hen_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/hen-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.1
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Hen_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Hen_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/hen-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register Settings page
	 *
	 * @since    0.1
	 */
	public function hen_admin_settings() {
	    ?>
	    <div class="wrap">
		    <h2>Tezos WordPress Plugin</h2>
		    <!--<img src="<?php echo plugin_dir_url( __FILE__ ) . 'img/HEN-logo.png'; ?>" width="150">-->
		    <form action="options.php" method="post">
			    <?php 
	            settings_fields( 'hen_plugin_options' );
			    do_settings_sections( 'hen_plugin' );
			    submit_button( 'Save Changes', 'primary' ); 
	            ?>
		    </form>



	    </div>
	    <?php

	}

	/**
	 * Register admin menu
	 *
	 * @since    0.1
	 */
	public function hen_create_menu() {
             
	    // Create custom top-level menu
	    add_menu_page( 'Tezos Settings', 'Tezos', 'manage_options', 'tezos-nfts', array( $this, 'hen_admin_settings' ), 'dashicons-smiley' );

	    add_submenu_page( 'tezos-nfts', 'Import your Tezos NFTs', 'Import NFTs', 'manage_options', 'hen-import', array( $this, 'hen_import_nfts' ) );
             
	    add_submenu_page( 'tezos-nfts', 'Delete your Tezos NFTs', 'Delete NFTs', 'manage_options', 'hen-delete', array( $this, 'hen_delete_nfts' ) );

	}

	/**
	 * Register our settings field with the Settings API
	 *
	 * @since    0.1
	 */
	public function hen_plugin_admin_init(){

		// Define the setting args
		$args = array(
		    'type' 				=> 'string', 
		    'sanitize_callback' => array( $this, 'hen_plugin_validate_options' ),
		    'default' 			=> NULL
		);

	    // Register our settings
	    register_setting( 'hen_plugin_options', 'hen_plugin_options', $args );
	    
	    // Add a settings section
	    add_settings_section( 
	    	'hen_plugin_main', 
	    	'Tezos Plugin Settings',
	        array( $this, 'hen_plugin_section_text' ), 
	        'hen_plugin' 
	    );
	    
	    // Create our settings field for name
	    add_settings_field( 
	    	'hen_plugin_name', 
	    	'Tezos Wallet',
	        array( $this, 'hen_plugin_setting_name' ), 
	        'hen_plugin', 
	        'hen_plugin_main' 
	    );

	}

	/**
	 * Section header
	 *
	 * @since    0.1
	 */
	public function hen_plugin_section_text() {

	    echo '<p>Connect your Tezos Wallet to import your Tezos NFT collection to WordPress.</p>';

	}
	        
	/**
	 * Display the Tezos Wallet settings field and wallet metadata if saved
	 *
	 * @since    0.1
	 */
	public function hen_plugin_setting_name() {

	    // Get option 'text_string' value from the database
	    $options = get_option( 'hen_plugin_options' );
	    $tz_wallet = isset( $options['tz_wallet'] ) ? $options['tz_wallet'] : '';

	    // Display the field
	    echo "<input id='tz_wallet' name='hen_plugin_options[tz_wallet]' type='text' value='" . esc_attr( $tz_wallet ) . "' size='50' />";
	    
	    //SUCCESFUL 200 RESPONSE CONFIRMS WALLET EXISTS
		if ( array( $this, 'hen_validate__wallet' )( $tz_wallet ) == 200 ) {

			echo '<p>WALLET CONNECTED SUCCESSFULL</p>';

			//check for wallet metadata to display
			call_user_func_array( array( $this, 'hen_get_tezos_wallet_metadata' ), array( $tz_wallet ) );


		} ;

	}

	/**
	 * Validate and sanitize the Tezos Wallet settings option
	 *
	 * @since    0.1
	 */
	public function hen_plugin_validate_options( $input ) {
	        
	    // Sanitize the data we are receiving 
	    $valid['tz_wallet'] = sanitize_text_field( $input['tz_wallet'] );

	    return $valid;
	}

	/**
	 * Return the Tezos wallet metadata
	 *
	 * @since    0.1
	 */
	public function hen_get_tezos_wallet_metadata( $tz_wallet ) {

	    // TZKT https://api.tzkt.io/#operation/Accounts_GetMetadata
	    $request = wp_remote_get( 'https://api.tzkt.io/v1/accounts/' .esc_attr( $tz_wallet ). '/metadata' );

	    // If an error is returned, return false to end the request
	    if( is_wp_error( $request ) ) {
	        return false;
	    }

	    // Retrieve only the body from the raw response
	    $body = wp_remote_retrieve_body( $request );

	    // Decode the JSON string
	    $data = json_decode( $body );

		?>
		<p>
			<ul>
				<li><img src="https://services.tzkt.io/v1/avatars2/<?php echo esc_attr( $tz_wallet ); ?>"></li>
				<li><strong><?php echo isset( $data->alias ) ? esc_html( $data->alias ) : null; ?></strong></li>
				<li><?php echo isset( $data->description ) ? esc_html( $data->description ) : null; ?></li>
				<li><?php //echo $data->twitter; ?></li>
				<li><?php //echo $data->instagram; ?></li>
				<li><?php //echo $data->facebook; ?></li>
			</ul>
		</p>
		<?php

	}

	/**
	 * Return the Tezos wallet address stored as an option
	 *
	 * @since    0.1
	 */
	public function hen_get_wallet() {

		$hen_options = get_option( 'hen_plugin_options' );
		$hen_tz_wallet = ( !empty( $hen_options) ) ? $hen_options['tz_wallet'] : '';

		return $hen_tz_wallet;

	}

	/**
	 * Verify the Tezos wallet exists
	 *
	 * @since    0.1
	 */
	public function hen_validate__wallet( $tz_wallet ) {

	    // BCD https://better-call.dev/docs#operation/get-account-info
	    //$request = wp_remote_get( 'https://api.better-call.dev/v1/account/mainnet/' .esc_attr( $tz_wallet ) );

	    // TZKT https://api.tzkt.io/#operation/Accounts_GetMetadata
	    $request = wp_remote_get( 'https://api.tzkt.io/v1/accounts/' .esc_attr( $tz_wallet ). '/metadata' );

	    // If an error is returned, return false to end the request
	    if( is_wp_error( $request ) ) {
	        return false;
	    }

	    // Retrieve only the body from the raw response
	    $code = wp_remote_retrieve_response_code( $request );

	    return $code;

	}

	public function hen_plugin_footer() {

		if ( isset( $_GET['page'] ) && strpos( $_GET['page'], "hen" ) !== false ) {
			echo 'Made with&nbsp;❤️&nbsp;by Brad (<a href="https://twitter.com/williamsba" target=_"_blank">@williamsba</a>). If you enjoy this plugin donate some Tezos to help maintain it! tz1hFfHPJEXeysH1QYBJKQnPJ2R65gcBYWnD';

		}	

	}

	/**
	 * NFT Import Section
	 *
	 * @since    0.1
	 */
	public function hen_import_nfts() {

		?>
		<h2>Tezos WordPress Plugin - Import your Tezos NFTs</h2>
		<p>Import all Tezos NFTs into the NFT custom post type in WordPress.</p>
		<form method="post">
			<?php submit_button( 'Import', 'primary', 'hen-import' ); ?>
		</form>

		<?php

	    //Import NFTs if requested
	    if ( !empty( $_POST["hen-import"] ) ) {
	    	$this->hen_nft_import();
	    }

	}

	/**
	 * NFT Importer Code
	 *
	 * @since    0.1
	 */
	public function hen_nft_import() {
		
		$tz_wallet = $this->hen_get_wallet();
		
		//get total tokens/NFTs in wallet (returns ALL NFTs regardless of balance)
		$tz_total = $this->hen_get_total_nft_count( $tz_wallet );

		//start the offset at 0
		$the_offset = 0;

		//LOOP THROUGH ALL TOKENS
		for ( $loop_count = ceil( $tz_total / 10 ) ; $loop_count >= 0 ; $loop_count-- ) {

				//Set the request URL to the BDC API
				$request_url = 'https://api.better-call.dev/v1/account/mainnet/' .esc_attr( $tz_wallet ). '/token_balances?sort_by=balance&offset=' .( $the_offset );

				
				//helpful when debugging to see the request URL being used
				//echo 'REQUESTING: ' .esc_url_raw( $request_url ) .'<br />';

			    $request = wp_remote_get( esc_url_raw( $request_url ) );


			    // If an error is returned, return false to end the request
			    if( is_wp_error( $request ) ) {
			        return false;
			    }

			    // Retrieve only the body from the raw response
			    $body = wp_remote_retrieve_body( $request );

			    // Decode the JSON string
			    $api_data = json_decode( $body );

			    //var_dump( $api_data );

			    // Verify the $data variable is not empty
			    if( ! empty( $api_data ) ) {

			    	//total tokens attached to the Tezos wallet
			    	//this includes all tokens every owned. 
			    	//Check balance > 0 to confirm it is still owned
			    	//$total_tokens = absint( $api_data->total );

			        echo '<ul>';

			        // Loop through the returned dataset 
			        for ( $i = 0 ; $i < 10; $i++ ) {

			            if ( isset( $api_data->balances[$i]->name ) && !empty($api_data->balances[$i]->name) ) {

			            	echo '<li>';
			            	//echo 'SIZE OF: '.sizeof( $api_data->balances );
			            	//set NFT name
			            	$nft_name = $api_data->balances[$i]->name;
			            	echo '<strong>'. $i . '. ' .esc_html( $nft_name ) .' - IMPORTED</strong>';

			            	//set NFT display URI
			            	//$nft_file = str_replace( 'ipfs://', 'https://cloudflare-ipfs.com/ipfs/', $data->balances[$i]->display_uri );
			            	
			            	//echo $data->balances[$i]->formats[0]->uri;
			            	//$nft_file = str_replace( 'ipfs://', 'https://cloudflare-ipfs.com/ipfs/', $api_data->balances[$i]->formats[0]->uri );
			            	$nft_file =  isset( $api_data->balances[$i]->formats[0]->uri ) ? str_replace( 'ipfs://', 'https://cloudflare-ipfs.com/ipfs/', $api_data->balances[$i]->formats[0]->uri ) : null;

			            	//echo '<img src="' .esc_attr( $nft_file ). '" height="150" />';

			            	//set NFT description
			            	$nft_desc = ( isset( $api_data->balances[$i]->name ) ) ? $api_data->balances[$i]->name : '';

			            	//set NFT post data
							$post_data = array(
							            'post_title'    => esc_html( $nft_name ),
							            'post_content'  => esc_html( $nft_desc ),
							            'post_status'   => 'publish',
							            'post_type'     => 'nft',
							            'post_author'   => get_current_user_id(),
							            );

							//Save new NFT CPT entry
							$nft_id = wp_insert_post( $post_data );

							//SAVE METADATA
							$hen_token_id = $api_data->balances[$i]->token_id;
							update_post_meta( $nft_id, 'hen_token_id', absint( $hen_token_id ) );

							$hen_nft_file = $nft_file;
							update_post_meta( $nft_id, 'hen_nft_file', $hen_nft_file );

							//$hen_nft_creator = $api_data->balances[$i]->creators;
							$hen_nft_creator =  isset( $api_data->balances[$i]->creators ) ? $api_data->balances[$i]->creators : null;
							update_post_meta( $nft_id, 'hen_nft_creator', $hen_nft_creator );

							$hen_nft_balance =  isset( $api_data->balances[$i]->balance ) ? $api_data->balances[$i]->balance : null;
							update_post_meta( $nft_id, 'hen_nft_balance', absint( $hen_nft_balance ) );

							//unique metakey to track all NFT entries this plugin creates
							update_post_meta( $nft_id, 'hen_nft_plugin', true );

							//SET FEATURED IMAGE
							//IPFS does not include a file extension, so this currently does not work.
							//related trac ticket: https://core.trac.wordpress.org/ticket/18730
							//$image = media_sideload_image( $hen_nft_file, $nft_id );

			            	echo '</li>';

			        	}

			    	}

			    	echo '</ul>';

			    	//increase the offset by 10
			    	$the_offset = $the_offset + 13;

				}

			}

	}

	/**
	 * Count total NFTs in a wallet. 
	 *
	 * @since    0.1
	 */
	public function hen_get_total_nft_count( $tz_wallet ) {

		//$request = wp_remote_get( 'https://api.better-call.dev/v1/account/mainnet/' .esc_attr( $tz_wallet ). '/token_balances' );
		$request = wp_remote_get( 'https://api.better-call.dev/v1/account/mainnet/' .esc_attr( $tz_wallet ). '/count' );

	    // If an error is returned, return false to end the request
	    if( is_wp_error( $request ) ) {
	        return false;
	    }

	    // Retrieve only the body from the raw response
	    $body = wp_remote_retrieve_body( $request );

	    // Decode the JSON string
	    $data = json_decode( $body );
	    //var_dump( $data );
	    
	    foreach ($data as $arr) {

	    	$total = $arr;
	    }
	    
	    // Verify the $total variable is not empty
	    if( ! empty( $total ) ) {

	    	//total tokens attached to the Tezos wallet
	    	//this includes all tokens every owned. 
	    	//Check balance > 0 to confirm it is still owned
	    	return absint( $total );

	    }else{
	    	return 0;
	    }

	}


	/**
	 * NFT Import Section
	 *
	 * @since    0.1
	 */
	public function hen_delete_nfts() {

		?>
		<h2>Tezos WordPress Plugin - Delete your Tezos NFTs</h2>

		<P>Delete all imported Tezos NFTs. This will only delete content created by this plugin.</p>
	   <form method="post">
	    	<?php submit_button( 'Delete', 'primary', 'hen-delete' ); ?>
	    </form>

		<?php

	    //Delete all imported NFT entries
	    if ( !empty( $_POST["hen-delete"] ) ) {
	    	$this->hen_nft_delete_all();
	    }

	}

	/**
	 * Delete all NFT entries created by this plugin
	 *
	 * @since    0.1
	 */
	public function hen_nft_delete_all() {

		//delete all NFT custom post types entries that don't include the unique meta_data key set when created
		$all_nfts = get_posts( 
			array( 
				'post_type' 	=>	'nft',
				'numberposts' 	=>	-1, 
				'meta_key'		=>	'hen_nft_plugin',
				'meta_value'	=>	true 
			) 
		);

		$x = 0;

		foreach ( $all_nfts as $nft ) {

			wp_delete_post( absint( $nft->ID ), true );

			$x++;

		}

		echo $x . ' NFT entries deleted';

	}

}
