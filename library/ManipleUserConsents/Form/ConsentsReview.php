<?php

class ManipleUserConsents_Form_ConsentsReview extends Zefram_Form2
{
    public function __construct(ManipleCore_Settings_SettingsManager $settingsManager)
    {
        $options = array(
            'prefixPath' => array(
                array(
                    'prefix' => 'ModEdu_Form_',
                    'path' => __DIR__ . '/../../../../mod-edu/library/Form',
                ),
            ),
            'elements' => array(
                'title' => array(
                    'type' => 'text',
                    'options' => array(
                        'required' => true,
                        'label' => 'Page title',
                    ),
                ),
                'body' => array(
                    'type' => 'richText',
                    'options' => array(
                        'label' => 'Body text',
                    ),
                ),
            ),
            'defaults' => array(
                'title' => $settingsManager->get(ManipleUserConsents_ConsentsReview::TITLE_SETTING),
                'body'  => $settingsManager->get(ManipleUserConsents_ConsentsReview::BODY_SETTING),
            ),
        );

        parent::__construct($options);
    }
}
