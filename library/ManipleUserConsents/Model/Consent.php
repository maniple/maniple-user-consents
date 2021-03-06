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
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    /**
     * @return int
     */
    public function getLatestVersionId()
    {
        return (int) $this->latest_version_id;
    }

    /**
     * @return int
     */
    public function getLatestMajorVersionId()
    {
        return (int) $this->latest_major_version_id;
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

    /**
     * @return int
     */
    public function getDisplayPriority()
    {
        return (int) $this->display_priority;
    }

    public function save()
    {
        $this->updated_at = time();

        if (!$this->isStored()) {
            $this->created_at = $this->updated_at;
        }

        return parent::save();
    }
}
