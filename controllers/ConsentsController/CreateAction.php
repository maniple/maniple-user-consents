<?php

/**
 * @property Zend_Controller_Request_Http $_request
 */
class ManipleUserConsents_ConsentsController_CreateAction extends Maniple_Controller_Action_StandaloneForm
{
    const className = __CLASS__;

    protected $_actionControllerClass = ManipleUserConsents_ConsentsController::className;

    /**
     * @Inject
     * @var Zefram_Db
     */
    protected $_db;

    protected function _init()
    {
        parent::_init();

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
    }

    protected function _process()
    {
        /** @var ManipleUserConsents_Model_Table_Consents $consentsTable */
        $consentsTable = $this->_db->getTable(ManipleUserConsents_Model_Table_Consents::className);

        $consent = $consentsTable->createRow(array(
            'title' => $this->_form->getValue('title'),
            'body'  => $this->_form->getValue('body'),
            'body_type' => 'html',
            'is_required' => (int) $this->_form->getValue('is_required'),
            'is_active' => (int) $this->_form->getValue('is_active'),
            'created_at' => time(),
            'updated_at' => time(),
            'display_order' => 0,
        ));
        $consent->save();

        return $this->view->url('maniple-user-consents.consents.index');
    }
}

