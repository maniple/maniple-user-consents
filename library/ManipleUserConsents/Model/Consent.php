<?php

/**
 * @method ManipleUserConsents_Model_Table_Consents getTable()
 */
class ManipleUserConsents_Model_Consent extends Zefram_Db_Table_Row
{
    const className = __CLASS__;

    protected $_tableClass = ManipleUserConsents_Model_Table_Consents::className;

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->consent_id;
    }
}
