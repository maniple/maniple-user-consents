<?php

/**
 * @property ManipleUser_Model_User $User
 * @property ManipleUserConsents_Model_ConsentVersion $ConsentVersion
 * @method ManipleUserConsents_Model_Table_UserConsents getTable()
 */
class ManipleUserConsents_Model_UserConsent extends Zefram_Db_Table_Row
{
    const className = __CLASS__;

    const STATE_ACCEPTED = 'ACCEPTED';
    const STATE_DECLINED = 'DECLINED';
    const STATE_REVOKED  = 'REVOKED';

    protected $_tableClass = ManipleUserConsents_Model_Table_UserConsents::className;

    /**
     * @return bool
     */
    public function isAccepted()
    {
        return $this->state === self::STATE_ACCEPTED;
    }

    /**
     * @return bool
     */
    public function isDeclined()
    {
        return $this->state === self::STATE_DECLINED;
    }

    /**
     * @return bool
     */
    public function isRevoked()
    {
        return $this->state === self::STATE_REVOKED;
    }
}
