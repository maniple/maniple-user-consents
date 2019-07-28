<?php

/**
 * @method ManipleUserConsents_Model_UserConsentState createRow(array $data = array(), string $defaultSource = null)
 * @method ManipleUserConsents_Model_UserConsentState|null fetchRow(string|array|Zend_Db_Table_Select $where = null, string|array $order = null, int $offset = null)
 * @method ManipleUserConsents_Model_UserConsentState|null findRow(mixed $id)
 * @method ManipleUserConsents_Model_Rowset_UserConsentStates find(mixed $key, mixed ...$keys)
 * @method ManipleUserConsents_Model_Rowset_UserConsentStates fetchAll(string|array|Zend_Db_Table_Select $where = null, string|array $order = null, int $count = null, int $offset = null)
 */
class ManipleUserConsents_Model_Table_UserConsentStates extends Zefram_Db_Table
{
    const className = __CLASS__;

    protected $_rowClass = ManipleUserConsents_Model_UserConsentState::className;

    protected $_rowsetClass = ManipleUserConsents_Model_Rowset_UserConsentStates::className;

    protected $_name = 'user_consent_states';

    protected $_referenceMap = array(
        'UserConsent' => array(
            'columns'       => 'user_consent_id',
            'refColumns'    => 'user_consent_id',
            'refTableClass' => ManipleUserConsents_Model_Table_UserConsents::className,
        ),
    );
}
