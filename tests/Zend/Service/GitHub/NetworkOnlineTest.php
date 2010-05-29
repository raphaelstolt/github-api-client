<?php
/**
 * @group online
 */
class Zend_Service_GitHub_NetworkOnlineTest extends Zend_Service_GitHubOnlineTestcase
{
    public function testNetworkMetaGetsListed()
    {
        $response = $this->_gitHubClient->network->meta(
            'schacon', 
            'simplegit'
        );
        $this->assertTrue(isset($response['spacemap']));
        $this->assertTrue(isset($response['blocks']));
        $this->assertTrue(isset($response['nethash']));
    }
    public function testNetworkDataGetsListed()
    {
        $response = $this->_gitHubClient->network->meta(
            'schacon', 
            'simplegit'
        );
        $nethash = $response['nethash'];
        $response = $this->_gitHubClient->network->data(
            'schacon', 
            'simplegit',
            $nethash
        );
        $this->assertTrue(isset($response['commits']));
    }
}