<?php

namespace TagConcierge\ConsentModeBannerFree\Service;

use TagConcierge\ConsentModeBannerFree\Util\SanitizationUtil;
use TagConcierge\ConsentModeBannerFree\Util\SettingsUtil;

class SettingsService {

	private $settingsUtil;

	public function __construct( SettingsUtil $settingsUtil) {
		$this->settingsUtil = $settingsUtil;


		$this->initialize();
	}

	private function initialize(): void {
		$this->settingsUtil->addTab(
			'settings',
			'Settings'
		);

		$this->settingsUtil->addTab(
			'event_settings',
			'Consent Types'
		);

		add_action( 'admin_init', [$this, 'settingsInit'] );
		add_action( 'admin_menu', [$this, 'optionsPage'] );
	}

	public function settingsInit(): void {
		$this->settingsUtil->addSettingsSection(
			'basic',
			'Basic Settings',
			'This plugin allows to quickly deploy robust, privacy-oriented setup using Google Consent Mode (compatible with Google Tag Manager and Google Tags). Under the hood it uses a lightweight (1.9kB) Consent Banner.',
			'settings'
		);

		$this->settingsUtil->addSettingsSection(
			'banner',
			'Banner Main Modal',
			'Customise content of the main banner modal that is shown when user has not provided their consent. It allows user to grant all consent types or open settings.',
			'settings'
		);

		$this->settingsUtil->addSettingsSection(
			'banner_settings_modal',
			'Banner Settings Modal',
			'Customise content of the settings modal that allows user to grant only selected consent types.',
			'settings'
		);

		$this->settingsUtil->addSettingsSection(
			'gtm_snippet',
			'Google Tag Manager snippet',
			'You can use the settings below to optionally load GTM instance. If you are already loading GTM snippets somewhere else e.g. other plugins or directly in the theme code leave those fields empty. Paste two snippets provided by GTM. To find those snippets navigate to `Admin` tab in GTM console and click `Install Google Tag Manager`. If you already implemented GTM snippets in your page, paste them below, but select appropriate `Prevent loading GTM Snippet` option.',
			'settings'
		);

		$this->settingsUtil->addSettingsSection(
			'consent_settings',
			'Consent Types',
			'A consent type is related to a purpose of cookies storage and data processing. Advertising or Personalization are examples of two distinct purposes. You can learn more in <a href="https://support.google.com/google-ads/answer/13802165?sjid=8325387811722316139-EU">Google Consent Mode reference documentation</a>. You can specify any number of consent types below, whether they are one of the standard Google types or not. They will be added and configured in all consent mode parameters pushed to DataLayer and GTM workspace.',
			'event_settings'
		);

		$this->settingsUtil->addSettingsField(
			'disabled',
			'Disable?',
			[$this, 'checkboxField'],
			'basic',
			'When checked the plugin won\'t load anything in the page.'
		);

		$this->settingsUtil->addSettingsField(
			'banner_display_mode',
			'Display Mode',
			[$this, 'selectField'],
			'basic',
			'Form of the banner, small bar at the bottom of the page or center modal covering content.',
			['options' => ['bar' => 'Bar', 'modal' => 'Modal']]
		);

		$this->settingsUtil->addSettingsField(
			'banner_display_wall',
			'Wall',
			[$this, 'checkboxField'],
			'basic',
			'Whether to display a "wall" which will cover (with some default opacity) the content of the page when banner is shown.'
		);

		$this->settingsUtil->addSettingsField(
			'banner_theme',
			'Theme',
			[$this, 'selectField'],
			'basic',
			'Select theme of the banner that will apply default styling.',
			['options' => ['light' => 'Light']]
		);

		$this->settingsUtil->addSettingsField(
			'gtm_snippet_head',
			'GTM Snippet Head',
			[$this, 'textareaField'],
			'gtm_snippet',
			'Paste the first snippet provided by GTM. It will be loaded in the <head> of the page.',
			['rows' => 9]
		);

		$this->settingsUtil->addSettingsField(
			'gtm_snippet_body',
			'GTM Snippet Body',
			[$this, 'textareaField'],
			'gtm_snippet',
			'Paste the second snippet provided by GTM. It will be load after opening <body> tag.',
			['rows' => 6]
		);

		$this->settingsUtil->addSettingsField(
			'banner_title',
			'Title',
			[$this, 'inputField'],
			'banner',
			'Title of the main banner modal. Not shown when Display Mode is set to "bar".'
		);

		$this->settingsUtil->addSettingsField(
			'banner_description',
			'Content',
			[$this, 'textareaField'],
			'banner',
			'Content of the main banner. Supports simple markdown like [links](https://url.com) or **bold**. Buttons will be shown on the right side of this content.'
		);

		$this->settingsUtil->addSettingsField(
			'banner_buttons_accept',
			'Accept Button',
			[$this, 'inputField'],
			'banner',
			'Text of "accept" button on the main banner.'
		);

		$this->settingsUtil->addSettingsField(
			'banner_buttons_settings',
			'Open Settings Button',
			[$this, 'inputField'],
			'banner',
			'Text of "settings" button on the main banner.'
		);

		$this->settingsUtil->addSettingsField(
			'banner_buttons_reject',
			'Reject Button',
			[$this, 'inputField'],
			'banner',
			'Text of "reject" or "deny" button on the main banner.'
		);

		$this->settingsUtil->addSettingsField(
			'banner_settings_title',
			'Title',
			[$this, 'inputField'],
			'banner_settings_modal',
			'Title of the settings modal.'
		);

		$this->settingsUtil->addSettingsField(
			'banner_settings_description',
			'Content',
			[$this, 'textareaField'],
			'banner_settings_modal',
			'Content of the settings modal. Supports simple markdown like [links](https://url.com) or **bold**. '
		);

		$this->settingsUtil->addSettingsField(
			'banner_settings_buttons_save',
			'Save Button',
			[$this, 'inputField'],
			'banner_settings_modal',
			'Text of "save" button on the settings modal.'
		);

		$this->settingsUtil->addSettingsField(
			'banner_settings_buttons_close',
			'Close Button',
			[$this, 'inputField'],
			'banner_settings_modal',
			'Text of "close" button on the settings modal.'
		);

		$this->settingsUtil->addSettingsField(
			'banner_settings_buttons_reject',
			'Reject Button',
			[$this, 'inputField'],
			'banner_settings_modal',
			'Text of "reject" button on the settings modal.'
		);

		$this->settingsUtil->addSettingsField(
			'banner_settings_buttons_accept',
			'Accept Button',
			[$this, 'inputField'],
			'banner_settings_modal',
			'Text of "accept" button on the settings modal.'
		);

		$this->settingsUtil->addSettingsField(
			'consent_types',
			'Consent Types',
			[$this, 'consentEventParametersFields'],
			'consent_settings',
			'Name of consent event emitted to GTM container.',
			['type' => 'array']
		);

		   $defaults = [
			'banner_display_mode' => 'bar',
			'banner_display_wall' => 0,
			'banner_title' => 'Cookies Policy',
			'banner_description' => 'We are using various cookies files. Learn more in our [privacy policy](#) and make your choice.',
			'banner_buttons_accept' => 'Accept all',
			'banner_buttons_settings' => 'Settings',
			'banner_settings_title' => 'Cookies Settings',
			'banner_settings_description' => 'In order to provide you with best experience we use various...',
			'banner_settings_buttons_save' => 'Save preferences',
			'banner_settings_buttons_reject' => 'Reject',
			'banner_settings_buttons_accept' => 'Accept all',
			'consent_types' => [
				[
					'name' => 'analytics_storage',
					'title' => 'Analytics storage',
					'description' => 'Enables storage, such as cookies, related to analytics (for example, visit duration)',
					'default' => 'denied'
				], [
					'name' => 'ad_storage',
					'title' => 'Ads storage',
					'description' => 'Enables storage, such as cookies, related to advertising [link](https =>//www.google.com)',
					'default' => 'denied'
				], [
					'name' => 'ad_user_data',
					'title' => 'User Data',
					'description' => 'Sets consent for sending user data to Google for online advertising purposes.',
					'default' => 'denied'
				], [
					'name' => 'ad_personalization',
					'title' => 'Personalization',
					'description' => 'Sets consent for personalized advertising.',
					'default' => 'denied'
				]
			]
		];

		   foreach ($defaults as $defOpt => $defVal) {
			   if ( false === $this->settingsUtil->getOption($defOpt) ) {
				   $this->settingsUtil->updateOption($defOpt, $defVal);
			   }
		   }
	}

