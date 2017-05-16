<?php

require_once _PS_MODULE_DIR_ . 'belvg_customerattributes/includer.php';

class AuthController extends AuthControllerCore
{
    protected function processSubmitAccount()
    {
        if (Module::isEnabled('belvg_customerattributes')) {
            $module = new belvg_customerattributes();
            $this->errors = $module->checkAttributeBeforeCreate();
        }
        parent::processSubmitAccount();
    }
}