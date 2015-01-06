<?php

App::uses('BaseAuthenticate', 'Controller/Component/Auth');

/**
 * Controls the authentication with a cas server.
 */
class CasAuthenticate extends BaseAuthenticate {

    /**
     * Log the user in by letting cas do it's thing, then directing the user as needed.
     */
    public function authenticate(CakeRequest $request, CakeResponse $response) {
        $this->initSettings();
        $this->initCasClient();
        phpCAS::forceAuthentication();
        $user = $this->findUser(phpCAS::getUser());
        return $this->saveUser($user, phpCAS::getUser());
    }

    /**
     * Kill all the sessions to logout.
     */
    public function logout($user) {
        $this->initSettings();
        $this->initCasClient();
        session_unset();
        session_destroy();
        phpCAS::logout();
    }

    /**
     * Initialize the cas client.
     */
    private function initCasClient() {
        if (!phpCAS::isInitialized()) {
            phpCAS::setDebug($this->settings['debug']);
            phpCAS::client($this->settings['version'], $this->settings['hostname'], $this->settings['port'], $this->settings['uri'], true);
            phpCAS::setNoCasServerValidation();
        }
    }

    /**
     * Find the user in the model
     * @param String $username retrieved from cas
     * @return array hopefully the user or an empty array.
     */
    private function findUser($username) {
        $userModel = ClassRegistry::init($this->settings['userModel']);
        $user = $userModel->find('first', ['conditions' => [$this->settings['userModel'] . '.' . $this->settings['userField'] => strtolower($username)]]);
        if (isset($user[$this->settings['userModel']])) {
            return $user[$this->settings['userModel']];
        }
        return $user;
    }

    /**
     * Save the user model if the user doesn't exist.
     * @param array $user model
     * @param string $username returned from cas.
     * @return array $user model
     */
    private function saveUser($user, $username) {
        if ($this->settings['addUserIfDoesNotExist'] && empty($user)) {
            $userModel = ClassRegistry::init($this->settings['userModel']);
            $user = [$this->settings['userModel'] => [
                    $this->settings['userField'] => strtolower($username),
                    $this->settings['passwordField'] => $this->settings['password']
            ]];
            if ($this->settings['hasProfile']) {
                $user[$this->settings['profileModel']]['orientation_id'] = -1;
            }
            $userModel->save($user);
        }
        return $user;
    }

    /**
     * Init default settings.
     */
    private function initSettings() {
        $settingsDefaults = [
            'version' => '2.0',
            'addUserIfDoesNotExist' => true,
            'port' => 443,
            'userModel' => 'User',
            'userField' => 'username',
            'passwordField' => 'password',
            'password' => '',
            'hasProfile' => true,
            'profileModel' => 'Profile'];
        foreach ($settingsDefaults as $name => $value) {
            if (!isset($this->settings[$name])) {
                $this->settings[$name] = $value;
            }
        }
    }

}