	public function consentEventParametersFields( $args): void {
		$fieldsConfiguration = [
			'name' => [
				'renderMethodName' => 'inputField',
				'placeholder' => 'Internal Consent Type Name',
				'type' => 'text',
			],
			'title' => [
				'renderMethodName' => 'inputField',
				'placeholder' => 'User facing title',
				'type' => 'text',
			],
			'description' => [
				'renderMethodName' => 'textareaField',
				'placeholder' => 'Detailed description of what those cookies are used for.',
				'rows' => 6,
			],
			'default' => [
				'renderMethodName' => 'selectField',
				'placeholder' => 'Default state',
				'options' => ['denied' => 'denied', 'granted' => 'granted'],
			],
		];

		$consentEventParameters = $this->settingsUtil->getOption('consent_types');

		if (false === is_array($consentEventParameters)) {
			$consentEventParameters = [];
		}

		$hasEmptyRow = false;

		foreach ($consentEventParameters as $consentEventParameter) {
			if (false === isset($consentEventParameter['name']) || true === empty($consentEventParameter['name'])) {
				$hasEmptyRow = true;
				break;
			}
		}

		if (false === $hasEmptyRow) {
			$consentEventParameters[] = [
				'name' => '',
				'title' => '',
				'description' => '',
				'default' => '',
			];
		}

		echo '<table style="width: 100%;">';
		echo '<th>';
		echo '<tr>';
		echo '<td><strong>Consent Type<strong></td><td>Title</td><td>Description</td><td>Default</td>';
		echo '</tr>';
		echo '</th>';
		foreach ($consentEventParameters as $index => $parameterConfig) {
			echo '<tr>';
			foreach ($parameterConfig as $name => $value) {
				$fieldName = sprintf('%s[%d][%s]', $args['label_for'], $index, $name);
				$fieldConfiguration = $fieldsConfiguration[$name] ?? null;

				if (null === $fieldConfiguration) {
					continue;
				}
				echo '<td style="vertical-align: top">';
				$this->{$fieldConfiguration['renderMethodName']}(array_merge($fieldConfiguration, [
					'label_for' => $fieldName,
					'value' => $value,
				]));
				echo '</td>';
			}
			echo '<td><a href="#" class="consent-event-parameter-remove" onclick="this.parentNode.parentNode.remove();">remove</a></td>';
			echo '</tr>';
		}
		echo '</table>';
	}

