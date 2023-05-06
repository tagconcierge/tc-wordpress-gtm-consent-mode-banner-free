<?php

namespace TagConcierge\GtmCookiesFree\Util;

use TagConcierge\GtmCookiesFree\GtmCookiesFree;

class SettingsUtil
{
    /** @var string */
    protected $snakeCaseNamespace;
    /** @var string */
    protected $spineCaseNamespace;
    /** @var array */
    protected $tabs = [];
    /** @var array */
    protected $sections = [];

    public function __construct()
    {
        $this->snakeCaseNamespace = GtmCookiesFree::SNAKE_CASE_NAMESPACE;
        $this->spineCaseNamespace = GtmCookiesFree::SPINE_CASE_NAMESPACE;
    }

    public function getOption( $optionName) {
        return get_option($this->snakeCaseNamespace . '_' . $optionName);
    }

    public function deleteOption( $optionName): bool
    {
        return delete_option($this->snakeCaseNamespace . '_' . $optionName);
    }

    public function updateOption( $optionName, $optionValue): bool
    {
        return update_option($this->snakeCaseNamespace . '_' . $optionName, $optionValue);
    }

    public function registerSetting( $settingName): void {
        register_setting( $this->snakeCaseNamespace, $this->snakeCaseNamespace . '_' . $settingName );
    }

    public function addTab( $tabName, $tabTitle, $showSaveButton = true): void {
        $this->tabs[$tabName] = [
            'name' => $tabName,
            'title' => $tabTitle,
            'show_save_button' => $showSaveButton
        ];
    }

    public function addSettingsSection( $sectionName, $sectionTitle, $description, $tab): void {
        $this->sections[$sectionName] = [
            'name' => $sectionName,
            'tab' => $tab
        ];
        add_settings_section(
            $this->snakeCaseNamespace . '_' . $sectionName,
            __( $sectionTitle, $this->spineCaseNamespace ),
            static function( $args) use ( $description) {
                ?>

                <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php echo wp_kses($description, SanitizationUtil::WP_KSES_ALLOWED_HTML, SanitizationUtil::WP_KSES_ALLOWED_PROTOCOLS); ?></p>
                <?php
            },
            $this->snakeCaseNamespace . '_' . $tab
        );
    }

    public function addSettingsField( $fieldName, $fieldTitle, $fieldCallback, $fieldSection, $fieldDescription = '', $extraAttrs = []) : void {
        $attrs = array_merge([
            'label_for'   => $this->snakeCaseNamespace . '_' . $fieldName,
            'description' => $fieldDescription,
        ], $extraAttrs);
        $section = $this->sections[$fieldSection];
        register_setting( $this->snakeCaseNamespace . '_' . $section['tab'], $this->snakeCaseNamespace . '_' . $fieldName );
        add_settings_field(
            $this->snakeCaseNamespace . '_' . $fieldName, // As of WP 4.6 this value is used only internally.
            // Use $args' label_for to populate the id inside the callback.
            __( $fieldTitle, $this->spineCaseNamespace ),
            $fieldCallback,
            $this->snakeCaseNamespace . '_' . $section['tab'],
            $this->snakeCaseNamespace . '_' . $fieldSection,
            $attrs
        );
    }

    public function addSubmenuPage( $options, $title1, $title2, $capabilities) : void {
        $snakeCaseNamespace = $this->snakeCaseNamespace;
        $spineCaseNamespace = $this->spineCaseNamespace;
        $activeTab = isset( $_GET[ 'tab' ] ) ? sanitize_key($_GET[ 'tab' ]) : array_keys($this->tabs)[0];
        add_submenu_page(
            $options,
            $title1,
            $title2,
            $capabilities,
            $this->spineCaseNamespace,
            function() use ( $capabilities, $snakeCaseNamespace, $spineCaseNamespace, $activeTab) {
                // check user capabilities
                if ( ! current_user_can( $capabilities ) ) {
                    return;
                }
                // show error/update messages
                settings_errors( $snakeCaseNamespace . '_messages' );
                ?>
                <div class="wrap">
                    <div id="icon-themes" class="icon32"></div>
                    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

                    <h2 class="nav-tab-wrapper">
                        <?php foreach ($this->tabs as $tab) : ?>
                            <a
                                href="<?php echo esc_url(sprintf('?page=%s&tab=%s', $this->spineCaseNamespace, $tab['name'])); ?>"
                                class="nav-tab
						<?php if ($activeTab === $tab['name']) : ?>
							nav-tab-active
						<?php endif; ?>
					"><?php echo wp_kses($tab['title'], SanitizationUtil::WP_KSES_ALLOWED_HTML, SanitizationUtil::WP_KSES_ALLOWED_PROTOCOLS); ?></a>
                        <?php endforeach; ?>
                    </h2>

                    <form action="options.php" method="post">
                        <?php
                        // output security fields for the registered setting "wporg_options"
                        settings_fields( $snakeCaseNamespace . '_' . $activeTab );
                        // output setting sections and their fields
                        // (sections are registered for "wporg", each field is registered to a specific section)
                        do_settings_sections( $snakeCaseNamespace . '_' . $activeTab );
                        // output save settings button
                        if (false !== $this->tabs[$activeTab]['show_save_button']) {
                            submit_button( __( 'Save Settings', $spineCaseNamespace ) );
                        }
                        ?>
                    </form>
                </div>
                <?php
            }
        );
    }
}
