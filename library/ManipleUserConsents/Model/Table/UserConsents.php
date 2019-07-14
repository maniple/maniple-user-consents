<?php

/**
 * @method ManipleUserConsents_Model_UserConsent findRow(mixed $id)
 * @method ManipleUserConsents_Model_UserConsent createRow(array $data = array(), string $defaultSource = null)
 * @method ManipleUserConsents_Model_Rowset_UserConsents find(mixed $key, mixed ...$keys)
 * @method ManipleUserConsents_Model_Rowset_UserConsents fetchAll(string|array|Zend_Db_Table_Select $where = null, string|array $order = null, int $count = null, int $offset = null)
 */
class ManipleUserConsents_Model_Table_UserConsents extends Zefram_Db_Table
{
    const className = __CLASS__;

    protected $_rowClass = ManipleUserConsents_Model_UserConsent::className;

    protected $_rowsetClass = ManipleUserConsents_Model_Rowset_UserConsents::className;

    protected $_name = 'user_consents';

    protected $_primary = 'user_consent_id';

    protected $_referenceMap = array(
        'User' => array(
            'columns'       => 'user_id',
            'refColumns'    => 'user_id',
            'refTableClass' => ManipleUser_Model_DbTable_Users::className,
        ),
        'ConsentVersion' => array(
            'columns'       => 'consent_version_id',
            'refColumns'    => 'consent_version_id',
            'refTableClass' => ManipleUserConsents_Model_Table_ConsentVersions::className,
        ),
    );
}
