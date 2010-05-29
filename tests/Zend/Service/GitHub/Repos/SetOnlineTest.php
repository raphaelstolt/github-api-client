<?php
require_once 'Zend/Service/GitHub.php';
require_once 'Zend/Service/GitHubOnlineTestcase.php';

class Zend_Service_GitHub_Repos_SetOnlineTest extends Zend_Service_GitHubOnlineTestcase
{
    public function testRepositoryVisibilityIsChangedToPublic()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghrvis');
        $this->markTestSkipped('No private plan GitHub account available.');
        $this->_createRepository($repositoryName, 'private');
        $response = $this->_gitHubClient->repos->set->public($repositoryName);
        $this->_deleteRepository($repositoryName);
        $this->assertFalse($response['repository']['private']);
    }    
    public function testRepositoryVisibilityIsChangedToPrivate()
    {
        $this->markTestSkipped('No private plan GitHub account available.');
        $repositoryName = $this->_getRandomRepositoryName('zfghrvis');
        $this->_createRepository($repositoryName);
        $response = $this->_gitHubClient->repos->set->private($repositoryName);
        $this->_deleteRepository($repositoryName);
        $this->assertTrue($response['repository']['private']);
    }
}
