<?php
require_once _PS_MODULE_DIR_ . 'belvg_customerattributes/includer.php';
class AuthController extends AuthControllerCore
{
    /*
    * module: belvg_customerattributes
    * date: 2016-10-14 16:19:23
    * version: 1.6.5
    */
    protected function processSubmitAccount()
    {
        if (Module::isEnabled('belvg_customerattributes')) {
            $module = new belvg_customerattributes();
            $this->errors = $module->checkAttributeBeforeCreate();
        }
        parent::processSubmitAccount();
    }
}