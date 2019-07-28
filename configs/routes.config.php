<?php

return array(
    'maniple-user-consents.consents.index' => array(
        'route'    => 'consents',
        'defaults' => array(
            'module'     => 'maniple-user-consents',
            'controller' => 'consents',
            'action'     => 'index',
        ),
    ),
    'maniple-user-consents.consents.create' => array(
        'route'    => 'consents/create',
        'defaults' => array(
            'module'     => 'maniple-user-consents',
            'controller' => 'consents',
            'action'     => 'create',
        ),
    ),
    'maniple-user-consents.consents.edit' => array(
        'route'    => 'consents/:consent_id/edit',
        'defaults' => array(
            'module'     => 'maniple-user-consents',
            'controller' => 'consents',
            'action'     => 'edit',
        ),
    ),
    'maniple-user-consents.consents-review.index' => array(
        'route'    => 'consents-review',
        'defaults' => array(
            'module'     => 'maniple-user-consents',
            'controller' => 'consents-review',
            'action'     => 'index',
        ),
    ),
    'maniple-user-consents.consents-review.edit' => array(
        'route'    => 'consents-review/edit',
        'defaults' => array(
            'module'     => 'maniple-user-consents',
            'controller' => 'consents-review',
            'action'     => 'edit',
        ),
    ),
);
