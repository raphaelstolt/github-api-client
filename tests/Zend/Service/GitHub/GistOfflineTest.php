<?php
/**
 * @group offline
 */
class Zend_Service_GitHub_GistOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    public function testUseOfUserApiPartReturnsAGitHubInstance()
    {
        $assertionMessage = "Use of gist API part didn't return "
            . "expected Zend_Service_GitHub instance.";
        $this->assertTrue($this->_gitHubClient->gist instanceof Zend_Service_GitHub, 
            $assertionMessage
        );
    }
    public function testMetaReturnsGistMetadata()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('gist-meta')
        );
        
        $gistsMeta['gists'][0] = array(
            'files' => array('ports.sh'),
            'public' => true,
            'repo' => '374130',
            'description' => 'List what ports are in use on OS X',
            'created_at' => '2010/04/21 10:24:32 -0700',
            'owner' => 'defunkt'
        );
        $response = $this->_gitHubClient->gist->meta('374130');
        
        $assertionMessage = "Response didn't return expected Gist metadata";
        $this->assertSame($response, $gistsMeta, $assertionMessage);
    }
    public function testListReturnsGistOfUser()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('gist-list')
        );
        
        $response = $this->_gitHubClient->gist->list('raphaelstolt');
        
        $assertionMessage = "Gist list doesn't match expected Gist count";
        $this->assertTrue(count($response['gists']) === 6, $assertionMessage);
        
        $assertionMessage = "Gist owner doesn't match expected owner";
        foreach ($response['gists'] as $gist) {
            $this->assertSame($gist['owner'], 'raphaelstolt', $assertionMessage);
        }
    }
    public function testContentReturnsTheRawGistContent()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('gist-content')
        );
        
        $response = $this->_gitHubClient->gist->content('402018', 'redis-glue-test.php');
        
        $assertionMessage = "Gist file content isn't the same";
        $this->assertSame(
            $response, 
            $this->_getStoredFileContent('redis-glue-test.php'), 
            $assertionMessage
        );
    }
}