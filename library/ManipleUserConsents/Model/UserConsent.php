<?php

/**
 * @property ManipleUser_Model_User $User
 * @property ManipleUserConsents_Model_ConsentVersion $ConsentVersion
 * @method ManipleUserConsents_Model_Table_UserConsents getTable()
 */
class ManipleUserConsents_Model_UserConsent extends Zefram_Db_Table_Row
{
    const className = __CLASS__;

    protected $_tableClass = ManipleUserConsents_Model_Table_UserConsents::className;

    /**
     * @return bool
     */
    public function getDecision()
    {
        return (bool) $this->decision;
    }

    /**
     * @return bool
     */
    public function isGranted()
    {
        return $this->getDecision() && !$this->isRevoked();
    }

    /**
     * @return bool
     */
    public function isDeclined()
    {
        return !$this->getDecision() && !$this->isRevoked();
    }

    /**
     * @return bool
     */
    public function isRevoked()
    {
        return $this->revoked_at !== null;
    }

    /**
     * @return bool
     */
    public function isMissing()
    {
        return $this->ConsentVersion->isRequired() && !$this->isGranted();
    }
}
