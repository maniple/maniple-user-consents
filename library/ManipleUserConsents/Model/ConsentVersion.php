<?php

/**
 * @property ManipleUserConsents_Model_Consent $Consent
 * @method ManipleUserConsents_Model_Table_ConsentVersions getTable()
 */
class ManipleUserConsents_Model_ConsentVersion extends Zefram_Db_Table_Row
{
    const className = __CLASS__;

    protected $_tableClass = ManipleUserConsents_Model_Table_ConsentVersions::className;

    public function getId()
    {
        return (int) $this->consent_version_id;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return (bool) $this->is_required;
    }
}