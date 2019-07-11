<?php

/**
 * @method ManipleUserConsents_Model_Table_UserConsents getTable()
 */
class ManipleUserConsents_Model_UserConsent extends Zefram_Db_Table_Row
{
    const className = __CLASS__;

    protected $_tableClass = ManipleUserConsents_Model_Table_UserConsents::className;

    public function isGranted()
    {
        return $this->revoked_at === null;
    }

    public function isRevoked()
    {
        return $this->revoked_at !== null;
    }
}
