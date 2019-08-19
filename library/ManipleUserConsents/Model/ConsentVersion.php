<?php

/**
 * @property ManipleUserConsents_Model_Consent $Consent
 * @property ManipleUserConsents_Model_ConsentVersion|null $MajorVersion
 * @method ManipleUserConsents_Model_Table_ConsentVersions getTable()
 */
class ManipleUserConsents_Model_ConsentVersion extends Zefram_Db_Table_Row
{
    const className = __CLASS__;

    protected $_tableClass = ManipleUserConsents_Model_Table_ConsentVersions::className;

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->consent_version_id;
    }

    /**
     * @return bool
     */
    public function isMajorVersion()
    {
        return $this->major_version_id === $this->consent_version_id;
    }

    /**
     * @return int
     */
    public function getMajorVersionId()
    {
        return (int) $this->major_version_id;
    }

    public function save()
    {
        $this->updated_at = time();

        if (!$this->isStored()) {
            $this->created_at = $this->updated_at;
        }

        $result = null;

        // In order to make major_version_id reference this row, we need
        // to save it, to get its primary key value
        if (!$this->consent_version_id) {
            $result = parent::save();
        }

        // Ensure empty major_version_id references this row
        if (!$this->major_version_id) {
            $this->major_version_id = $this->consent_version_id;
            $result = null;
        }

        return $result ? $result : parent::save();
    }
}
