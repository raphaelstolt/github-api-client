<?php

class Zend_Service_GitHub_Issues extends Zend_Service_GitHub
{
    /**
     * @var string
     */
    const OPEN_VALUE = 'open';
    /**
     * @var string
     */
    const CLOSED_VALUE = 'closed';
    /**
     * Supported GitHub API parts
     * @var array
     */
    protected $_supportedMethods = array(
        'search',
        'open',
        'edit',
        'reopen',
        'close',
        'list',
        'show',
        'comments',
        'comment',
        'labels'
    );
    /**
     * @param string $login 
     * @param string $token
     */
    public function __construct($login = null, $token = null)
    {
        parent::__construct($login, $token);
    }
    /**
     * Searches in the issues in a specific repository of an user.
     *
     * @param string $user        The owner of the repository
     * @param string $repository  The repository to search issues in
     * @param string $state       The state of the issue, accepted values are open and close
     * @param string $term        The search term.
     * @return array
     * @throws Zend_Service_GitHub_Exception 
     */
    protected function _search($user, $repository, $state, $term)
    {
        $this->_init();
        $path = sprintf('/issues/search/%s/%s/%s/%s', 
            $user, 
            $repository, 
            $this->_validIssueState($state), 
            $this->_validIssueSearchTerm($term)
        );

        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Lists all issues of a specific repository owned by a given user
     * in a given state.
     *
     * @param string $user        The owner of the repository
     * @param string $repository  The repository to search issues in
     * @param string $state       The state of the issue. Accepted values are open and close.
     * @return array
     * @throws Zend_Service_GitHub_Exception 
     */
    protected function _list($user, $repository, $state)
    {
        $this->_init();
        $path = sprintf('/issues/list/%s/%s/%s', 
            $user, 
            $repository, 
            $this->_validIssueState($state)
        );
        
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Shows a specific issues of a specific repository owned by a given user.
     *
     * @param string $user         The owner of the repository
     * @param string $repository   The repository to search issues in
     * @param integer $issueNumber The issue number of the issue to show.
     * @return array
     * @throws Zend_Service_GitHub_Exception 
     */
    protected function _show($user, $repository, $issueNumber)
    {
        $this->_init();
        $path = sprintf('/issues/show/%s/%s/%d', 
            $user, 
            $repository, 
            $this->_validInteger($issueNumber)
        );
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Opens an issue in a specific repository.
     *
     * @param string $user        The owner of the repository
     * @param string $repository  The repository to search issues in
     * @param array  $params      The issue (detail) params. Accepted keys are title and body
     * @return array
     * @throws Zend_Service_GitHub_Exception 
     */
    protected function _open($user, $repository, array $params)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/issues/open/%s/%s', $user, $repository);
        $response = $this->_post($path, $this->_validIssueParams($params));
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Edits a specific issue.
     *
     * @param string  $user        The owner of the repository
     * @param string  $repository  The repository holding the issue to edit
     * @param integer $issueNumber The number of the issue to edit
     * @param array   $params      The issue (detail) params. Accepted keys are title and body
     * @return array
     * @throws Zend_Service_GitHub_Exception 
     */
    protected function _edit($user, $repository, $issueNumber, array $params)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/issues/edit/%s/%s/%d', 
            $user,
            $repository, 
            $this->_validInteger($issueNumber)
        );
        $response = $this->_post($path, $this->_validIssueParams($params));
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Closes an issue of a specific repository.
     *
     * @param string  $user        The owner of the repository
     * @param string  $repository  The repository to search issues in
     * @param integer $issueNumber The issue number.
     * @return array
     * @throws Zend_Service_GitHub_Exception 
     */
    protected function _close($user, $repository, $issueNumber)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/issues/close/%s/%s/%d', 
            $user, 
            $repository, 
            $this->_validInteger($issueNumber)
        );
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Reopens an issue of a specific repository.
     *
     * @param string  $user        The owner of the repository
     * @param string  $repository  The repository to search issues in
     * @param integer $issueNumber The issue number.
     * @return array
     * @throws Zend_Service_GitHub_Exception 
     */
    protected function _reopen($user, $repository, $issueNumber)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/issues/reopen/%s/%s/%d', 
            $user, 
            $repository, 
            $this->_validInteger($issueNumber)
        );
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Comments on a specific issue.
     *
     * @param string  $user        The owner of the repository
     * @param string  $repository  The repository to search issues in
     * @param integer $issueNumber The issue number.
     * @return array
     * @throws Zend_Service_GitHub_Exception 
     */
    protected function _comment($user, $repository, $issueNumber, $comment)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/issues/comment/%s/%s/%d', 
            $user, 
            $repository, 
            $this->_validInteger($issueNumber)
        );
        $params = array('comment' => $this->_validIssueComment($comment));        
        $response = $this->_post($path, $params);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Get the comments on an issue of a specific repository.
     *
     * @param string  $user        The owner of the repository
     * @param string  $repository  The repository to search issues in
     * @param integer $issueNumber The issue number.
     * @throws Zend_Service_GitHub_Exception 
     * @return array
     */
    protected function _comments($user, $repository, $issueNumber)
    {
        $this->_init();
        $path = sprintf('/issues/comments/%s/%s/%d', 
            $user, 
            $repository, 
            $this->_validInteger($issueNumber)
        );
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Get the issue labels of a specific repository.
     *
     * @param string  $user        The owner of the repository
     * @param string  $repository  The repository to get the issue labels from
     * @throws Zend_Service_GitHub_Exception 
     * @return array
     */
    protected function _labels($user, $repository)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/issues/labels/%s/%s', 
            $user, 
            $repository
        );
        $response = $this->_get($path);

        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Returns the valid issue params.
     *
     * @param array $params
     * @return array
     * @throws Zend_Service_Github_Exception if required params are not set.
     */
    private function _validIssueParams(array $params) {
        $supportedIssueParams = array('title', 'body');
        $issueParamsKeys = array_keys($params);
        if (count(array_intersect($supportedIssueParams, $issueParamsKeys)) === count($supportedIssueParams)) {
            $_params = array(
                'title' => $params['title'], 
                'body' => $params['body']
            ); 
            return $_params;
        }
        require_once 'Zend/Service/GitHub/Exception.php';
        $exceptionMessage = "Invalid issue param(s) '%s' provided";
        $exceptionMessage = sprintf($exceptionMessage, implode(', ', $issueParamsKeys));    
        throw new Zend_Service_GitHub_Exception($exceptionMessage);
    }
    /**
     * Returns a valid issue state.
     *
     * @param  integer $value
     * @return integer $value
     * @throws Zend_Service_GitHub_Exception if the issue 
     * state value is not supported. Possible values are: open and closed.
     */
    private function _validIssueState($state) 
    {
        $acceptedValues = array(
            self::OPEN_VALUE, 
            self::CLOSED_VALUE
        );
        if (in_array($state, $acceptedValues)) {
            return $state;
        }
        require_once 'Zend/Service/GitHub/Exception.php';
        $exceptionMessage = "Invalid issue state '%s' "
            . "provided";
        $exceptionMessage = sprintf($exceptionMessage, $state);
        throw new Zend_Service_GitHub_Exception($exceptionMessage);
    }
    /**
     * Returns a valid issue search term.
     *
     * @param  string $term The term to search the issues against.
     * @return string
     * @throws Zend_Service_Github_Exception if the term is empty.
     */
    private function _validIssueSearchTerm($term) 
    {
        $len = iconv_strlen($term, 'UTF-8');
        if (0 == $len) {
            $exceptionMessage = 'Issue search term must contain at least one '
                . 'character';
            throw new Zend_Service_Github_Exception($exceptionMessage);
        } 
        return urlencode($term);
    }
    /**
     * Returns a valid issue comment.
     *
     * @param  string $comment The comment for an issue.
     * @return string
     * @throws Zend_Service_Github_Exception if the comment is empty.
     */
    private function _validIssueComment($comment) 
    {
        $len = iconv_strlen($comment, 'UTF-8');
        if (0 == $len) {
            $exceptionMessage = 'Issue comment must contain at least one '
                . 'character';
            throw new Zend_Service_Github_Exception($exceptionMessage);
        } 
        return $comment;
    }
}