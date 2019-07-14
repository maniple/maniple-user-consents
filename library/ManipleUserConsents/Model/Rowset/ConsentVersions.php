<?php

/**
 * @method bool setTable(ManipleUserConsents_Model_Table_ConsentVersions $table)
 * @method ManipleUserConsents_Model_Table_ConsentVersions getTable()
 * @method ManipleUserConsents_Model_ConsentVersion|null current()
 * @method ManipleUserConsents_Model_ConsentVersion offsetGet(string $offset)
 * @method ManipleUserConsents_Model_ConsentVersion getRow(int $position, $seek = false)
 */
class ManipleUserConsents_Model_Rowset_ConsentVersions extends Zefram_Db_Table_Rowset
{
    const className = __CLASS__;

    protected $_tableClass = ManipleUserConsents_Model_Table_ConsentVersions::className;

    protected $_rowClass = ManipleUserConsents_Model_ConsentVersion::className;
}
