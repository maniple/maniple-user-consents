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
    'maniple-user-consents.user-consents.update' => array(
        'route'    => 'user-consents/update',
        'defaults' => array(
            'module'     => 'maniple-user-consents',
            'controller' => 'user-consents',
            'action'     => 'update',
        ),
    ),
);
