<?php
require_once 'Zend/Service/GitHubOnlineTestcase.php';
require_once 'Zend/Service/GitHub/Repos/Collaborators.php';

class Zend_Service_GitHub_Repos_CollaboratorsOnlineTest extends Zend_Service_GitHubOnlineTestcase
{
    public function testCollaboratorIsAddedToRepos()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghrcol');
        $this->_createRepository($repositoryName);
        $response = $this->_gitHubClient->repos->collaborators->add('raphaelstolt', $repositoryName);
        $this->_deleteRepository($repositoryName);
        $expectedCollaborators = 'zfghclient;raphaelstolt';
        $this->assertSame($expectedCollaborators, implode(';', $response['collaborators']));
    }
    public function testCollaboratorIsRemovedFromRepos()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghrcol');
        $this->_createRepository($repositoryName);
        $response = $this->_gitHubClient->repos->collaborators->add('raphaelstolt', $repositoryName);
        
        $expectedCollaborators = 'zfghclient;raphaelstolt';
        $this->assertSame($expectedCollaborators, implode(';', $response['collaborators']));
        $response = $this->_gitHubClient->repos->collaborators->remove('raphaelstolt', $repositoryName);
        $this->_deleteRepository($repositoryName);
        $expectedCollaborators = 'zfghclient';
        $this->assertSame($expectedCollaborators, implode(';', $response['collaborators']));
    }
}
