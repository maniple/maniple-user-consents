<?php

/**
 * @method bool setTable(ManipleUserConsents_Model_Table_UserConsentStates $table)
 * @method ManipleUserConsents_Model_Table_UserConsentStates getTable()
 * @method ManipleUserConsents_Model_UserConsentState|null current()
 * @method ManipleUserConsents_Model_UserConsentState offsetGet(string $offset)
 * @method ManipleUserConsents_Model_UserConsentState getRow(int $position, $seek = false)
 */
class ManipleUserConsents_Model_Rowset_UserConsentStates extends Zefram_Db_Table_Rowset
{
    const className = __CLASS__;

    protected $_tableClass = ManipleUserConsents_Model_Table_UserConsentStates::className;

    protected $_rowClass = ManipleUserConsents_Model_UserConsentState::className;
}
