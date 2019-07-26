<?php

/**
 * @property ManipleUserConsents_Model_ConsentVersion $LatestVersion
 * @property ManipleUserConsents_Model_ConsentVersion $LatestMajorVersion
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

    /**
     * @return bool
     */
    public function isRequired()
    {
        return (bool) $this->is_required;
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->LatestVersion ? $this->LatestVersion->title : null;
    }

    /**
     * @return string|null
     */
    public function getBody()
    {
        return $this->LatestVersion ? $this->LatestVersion->body : null;
    }
}
