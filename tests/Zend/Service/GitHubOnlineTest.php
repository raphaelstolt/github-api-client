<?php
require_once 'Zend/Service/GitHub.php';

class Zend_Service_GitHubOnlineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testCallOfUserUpdateWithNonAuthClientRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->user->show('raphaelstolt', array('name' => 'raphael_update_name'));
    }
}