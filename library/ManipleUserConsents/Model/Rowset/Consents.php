<?php

/**
 * @method bool setTable(ManipleUserConsents_Model_Table_Consents $table)
 * @method ManipleUserConsents_Model_Table_Consents getTable()
 * @method ManipleUserConsents_Model_Consent|null current()
 * @method ManipleUserConsents_Model_Consent offsetGet(string $offset)
 * @method ManipleUserConsents_Model_Consent getRow(int $position, $seek = false)
 */
class ManipleUserConsents_Model_Rowset_Consents extends Zefram_Db_Table_Rowset
{
    const className = __CLASS__;

    protected $_tableClass = ManipleUserConsents_Model_Table_Consents::className;

    protected $_rowClass = ManipleUserConsents_Model_Consent::className;
}
