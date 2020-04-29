<?php

class ManipleUserConsents_Form_Consent extends Zefram_Form2
{
    public function __construct(ManipleUserConsents_Model_Consent $consent = null)
    {
        $options = array(
            'elements' => array(
                'title' => array(
                    'type' => 'text',
                    'options' => array(
                        'required' => true,
                        'label' => 'Title',
                    ),
                ),
                'body' => array(
                    'type' => 'editor',
                    'options' => array(
                        'required' => true,
                        'label' => 'Consent text',
                    ),
                ),
                'is_required' => array(
                    'type' => 'checkbox',
                    'options' => array(
                        'label'       => 'Required',
                        'description' => 'When checked users have to accept this consent in order to use the page',
                    ),
                ),
                'is_active' => array(
                    'type' => 'checkbox',
                    'options' => array(
                        'label'       => 'Active',
                        'description' => 'When checked this consent will be shown in user sign up form and on consents update page',
                    ),
                ),
                'display_priority' => array(
                    'type' => 'select',
                    'options' => array(
                        'label' => 'Display priority',
                        'description' => 'Higher value moves the consent up on the list',
                        'multioptions' => array(0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5),
                    ),
                ),
            ),
        );

        foreach ($options['elements'] as &$element) {
            if ($element['type'] === 'checkbox') {
                $element['options']['type'] = 'checkbox';
            }
        }
        unset($element);

        if ($consent) {
            $options['defaults'] = array(
                'title'            => $consent->getTitle(),
                'body'             => $consent->getBody(),
                'is_required'      => (int) $consent->isRequired(),
                'is_active'        => (int) $consent->isActive(),
                'display_priority' => $consent->getDisplayPriority(),
            );
        }

        parent::__construct($options);

        /** @var ManipleEditor_Form_Element_Editor $bodyElement */
        $bodyElement = $this->getElement('body');
        $bodyElement->prependFilter(
            new Zefram_Filter(array(
                array('PregReplace', array(
                    'match'   => '/\s(href=[\'"])/',
                    'replace' => ' target="_blank" $1',
                )),
                new ManipleUserConsents_Filter_RelativizeHrefs(),
            ))
        );
    }
}
