<?php

/**
 * @method bool setTable(ManipleUserConsents_Model_Table_UserConsents $table)
 * @method ManipleUserConsents_Model_Table_UserConsents getTable()
 * @method ManipleUserConsents_Model_UserConsent|null current()
 * @method ManipleUserConsents_Model_UserConsent offsetGet(string $offset)
 * @method ManipleUserConsents_Model_UserConsent getRow(int $position, $seek = false)
 */
class ManipleUserConsents_Model_Rowset_UserConsents extends Zefram_Db_Table_Rowset
{
    const className = __CLASS__;

    protected $_tableClass = ManipleUserConsents_Model_Table_UserConsents::className;

    protected $_rowClass = ManipleUserConsents_Model_UserConsent::className;
}
