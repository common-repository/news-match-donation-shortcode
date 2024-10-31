<?php
/**
 * The class for the News Match Donation plugin's setting
 *
 * @package NewsMatchDonation\Settings
 */

/**
 * Register and populate a settings page for the News Match Donation Plugin:
 */
class NewsMatchDonation_Settings {
	/**
	 * The slug of the settings page
	 *
	 * @var string $settings_page The settings page slug
	 */
	private $settings_page = 'newsmatchdonation';

	/**
	 * The slug of the settings group
	 *
	 * @var string $settings_group The settings group slug
	 */
	private $settings_group = 'newsmatchdonation_group';

	/**
	 * The slug of the settings section
	 *
	 * @var string $settings_section The slug of the settings section
	 */
	private $settings_section = 'newsmatchdonation_section';

	/**
	 * The prefix used for this plugin's options saved in the options table
	 *
	 * @var string $options_prefix The prefix for this plugin's options saved in the options table
	 */
	public static $options_prefix = 'nmds_';

	/**
	 * The prefix used for the levels option
	 *
	 * This is set in __construct()
	 *
	 * @var string $levels_option The wp_option in the options table that contains the field levels.
	 */
	public $levels_option = '';

	/**
	 * Default value for the levels option
	 *
	 * @var array
	 */
	public static $levels_default = array(
		'gd' => array(
			'a' => 'a',
			'name' => 'Donor',
		),
		'l1' => array(
			'a' => 'a',
			'name' => 'Friend',
			'min' => 0,
			'max' => 5,
		),
		'l2' => array(
			'a' => 'an',
			'name' => 'Ally',
			'min' => 5,
			'max' => 50,
		),
		'l3' => array(
			'a' => 'a',
			'name' => 'Champion',
			'min' => 50,
			'max' => 500,
		),
		'l4' => array(
			'a' => 'an',
			'name' => 'Ambassador',
			'min' => 500,
			'max' => 5000,
		),
	);

	/**
	 * Constructor for the settings class
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_submenu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// if you change this you will make the saved settings for every plugin user inaccessible and reset them to the defaults.
		// please also change it in News_Match_Donation_Shortcode->render_view().
		$this->levels_option = self::$options_prefix . 'levels_';
	}

	/**
	 * Register the settings page
	 */
	public function register_submenu_page() {
		add_submenu_page(
			'options-general.php',
			esc_html__( 'News Match Donation Shortcode', 'newsmatch' ),
			esc_html__( 'News Match Shortcode', 'newsmatch' ),
			'manage_options', // permissions level is this because that seems right for site-wide config options.
			$this->settings_page,
			array( $this, 'settings_page_output' )
		);
	}