	public function checkboxField( $args ): void {
		// Get the value of the setting we've registered with register_setting()
		$value = $args['value'] ?? get_option( $args['label_for'] );
		?>
		<input
			type="checkbox"
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			name="<?php echo esc_attr( $args['label_for'] ); ?>"
			<?php if (true === @$args['disabled']) : ?>
				disabled="disabled"
			<?php endif; ?>
			<?php if (@$args['title']) : ?>
				title="<?php echo esc_attr($args['title']); ?>"
			<?php endif; ?>
			value="1"
			<?php checked( $value, 1 ); ?> />
		<?php if (@$args['description']) : ?>
		<p class="description">
			<?php echo wp_kses($args['description'], SanitizationUtil::WP_KSES_ALLOWED_HTML, SanitizationUtil::WP_KSES_ALLOWED_PROTOCOLS); ?>
		</p>
		<?php endif; ?>
		<?php
	}

	public function selectField( $args ): void {
		// Get the value of the setting we've registered with register_setting()
		$selectedValue = $args['value'] ?? get_option( $args['label_for'] );
		?>
		<select
			type="checkbox"
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			name="<?php echo esc_attr( $args['label_for'] ); ?>"
			<?php if (true === @$args['disabled']) : ?>
				disabled="disabled"
			<?php endif; ?>
		>
			<?php foreach ($args['options'] as $value => $label) : ?>
				<option value="<?php echo esc_attr($value); ?>"
					<?php if ($selectedValue == $value) : ?>
						selected
					<?php endif; ?>
				><?php echo esc_html($label); ?></option>
			<?php endforeach ?>
		</select>
		<?php if (isset($args['description'])) : ?>
		<p class="description">
			<?php echo wp_kses($args['description'], SanitizationUtil::WP_KSES_ALLOWED_HTML, SanitizationUtil::WP_KSES_ALLOWED_PROTOCOLS); ?>
		</p>
		<?php endif; ?>
		<?php
	}


	public function textareaField( $args ): void {
		// Get the value of the setting we've registered with register_setting()
		$value = $args['value'] ?? get_option( $args['label_for'] );
		?>
		<textarea
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			class="large-text code"
			rows="<?php echo esc_html( $args['rows'] ); ?>"
			placeholder="<?php echo esc_html( @$args['placeholder'] ); ?>"
			name="<?php echo esc_attr( $args['label_for'] ); ?>"><?php echo wp_kses($value, SanitizationUtil::WP_KSES_ALLOWED_HTML, SanitizationUtil::WP_KSES_ALLOWED_PROTOCOLS); ?></textarea>
		<?php if (isset($args['description'])) : ?>
		<p class="description">
			<?php echo esc_html( $args['description'] ); ?>
		</p>
		<?php endif; ?>
		<?php
	}

	public function inputField( $args ): void {
		// Get the value of the setting we've registered with register_setting()
		$value = $args['value'] ?? get_option( $args['label_for'] );
		?>
		<input
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			class="large-text code"
			type="<?php echo esc_html( $args['type'] ); ?>"
			<?php if (true === @$args['disabled']) : ?>
				disabled="disabled"
			<?php endif; ?>
			value="<?php echo esc_html($value); ?>"
			placeholder="<?php echo esc_html( @$args['placeholder'] ); ?>"
			name="<?php echo esc_attr( $args['label_for'] ); ?>" />
		<?php if (isset($args['description'])) : ?>
		<p class="description">
			<?php echo esc_html( $args['description'] ); ?>
		</p>
		<?php endif; ?>
		<?php
	}

	public function optionsPage(): void {
		$this->settingsUtil->addSubmenuPage(
			'options-general.php',
			'Consent Mode Banner',
			'Consent Mode Banner',
			'manage_options'
		);
	}
}
