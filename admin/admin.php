<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WP Database CRUD Admin Class
 */
class WTDDB_ADMIN {

	/**
	 *  __construct
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {

		// enqueue admin scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'wtddb_admin_enqueue_scripts' ) );


		// Activation Hook.
		register_activation_hook( WTDDB_PATH . 'wp-database-crud.php', array( $this, 'wtddb_activation_hook' ) );

		// Add admin menu.
		add_action( 'admin_menu', array( $this, 'wtddb_admin_menu' ) );

		// Add New data ajax 
		add_action( 'wp_ajax_wtddb_add_new_data', array( $this, 'wtddb_add_new_data' ) );

		// Add Edit data ajax
		add_action( 'wp_ajax_wtddb_edit_data', array( $this, 'wtddb_edit_data' ) );

		// Delete data ajax
		add_action( 'wp_ajax_wtddb_delete_data', array( $this, 'wtddb_delete_data' ) ); 
 
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function wtddb_admin_enqueue_scripts() {

		// // Enqueue custom stylesheet for plugin admin dashboard.
		wp_enqueue_style( 'wtddb-admin-style', WTDDB_FILE . 'assets/admin/css/wtddb-admin-style.css', array(), WTDDB_VERSION, 'all' );

		// Enqueue custom script for plugin admin dashboard.
		wp_enqueue_script( 'wtddb-admin-script', WTDDB_FILE . 'assets/admin/js/wtddb-admin-script.js', array( 'jquery' ), WTDDB_VERSION, true );

		// Localize script.
		wp_localize_script( 'wtddb-admin-script', 'wtddb_ajax_object', 
			array( 
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'wtddb_nonce' )
			) 
		);
	}

	// Activation Hook
	public function wtddb_activation_hook() {
		global $wpdb;

		// Create table.
		$table_name = $wpdb->prefix . 'wtddb_crud';
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(50) NOT NULL,
			email varchar(50) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	// Add New data ajax
	public function wtddb_add_new_data(){   
		// Check nonce
		if( ! wp_verify_nonce( $_POST['ajax_nonce'], 'wtddb_nonce' ) ){
			wp_send_json_error( 'Invalid Nonce' );
			wp_die();
		} 
		$formData  = $_POST['formData'];
		parse_str( $formData, $formFields );
		$name = $formFields['name'];
		$email = $formFields['email'];

		global $wpdb;
		$table_name = $wpdb->prefix . 'wtddb_crud';
		$data = array(
			'name' => $name,
			'email' => $email
		);
		$format = array(
			'%s',
			'%s'
		);
		$wpdb->insert( $table_name, $data, $format );

		wp_send_json_success( 'Data Added Successfully' );
		wp_die();
	
	}


	// Edit data ajax
	public function wtddb_edit_data(){
		// Check nonce
		if( ! wp_verify_nonce( $_POST['ajax_nonce'], 'wtddb_nonce' ) ){
			wp_send_json_error( 'Invalid Nonce' );
			wp_die();
		} 
		$formData  = $_POST['formData'];
		parse_str( $formData, $formFields );
		$id = $formFields['id'];
		$name = $formFields['name'];
		$email = $formFields['email'];

		global $wpdb;
		$table_name = $wpdb->prefix . 'wtddb_crud';
		$data = array(
			'name' => $name,
			'email' => $email
		);
		$where = array(
			'id' => $id
		);
		$format = array(
			'%s',
			'%s'
		);
		$where_format = array(
			'%d'
		);
		$wpdb->update( $table_name, $data, $where, $format, $where_format );

		wp_send_json_success( 'Data Updated Successfully' );
		wp_die();
	
	}

	// Delete data ajax
	public function wtddb_delete_data(){
		// Check nonce
		if( ! wp_verify_nonce( $_POST['ajax_nonce'], 'wtddb_nonce' ) ){
			wp_send_json_error( 'Invalid Nonce' );
			wp_die();
		} 
		$id = $_POST['id'];

		global $wpdb;
		$table_name = $wpdb->prefix . 'wtddb_crud';
		$wpdb->delete( $table_name, array( 'id' => $id ) );

		wp_send_json_success( 'Data Deleted Successfully' );
		wp_die();
	
	}

	/**
	 * Add admin menu
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function wtddb_admin_menu() {
		
		// Add menu page.
		add_menu_page(
			__( 'WP Database CRUD', 'wp-database-crud' ),
			__( 'WP Database CRUD', 'wp-database-crud' ),
			'manage_options',
			'wp-database-crud',
			array( $this, 'wtddb_admin_page' ),
			'dashicons-admin-tools',
			20
		); 
	}

	/**
	 * Admin page
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function wtddb_admin_page() {
		?>
		<div class="wrap">
			<div class="wtddb-page-title">
				<h1><?php  echo esc_html( __('WP Database CRUD', 'wp-database-crud') ); ?></h1>
				<!-- add new Data Popup -->
				<button class="button button-primary wtddb-add-new"><?php  echo esc_html( __('Add New Data', 'wp-database-crud') ); ?></button>

			</div>
			<?php
			// Display admin page.
			$this->wtddb_admin_page_content();
			?>

			<!-- Add new Data Popup -->
			<div class="wtddb-add-new-popup ">
				<div class="wtddb-add-new-popup-content">
					<span class="wtddb-add-new-popup-close">&times;</span>
					<h2><?php echo esc_html( __('Add New Data', 'wp-database-crud') ); ?></h2>
					<form class="wtddb-addnew-form" action="" method="post">
						<div class="wtddb-form-group">
							<label for="name"><?php  echo esc_html( __('Name', 'wp-database-crud') ); ?></label>
							<input type="text" name="name" id="name" required>
						</div>
						<div class="wtddb-form-group">
							<label for="email"><?php  echo esc_html( __('Email', 'wp-database-crud') ); ?></label>
							<input type="email" name="email" id="email" required>
							
						</div>
						<div class="wtddb-form-group"> 
							<button type="submit" class="button button-primary wtddb-addnew-form-submit"><?php echo  esc_html( __('Add Data', 'wp-database-crud') ); ?></button>
						</div>
					</form>
				</div>
			</div>	
			<!-- Edit Popup -->
			<div class="wtddb-edit-popup ">
				<div class="wtddb-add-new-popup-content">
					<span class="wtddb-edit-popup-close">&times;</span>
					<h2><?php echo esc_html( __('Edit Data', 'wp-database-crud') ); ?></h2>

					<form class="wtddb-edit-form" action="" method="post">
						<!-- id -->
						<input type="hidden" name="id" id="id" required>
						<div class="wtddb-form-group">
							<label for="name"><?php echo esc_html( __('Name', 'wp-database-crud') ); ?></label>
							<input type="text" name="name" id="name" required>
						</div>
						<div class="wtddb-form-group">
							<label for="email"><?php echo esc_html( __('Email', 'wp-database-crud') ); ?></label>
							<input type="email" name="email" id="email" required>
							
						</div>
						<div class="wtddb-form-group"> 
							<button type="submit" class="button button-primary wtddb-edit-form-submit"><?php echo esc_html( __('Add Data', 'wp-database-crud') ); ?></button>
						</div>
					</form>
				</div>
			</div>	

			 
		</div>
		<?php
	}
 

	/**
	 * Admin page content
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function wtddb_admin_page_content() {

		// get Data with wpdb Prepare
		global $wpdb;
		$table_name = $wpdb->prefix . 'wtddb_crud';
		$results = $wpdb->get_results( "SELECT * FROM $table_name" ); 


		?>
		  <table class="wtddb-crud-list-table wp-list-table widefat  striped">
            <thead>
                <tr>
                    <th><?php echo esc_html( __('SL', 'wp-database-crud') ); ?></th>
                    <th><?php echo esc_html( __('ID', 'wp-database-crud') ); ?></th>
                    <th><?php echo esc_html( __('Name', 'wp-database-crud') ); ?></th>
                    <th><?php echo esc_html( __('Email', 'wp-database-crud') ); ?></th>
                    <th><?php echo esc_html( __('Action', 'wp-database-crud') ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($results as $key => $row) {
					$data_edit = wp_json_encode( $row );
                    echo '<tr>';
					// set sl 
					echo '<td> '. esc_html($key+1) .'</td>';
                    echo '<td>' . esc_html($row->id) . '</td>';
                    echo '<td>' . esc_html($row->name) . '</td>';
                    echo '<td>' . esc_html($row->email) . '</td>';
                    echo '<td>';
					echo '<a href="#" edit-data="'.esc_attr($data_edit).'" class="button button-primary wtddb-edit-btn">Edit</a>';
					echo '<a href="#" data-id="'.esc_attr($row->id).'" class="button button-danger wtddb-delete-btn">Delete</a> ';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
		<?php
	}
 

 
 
}
