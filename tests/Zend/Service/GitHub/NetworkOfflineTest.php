<?php
/**
 * @group offline
 */
class Zend_Service_GitHub_NetworkOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testNetworkMetaResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->network->meta(
            'schacon', 
            'simplegit'
        );
    }
    public function testNetworkMetaGetsListed()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('network-meta')
        );
        $response = $this->_gitHubClient->network->meta(
            'schacon', 
            'simplegit'
        );
        $this->assertTrue(isset($response['spacemap']));
        $this->assertTrue(isset($response['blocks']));
        $this->assertTrue(isset($response['nethash']));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testNetworkDataResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->network->data(
            'schacon', 
            'simplegit',
            'test_nethash'
        );
    }
    public function testNetworkDataGetsListed()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('network-data')
        );
        $response = $this->_gitHubClient->network->data(
            'schacon', 
            'simplegit',
            'test_nethash'
        );
        $this->assertTrue(isset($response['commits']));
    }
}
