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
     * @return int
     */
    public function getId()
    {
        return (int) $this->user_consent_id;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return (bool) $this->is_required;
    }

    /**
     * @return bool
     */
    public function isAccepted()
    {
        return $this->state === self::STATE_ACCEPTED;
    }

    /**
     * @return $this
     */
    public function setAccepted()
    {
        return $this->_setState(self::STATE_ACCEPTED);
    }

    /**
     * @return bool
     */
    public function isDeclined()
    {
        return $this->state === self::STATE_DECLINED;
    }

    /**
     * @return $this
     */
    public function setDeclined()
    {
        return $this->_setState(self::STATE_DECLINED);
    }

    /**
     * @return bool
     */
    public function isRevoked()
    {
        return $this->state === self::STATE_REVOKED;
    }

    /**
     * @return $this
     */
    public function setRevoked()
    {
        return $this->_setState(self::STATE_REVOKED);
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return $this
     */
    protected function _setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return int
     */
    public function getDisplayPriority()
    {
        return (int) $this->display_priority;
    }

    /**
     * @return int
     */
    public function getSavedAt()
    {
        return (int) $this->saved_at;
    }

    public function save()
    {
        $this->saved_at = time();

        return parent::save();
    }
}
