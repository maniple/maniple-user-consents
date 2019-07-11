<?php

/**
 * @property Zend_Controller_Request_Http $_request
 */
class ManipleUserConsents_ConsentsController_EditAction extends Maniple_Controller_Action_StandaloneForm
{
    const className = __CLASS__;

    protected $_actionControllerClass = ManipleUserConsents_ConsentsController::className;

    /**
     * @Inject
     * @var Zefram_Db
     */
    protected $_db;

    /**
     * @var ManipleUserConsents_Model_Consent
     */
    protected $_consent;

    protected function _init()
    {
        parent::_init();

        /** @var ManipleUserConsents_Model_Table_Consents $consentsTable */
        $consentsTable = $this->_db->getTable(ManipleUserConsents_Model_Table_Consents::className);

        $consent = $consentsTable->findRow((int) $this->getScalarParam('consent_id'));
        if (!$consent) {
            throw new Maniple_Controller_NotFoundException();
        }

        $this->_consent = $consent;
        $this->_form = new Zefram_Form2(array(
            'elements' => array(
                'title' => array(
                    'type' => 'text',
                ),
                'body' => array(
                    'type' => 'textarea',
                ),
                'is_required' => array(
                    'type' => 'checkbox',
                ),
                'is_active' => array(
                    'type' => 'checkbox',
                ),
                '__submit' => array(
                    'type' => 'submit',
                ),
            ),
        ));
        $this->_form->setDefaults($consent->toArray());
    }

    protected function _process()
    {
        $data = $this->_form->getValues();

        $this->_consent->title = $data['title'];
        $this->_consent->body = $data['body'];
        $this->_consent->is_required = (int) (bool) $data['is_required'];
        $this->_consent->is_active = (int) (bool) $data['is_active'];
        $this->_consent->updated_at = time();

        $this->_consent->save();

        return $this->view->url('maniple-user-consents.consents.index');
    }
}