	/**
	 * The settings page output
	 *
	 * Should output a bunch of HTML
	 */
	public function settings_page_output() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'newsmatch' ) );
		}
		?>
		<div class="wrap newsmatch-admin">
			<h1><?php esc_html_e( 'News Match Donation Shortcode Options', 'newsmatch' ); ?></h1>
			<form method="post" action="options.php">
				<?php
					settings_fields( $this->settings_group );
					do_settings_sections( $this->settings_page );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * All the settings
	 */
	public function register_settings() {
		add_settings_section(
			$this->settings_section,
			esc_html__( 'General donation settings', 'newsmatch' ),
			array( $this, 'settings_section_callback' ),
			$this->settings_page
		);

		register_setting( $this->settings_group, self::$options_prefix . 'org_name', 'sanitize_text_field' );
		add_settings_field(
			self::$options_prefix . 'org_name',
			__( 'Organization Name', 'newsmatch' ),
			array( $this, 'field_org_name' ),
			$this->settings_page,
			$this->settings_section
		);

		register_setting( $this->settings_group, self::$options_prefix . 'form_toggle', 'sanitize_text_field' );
		add_settings_field(
			self::$options_prefix . 'form_toggle',
			__( 'Donate via Newsmatch.org or FundJournalism.org?', 'newsmatch' ),
			array( $this, 'field_form_toggle' ),
			$this->settings_page,
			$this->settings_section
		);

		register_setting( $this->settings_group, self::$options_prefix . 'org_id', 'sanitize_text_field' );
		add_settings_field(
			self::$options_prefix . 'org_id',
			__( 'Organization ID', 'newsmatch' ),
			array( $this, 'field_org_id' ),
			$this->settings_page,
			$this->settings_section
		);

		register_setting( $this->settings_group, self::$options_prefix . 'default_donation', array( $this, 'sanitize_money_field' ) );
		add_settings_field(
			self::$options_prefix . 'default_donation',
			__( 'Default donation amount', 'newsmatch' ),
			array( $this, 'field_default_donation' ),
			$this->settings_page,
			$this->settings_section
		);

		register_setting( $this->settings_group, self::$options_prefix . 'url_live', 'sanitize_text_field' );
		add_settings_field(
			self::$options_prefix . 'url_live',
			__( 'Live donation form URL', 'newsmatch' ),
			array( $this, 'field_url_live' ),
			$this->settings_page,
			$this->settings_section
		);

		register_setting( $this->settings_group, self::$options_prefix . 'url_staging', 'sanitize_text_field' );
		add_settings_field(
			self::$options_prefix . 'url_staging',
			__( 'Testing donation form URL', 'newsmatch' ),
			array( $this, 'field_url_staging' ),
			$this->settings_page,
			$this->settings_section
		);

		register_setting( $this->settings_group, self::$options_prefix . 'url_toggle', 'sanitize_text_field' );
		add_settings_field(
			self::$options_prefix . 'url_toggle',
			__( 'Use the live or testing donation form?', 'newsmatch' ),
			array( $this, 'field_url_toggle' ),
			$this->settings_page,
			$this->settings_section
		);

		register_setting( $this->settings_group, self::$options_prefix . 'default_sf_id', 'sanitize_text_field' );
		add_settings_field(
			self::$options_prefix . 'default_sf_id',
			__( 'Default Salesforce campaign id', 'newsmatch' ),
			array( $this, 'field_default_sf_id' ),
			$this->settings_page,
			$this->settings_section
		);

		/*
		 * Donor Levels Section
		 */
		// let's just make levels option prefix simple and easy to remember:
		$levels_option = $this->levels_option;

		add_settings_section(
			$this->donor_levels_section,
			esc_html__( 'Donor Levels Settings', 'newsmatch' ),
			array( $this, 'donor_levels_section_callback' ),
			$this->settings_page
		);

		register_setting(
			$this->settings_group,
			$levels_option,
			array(
				'sanitize_callback' => array( $this, 'levels_option_save' ),
				'default' => $this::$levels_default,
			)
		);

		// Generic Donor Article (a/an/the)
		add_settings_field(
			$levels_option . '[gd][a]',
			__( 'Generic donor article', 'newsmatch' ),
			array( $this, 'field_gd_a' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[gd][a]',
			)
		);

		// Generic Donor Label (supporter/donor/sustainer/etc)
		add_settings_field(
			$levels_option . '[gd][name]',
			__( 'Generic donor label', 'newsmatch' ),
			array( $this, 'field_gd_name' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[gd][name]',
			)
		);

		// Level 1 Article (a/an/the)
		add_settings_field(
			$levels_option . '[l1][a]',
			__( 'Level 1 article', 'newsmatch' ),
			array( $this, 'field_l1_a' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l1][a]',
			)
		);

		// Level 1 Name (supporter/donor/sustainer/etc)
		add_settings_field(
			$levels_option . '[l1][name]',
			__( 'Level 1 name', 'newsmatch' ),
			array( $this, 'field_l1_name' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l1][name]',
			)
		);

		// Level 1 Min (minimum dollar emount to qualify)
		add_settings_field(
			$levels_option . '[l1][min]',
			__( 'Level 1 min', 'newsmatch' ),
			array( $this, 'field_l1_min' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l1][min]',
			)
		);

		// Level 1 Max (maximum dollar amount)
		add_settings_field(
			$levels_option . '[l1][max]',
			__( 'Level 1 max', 'newsmatch' ),
			array( $this, 'field_l1_max' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l1][max]',
			)
		);

		// Level 2 Article (a/an/the)
		add_settings_field(
			$levels_option . '[l2][a]',
			__( 'Level 2 article', 'newsmatch' ),
			array( $this, 'field_l2_a' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l2][a]',
			)
		);

		// Level 2 Name (supporter/donor/sustainer/etc)
		add_settings_field(
			$levels_option . '[l2][name]',
			__( 'Level 2 name', 'newsmatch' ),
			array( $this, 'field_l2_name' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l2][name]',
			)
		);

		// Level 2 Min (minimum dollar emount to qualify)
		add_settings_field(
			$levels_option . '[l2][min]',
			__( 'Level 2 min', 'newsmatch' ),
			array( $this, 'field_l2_min' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l2][min]',
			)
		);

		// Level 2 Max (maximum dollar amount)
		add_settings_field(
			$levels_option . '[l2][max]',
			__( 'Level 2 max', 'newsmatch' ),
			array( $this, 'field_l2_max' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l2][max]',
			)
		);

		// Level 3 Article (a/an/the)
		add_settings_field(
			$levels_option . '[l3][a]',
			__( 'Level 3 article', 'newsmatch' ),
			array( $this, 'field_l3_a' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l3][a]',
			)
		);

		// Level 3 Name (supporter/donor/sustainer/etc)
		add_settings_field(
			$levels_option . '[l3][name]',
			__( 'Level 3 name', 'newsmatch' ),
			array( $this, 'field_l3_name' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l3][name]',
			)
		);

		// Level 3 Min (minimum dollar emount to qualify)
		add_settings_field(
			$levels_option . '[l3][min]',
			__( 'Level 3 min', 'newsmatch' ),
			array( $this, 'field_l3_min' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l3][min]',
			)
		);

		// Level 3 Max (maximum dollar amount)
		add_settings_field(
			$levels_option . '[l3][max]',
			__( 'Level 3 max', 'newsmatch' ),
			array( $this, 'field_l3_max' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l3][max]',
			)
		);

		// Level 4 Article (a/an/the)
		add_settings_field(
			$levels_option . '[l4][a]',
			__( 'Level 4 article', 'newsmatch' ),
			array( $this, 'field_l4_a' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l4][a]',
			)
		);

		// Level 4 Name (supporter/donor/sustainer/etc)
		add_settings_field(
			$levels_option . '[l4][name]',
			__( 'Level 4 name', 'newsmatch' ),
			array( $this, 'field_l4_name' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l4][name]',
			)
		);

		// Level 4 Min (minimum dollar emount to qualify)
		add_settings_field(
			$levels_option . '[l4][min]',
			__( 'Level 4 min', 'newsmatch' ),
			array( $this, 'field_l4_min' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l4][min]',
			)
		);

		// Level 4 Max (minimum dollar emount to qualify)
		add_settings_field(
			$levels_option . '[l4][max]',
			__( 'Level 4 max', 'newsmatch' ),
			array( $this, 'field_l4_max' ),
			$this->settings_page,
			$this->donor_levels_section,
			array(
				'id' => $levels_option . '[l4][max]',
			)
		);
	}

	/**
	 * Settings section description
	 */
	public function settings_section_callback() {
		echo wp_kses_post( __( 'For more information about these settings, and about the plugin, please see the <a href="https://wordpress.org/plugins/news-match-donation-shortcode">plugin page on WordPress.org</a>.', 'newsmatch' ) );
	}

	/**
	 * Settings section description for donor levels
	 */
	public function donor_levels_section_callback() {
		echo esc_html__( 'In this section, set the names and donation amounts for the various donation levels. You can configure up to four donation levels, or use fewer.' , 'newsmatch' );
	}

	/**
	 * Output the input field for the organization's name
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_org_name( $args ) {
		$option = self::$options_prefix . 'org_name';
		$value = get_option( $option, '' );
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $option ),
			esc_attr( $value )
		);
	}

	/**
	 * Ouput the radio button for toggling between 'Newsmatch.org' and 'FundJournalism.org' urls
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_form_toggle( $args ) {
		$option = self::$options_prefix . 'form_toggle';
		$value = get_option( $option, 'nm' );
		if ( ! in_array( $value, array( 'nm', 'fj' ), true ) ) {
			$value = 'nm';
		}

		echo sprintf(
			'<p><label>
				<input name="%1$s" id="%1$s-nm" type="radio" value="nm" %2$s>
				%4$s
			</label></p>
			<p><label>
				<input name="%1$s" id="%1$s-fj" type="radio" value="fj" %3$s>
				%5$s
			</label></p>',
			esc_attr( $option ),
			checked( $value, 'nm', false ), // Checked for NM.
			checked( $value, 'fj', false ), // Checked for FJ.
			esc_html__( 'NewsMatch.org', 'newsmatch' ),
			esc_html__( 'FundJournalism.org', 'newsmatch' )
		);
	}

	/**
	 * Output the input field for the organization's org_id
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_org_id( $args ) {
		$option = self::$options_prefix . 'org_id';
		$value = get_option( $option, '' );
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $option ),
			esc_attr( $value )
		);
	}

	/**
	 * Output the input field for the organization's default_donation
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_default_donation( $args ) {
		$option = self::$options_prefix . 'default_donation';
		$value = get_option( $option, '15.00' );
		echo sprintf(
			'$<input name="%1$s" id="%1$s" type="number" min="0.01" step="0.01" value="%2$s">',
			esc_attr( $option ),
			esc_attr( $value )
		);
	}

	/**
	 * Output the input field for the live donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_url_live( $args ) {
		$option = self::$options_prefix . 'url_live';
		$value = get_option( $option, '' );
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $option ),
			esc_attr( $value )
		);
	}

	/**
	 * Output the input field for the staging/testing donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_url_staging( $args ) {
		$option = self::$options_prefix . 'url_staging';
		$value = get_option( $option, '' );
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $option ),
			esc_attr( $value )
		);
	}

	/**
	 * Ouput the radio button for toggling between 'live' and 'testing' urls
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_url_toggle( $args ) {
		$option = self::$options_prefix . 'url_toggle';
		$value = get_option( $option, 'staging' );
		if ( ! in_array( $value, array( 'staging', 'live' ), true ) ) {
			$value = 'staging';
		}

		echo sprintf(
			'<p><label>
				<input name="%1$s" id="%1$s-staging" type="radio" value="staging" %2$s>
				%4$s
			</label></p>
			<p><label>
				<input name="%1$s" id="%1$s-live" type="radio" value="live" %3$s>
				%5$s
			</label></p>',
			esc_attr( $option ),
			checked( $value, 'staging', false ), // Checked for testing.
			checked( $value, 'live', false ), // Checked for live.
			esc_html__( 'Staging', 'newsmatch' ),
			esc_html__( 'Live', 'newsmatch' )
		);
	}

	/**
	 * Render the settings input for the default SalesForce ID
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_default_sf_id( $args ) {
		$option = self::$options_prefix . 'default_sf_id';
		$value = get_option( $option, '' );
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $option ),
			esc_attr( $value )
		);
	}

	/**
	 * Output the input field for the generic donor article
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_gd_a( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['gd']['a'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'a, an, the: This is an article that refers to a donor.', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the generic donor label
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_gd_name( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['gd']['name'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'Supporter, Donor, Sustainer, etc.', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 1 article
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l1_a( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l1']['a'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'a, an, the: This is an article that refers to a donor of this level.', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 1 name
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l1_name( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l1']['name'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'i.e. Friend', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 1 min
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l1_min( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l1']['min'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'minimum dollar amount to qualify for this level', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 1 max
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l1_max( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l1']['max'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'maximum dollar amount for this level', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 2 article
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l2_a( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l2']['a'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'a, an, the: This is an article that refers to a donor of this level.', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 2 name
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l2_name( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l2']['name'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'i.e. Ally', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 2 min
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l2_min( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l2']['min'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'minimum dollar amount to qualify for this level', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 2 max
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l2_max( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l2']['max'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'maximum dollar amount for this level', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 3 article
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l3_a( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l3']['a'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'a, an, the: This is an article that refers to a donor of this level.', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 3 name
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l3_name( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l3']['name'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'i.e. Champion', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 3 min
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l3_min( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l3']['min'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'minimum dollar amount to qualify for this level', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 3 max
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l3_max( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l3']['max'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'maximum dollar amount for this level', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}


	/**
	 * Output the input field for the level 4 article
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l4_a( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l4']['a'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'a, an, the: This is an article that refers to a donor of this level.', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 4 name
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l4_name( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l4']['name'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'i.e. Ambassador', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 4 min
	 *
	 * This is part of the donation form URL
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l4_min( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l4']['min'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'minimum dollar amount to qualify for this level', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Output the input field for the level 4 max
	 *
	 * This is part of the donation form URL

	 // make sure that the value is one that we would want to preserve, and that it is sanitized
	 *
	 * @param array $args Optional arguments passed to callbacks registered with add_settings_field.
	 */
	public function field_l4_max( $args ) {
		$option_value = get_option( $this->levels_option, $this::$levels_default );
		$value = $option_value['l4']['max'];
		echo sprintf(
			'<input name="%1$s" id="%1$s" type="text" value="%2$s">',
			esc_attr( $args['id'] ),
			esc_attr( $value )
		);
		echo sprintf(
			'<label for="%2$s"><i>%1$s</i></label>',
			esc_html__( 'minimum dollar amount to qualify for this level', 'newsmatch' ),
			esc_attr( $args['id'] )
		);
	}

	/**
	 * Clean and save the levels options
	 *
	 * @since 0.1
	 * @param array $submission the submitted option value for the levels settings.
	 * @return The sanitized submission, or the default values, whichever is safer.
	 */
	public function levels_option_save( $submission = array() ) {
		// This is where we will store the post-sanitization options.
		$proposed = array();

		if ( is_array( $submission ) ) {
			foreach ( $submission as $entry_key => $values ) {
				$sanitized_entry = sanitize_key( $entry_key );

				// make sure that the option is a name that we want to preserve, and sanitize it, for it is an array key.
				if ( ! empty( $sanitized_entry ) && in_array( $sanitized_entry, array( 'gd', 'l1', 'l2', 'l3', 'l4' ), true ) ) {
					$proposed[ $sanitized_entry ] = array();

					// sanitize all the option values!
					foreach ( $values as $key => $value ) {
						$sanitized_key = sanitize_key( $key );
						$sanitized_value = sanitize_text_field( $value );

						// make sure that the value is one that we would want to preserve, and that it is sanitized.
						if (
							! empty( $sanitized_key )
							&& in_array( $sanitized_key, array( 'a', 'name', 'min', 'max' ), true )
							&& ! empty( $sanitized_value )
						) {
							$proposed[ $sanitized_entry ][ $sanitized_key ] = $sanitized_value;
						}
					}
				}
			}
		}

		$cleaned = wp_parse_args( $proposed, $this::$levels_default );

		return $cleaned;
	}

	/**
	 * Sanitize money input
	 *
	 * @since 0.1.1
	 * @param string $amount The saved amount
	 * @return float The safe saved amount: >= 0.01
	 */
	public function sanitize_money_input( $amount ) {
		$return = 0.01;

		// default donation amount must be greater than 0.
		if ( is_numeric( $amount ) ) {
			$round = round( (float) $amount, 2 );
			if ( 0.01 <= $round ) {
				$return = $round;
			} elseif ( 0.01 <= ( -1 * $round ) ) {
				$return = -1 * $round;
			}
		}
		// if the amount was 0 < abs(x) < 0.01, well, it's 0.01 now.
		return $return;
	}
}
