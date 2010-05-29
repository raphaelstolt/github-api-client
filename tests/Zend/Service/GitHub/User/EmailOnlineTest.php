<?php
require_once 'Zend/Service/GitHubOnlineTestcase.php';
require_once 'Zend/Service/GitHub/User/Email.php';

class Zend_Service_GitHub_User_EmailOnlineTest extends Zend_Service_GitHubOnlineTestcase
{
    const TEST_EMAIL_ADDRESS = 'some@email.org';
    const PRIMARY_EMAIL_ADDRESS = 'raphael.stolt@googlemail.com';

    public function testThatAnEmailAddressIsAdded()
    {
        $response = $this->_gitHubClient->user->email->add(self::TEST_EMAIL_ADDRESS);
        $this->assertTrue(in_array(self::TEST_EMAIL_ADDRESS, $response['emails']));
    }
    public function testThatThePrimaryEmailAddressIsListed()
    {
        $response = $this->_gitHubClient->user->emails();
        $this->assertTrue(in_array(self::PRIMARY_EMAIL_ADDRESS, $response['emails']));
    }
    public function testThatAnEmailAddressGetsRemoved()
    {
        $response = $this->_gitHubClient->user->email->remove(self::TEST_EMAIL_ADDRESS);
        $this->assertFalse(in_array(self::TEST_EMAIL_ADDRESS, $response['emails']));
    }
}