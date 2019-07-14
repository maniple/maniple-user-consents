<?php

return array(
    'ManipleUserConsents_ConsentManager' => array(
        'class' => 'ManipleUserConsents_ConsentManager',
    ),
    'ManipleUserConsents_Controller_Plugin_UserConsentsGuard' => array(
        'class' => 'ManipleUserConsents_Controller_Plugin_UserConsentsGuard',
    ),

    'ManipleUserConsents_Model_Table_Consents' => array(
        'callback' => 'Maniple_Model_TableProvider::getTable',
        'args' => 'ManipleUserConsents_Model_Table_Consents',
    ),
    'ManipleUserConsents_Model_Table_ConsentVersions' => array(
        'callback' => 'Maniple_Model_TableProvider::getTable',
        'args' => 'ManipleUserConsents_Model_Table_ConsentVersions',
    ),
    'ManipleUserConsents_Model_Table_UserConsents' => array(
        'callback' => 'Maniple_Model_TableProvider::getTable',
        'args' => 'ManipleUserConsents_Model_Table_UserConsents',
    ),
);
