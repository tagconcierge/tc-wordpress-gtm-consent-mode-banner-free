<?php

namespace TagConcierge\GtmCookiesFree\Service;

use TagConcierge\GtmCookiesFree\Util\SanitizationUtil;
use TagConcierge\GtmCookiesFree\Util\SettingsUtil;

class SettingsService
{
    private $settingsUtil;

    public function __construct(SettingsUtil $settingsUtil)
    {
        $this->settingsUtil = $settingsUtil;


        $this->initialize();
    }

    private function initialize(): void
    {
        $this->settingsUtil->addTab(
            'settings',
            'Settings'
        );

        $this->settingsUtil->addTab(
            'event_settings',
            'Cookies Categories'
        );

        add_action( 'admin_init', [$this, 'settingsInit'] );
        add_action( 'admin_menu', [$this, 'optionsPage'] );
    }

    public function settingsInit(): void
    {
        $this->settingsUtil->addSettingsSection(
            'basic',
            'Basic Settings',
            'GTM Cookies allows to quickly deploy robust, privacy-oriented setup using Google Tag Manager and dataLayer. [LINK TO DOCS]',
            'settings'
        );

        $this->settingsUtil->addSettingsSection(
            'gtm_snippet',
            'Google Tag Manager snippet',
            'Paste two snippets provided by GTM. To find those snippets navigate to `Admin` tab in GTM console and click `Install Google Tag Manager`. If you already implemented GTM snippets in your page, paste them below, but select appropriate `Prevent loading GTM Snippet` option.',
            'settings'
        );

        $this->settingsUtil->addSettingsSection(
            'consent_event',
            'Consent event',
            'Consent event is pushed to DataLayer when user decides on their cookies preference. This event will contain detailed consent information for each cookie category. [LINK TO DOCS]',
            'settings'
        );

        $this->settingsUtil->addSettingsSection(
            'consent_event_parameters',
            'Cookies Categories',
            'Use the table below to specify all categories of cookies and tools that your website use. Each entry consist of title, detailed description, option to make it required and name of the parameter being pushed to GTM DataLayer.',
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
            'gtm_snippet_consent_required',
            'Prevent loading GTM container before consent?',
            [$this, 'checkboxField'],
            'gtm_snippet',
            'When checked the GTM container won\'t load before user provides information about their consent. Only after any settings on the cookies banner are saved the GTM will be initated. It ensures no 3rd party tools and cookies are loaded before user consents. [LINK TO DOCS]'
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
            'consent_event_name',
            'Consent event name',
            [$this, 'inputField'],
            'consent_event',
            'Name of the consent event emitted to GTM container.',
            ['type' => 'text', 'placeholder' => 'user_consent']
        );

        $this->settingsUtil->addSettingsField(
            '_TODO_repush_events',
            'Defer events?',
            [$this, 'checkboxField'],
            'consent_event',
            'Select to use with eCommerce events or any tags that rely on both consent event and GTM Variable Version 1 [LINK TO DOCS].'
        );
        // Tags that rely on both consent event and variables may not receive correct values when events are not pushed

        $this->settingsUtil->addSettingsField(
            'consent_event_parameters',
            'Consent event parameters',
            [$this, 'consentEventParametersFields'],
            'consent_event_parameters',
            'Name of consent event emitted to GTM container.',
            ['type' => 'array']
        );
    }

    public function consentEventParametersFields($args): void
    {
        $fieldsConfiguration = [
            'name' => [
                'renderMethodName' => 'inputField',
                'description' => 'Cookies category title',
                'type' => 'text',
                'placeholder' => 'Marketing',
            ],
            'description' => [
                'renderMethodName' => 'textareaField',
                'description' => 'Detailed description of what those cookies are used for.',
                'rows' => 6,
            ],
            'is_required' => [
                'renderMethodName' => 'selectField',
                'description' => 'Is this consent mandatory?',
                'options' => [
                    '0' => 'optional',
                    '1' => 'required',
                ]
            ],
            'data_layer_name' => [
                'renderMethodName' => 'inputField',
                'description' => 'Parameter passed to dataLayer.',
                'type' => 'text',
                'placeholder' => 'marketing_consent',
            ],
        ];
        $consentEventParameters = $this->settingsUtil->getOption('consent_event_parameters');

        if (false === $consentEventParameters) {
            $consentEventParameters = [
                [
                    'name' => 'Marketing consent',
                    'description' => 'Consent to marketing purposes.',
                    'is_required' => '0',
                    'data_layer_name' => 'marketing_consent',
                ]
            ];
        }

        if (false === is_array($consentEventParameters)) {
            $consentEventParameters = [];
        }

        $hasEmptyRow = false;

        foreach ($consentEventParameters as $consentEventParameter) {
            if (false === isset($consentEventParameter['data_layer_name']) || true === empty($consentEventParameter['data_layer_name'])) {
                $hasEmptyRow = true;
                break;
            }
        }

        if (false === $hasEmptyRow) {
            $consentEventParameters[] = [
                'name' => '',
                'description' => '',
                'is_required' => '0',
                'data_layer_name' => '',
            ];
        }

        echo '<table style="width: 100%;">';
        foreach ($consentEventParameters as $index => $parameterConfig) {
            echo '<tr>';
            foreach ($parameterConfig as $name => $value) {
                $fieldName = sprintf('%s[%d][%s]', $args['label_for'], $index, $name);
                $fieldConfiguration = $fieldsConfiguration[$name] ?? null;

                if (null === $fieldConfiguration) {
                    continue;
                }
                echo '<td>';
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

    public function checkboxField( $args ): void
    {
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
        <p class="description">
            <?php echo wp_kses($args['description'], SanitizationUtil::WP_KSES_ALLOWED_HTML, SanitizationUtil::WP_KSES_ALLOWED_PROTOCOLS); ?>
        </p>
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
        <p class="description">
            <?php echo wp_kses($args['description'], SanitizationUtil::WP_KSES_ALLOWED_HTML, SanitizationUtil::WP_KSES_ALLOWED_PROTOCOLS); ?>
        </p>
        <?php
    }


    public function textareaField( $args ): void
    {
        // Get the value of the setting we've registered with register_setting()
        $value = $args['value'] ?? get_option( $args['label_for'] );
        ?>
        <textarea
            id="<?php echo esc_attr( $args['label_for'] ); ?>"
            class="large-text code"
            rows="<?php echo esc_html( $args['rows'] ); ?>"
            name="<?php echo esc_attr( $args['label_for'] ); ?>"><?php echo wp_kses($value, SanitizationUtil::WP_KSES_ALLOWED_HTML, SanitizationUtil::WP_KSES_ALLOWED_PROTOCOLS); ?></textarea>
        <p class="description">
            <?php echo esc_html( $args['description'] ); ?>
        </p>
        <?php
    }

    public function inputField( $args ): void
    {
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
            placeholder="<?php echo esc_html( $args['placeholder'] ); ?>"
            name="<?php echo esc_attr( $args['label_for'] ); ?>" />
        <p class="description">
            <?php echo esc_html( $args['description'] ); ?>
        </p>
        <?php
    }

    public function optionsPage(): void
    {
        $this->settingsUtil->addSubmenuPage(
            'options-general.php',
            'GTM Cookies',
            'GTM Cookies',
            'manage_options'
        );
    }
}
