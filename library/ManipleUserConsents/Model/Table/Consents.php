<?php

/**
 * @method ManipleUserConsents_Model_Consent findRow(mixed $id)
 * @method ManipleUserConsents_Model_Consent createRow(array $data = array(), string $defaultSource = null)
 * @method ManipleUserConsents_Model_Rowset_Consents find(mixed $key, mixed ...$keys)
 * @method ManipleUserConsents_Model_Rowset_Consents fetchAll(string|array|Zend_Db_Table_Select $where = null, string|array $order = null, int $count = null, int $offset = null)
 */
class ManipleUserConsents_Model_Table_Consents extends Zefram_Db_Table
{
    const className = __CLASS__;

    protected $_rowClass = ManipleUserConsents_Model_Consent::className;

    protected $_rowsetClass = ManipleUserConsents_Model_Rowset_Consents::className;

    protected $_name = 'consents';

    protected $_primary = 'consent_id';

    protected $_referenceMap = array(
        'LatestVersion' => array(
            'columns'       => 'latest_version_id',
            'refTableClass' => ManipleUserConsents_Model_Table_ConsentVersions::className,
            'refColumns'    => 'latest_version_id',
        ),
        'LatestMajorVersion' => array(
            'columns'       => 'major_version_id',
            'refTableClass' => ManipleUserConsents_Model_Table_ConsentVersions::className,
            'refColumns'    => 'latest_version_id',
        ),
    );
}
