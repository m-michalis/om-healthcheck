<?php


class InternetCode_Health_CheckController extends Mage_Core_Controller_Front_Action
{

    //URL  example.com/ic_health/check
    public function indexAction()
    {
        $checks = Mage::helper('health')->runChecks();

        $this->getResponse()->setHeader('content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($checks));
    }
}
