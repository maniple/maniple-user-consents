<?php

/**
 * @method ManipleUserConsents_Model_ConsentVersion findRow(mixed $id)
 * @method ManipleUserConsents_Model_ConsentVersion createRow(array $data = array(), string $defaultSource = null)
 * @method ManipleUserConsents_Model_Rowset_ConsentVersions find(mixed $key, mixed ...$keys)
 * @method ManipleUserConsents_Model_Rowset_ConsentVersions fetchAll(string|array|Zend_Db_Table_Select $where = null, string|array $order = null, int $count = null, int $offset = null)
 */
class ManipleUserConsents_Model_Table_ConsentVersions extends Zefram_Db_Table
{
    const className = __CLASS__;

    protected $_rowClass = ManipleUserConsents_Model_ConsentVersion::className;

    protected $_rowsetClass = ManipleUserConsents_Model_Rowset_ConsentVersions::className;

    protected $_name = 'consent_versions';

    protected $_primary = 'consent_version_id';

    protected $_referenceMap = array(
        'Consent' => array(
            'columns'       => 'consent_id',
            'refColumns'    => 'consent_id',
            'refTableClass' => ManipleUserConsents_Model_Table_Consents::className,
        ),
    );
}
