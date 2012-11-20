<?php

namespace Evario\JanrainBundle\Security\User\Provider;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class JanrainProvider implements UserProviderInterface
{
  protected $userManager;
  /** @var \Symfony\Component\Validator\Validator $validator */
  protected $validator;
  protected $apiKey;
  protected $container;

  public function __construct($userManager, $validator, $apiKey, $container)
  {
    $this->userManager = $userManager;
    $this->validator   = $validator;
    $this->apiKey      = $apiKey;
    $this->container   = $container;
  }

  public function supportsClass($class)
  {
    return $this->userManager->supportsClass($class);
  }

  public function extractJanrainInfo($token)
  {
    // TODO: Move apiKey to config file and reference it.
    /* STEP 1: Extract token POST parameter */
    $post_data = array('token'  => $token,
                       'apiKey' => $this->apiKey,
                       'format' => 'json');

    /* STEP 2: Use the token to make the auth_info API call */
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/auth_info');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $raw_json = curl_exec($curl);
    curl_close($curl);

    /* STEP 3: Parse the JSON auth_info response */
    return json_decode($raw_json, true);
  }

  public function loadUserByUsername($username)
  {
    // Check by PK
    if ($username) {
      $user = $this->userManager->findUserBy(array('id' => $username));
      if ($user) return $user;

      // Check by username
      $user = $this->userManager->findUserBy(array('username' => $username));
      if ($user) return $user;
    }

    if (isset($_POST['token'])) {
      $auth_info = $this->extractJanrainInfo($_POST['token']);

      if ($auth_info && $auth_info['stat'] == 'ok') {

        /* STEP 3 Continued: Extract the 'identifier' from the response */
        $profile    = $auth_info['profile'];

        // Retrieve by profile
        $user = $this->retrieveUser($profile);
        if ($user) return $user;

        // If we still have not found a user, we need to create a new one.
        $user = $this->userManager->createUser();
        $this->createProfile($user, $profile);

        $this->validate($user);

        $this->userManager->updateUser($user);
      }

      if ($user) return $user;
    }

    throw new UsernameNotFoundException('The user is not authenticated.');
  }

  /**
   * creates user profile based on got values
   * @param \Symfony\Component\Security\Core\User\UserInterface $user
   * @param array $profile
   */
  protected function createProfile(UserInterface $user, array $profile)
  {
    $user->setEnabled(true);
    $user->setPassword('');

    $username = strtolower(trim($profile['preferredUsername'], '_'));
    $user->setUsername($username);
    $user->setSocialIdentifier($profile['identifier']);


    if (method_exists($user, 'setEmail') && @$profile['email']) $user->setEmail($profile['email']);

    // use givenName and familyName if provided
    $name = @$profile['name'];
    if (is_array($name))
    {
      if (method_exists($user, 'setFirstName') && array_key_exists('givenName', $name) && $name['givenName']) $user->setFirstName($name['givenName']);
      if (method_exists($user, 'setLastName') && array_key_exists('familyName', $name) && $name['familyName']) $user->setLastName($name['familyName']);
    }

    // fill first name as displayName
    if (method_exists($user, 'setFirstName') && !$user->getFirstName()) $user->setFirstName($profile['displayName']);
  }

  /**
   * validates user
   * @param \Symfony\Component\Security\Core\User\UserInterface $user
   * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
   */
  protected function validate(UserInterface $user)
  {
    // check without validator groups
    if (count($this->validator->validate($user))) {
      throw new UsernameNotFoundException('The social media user could not be stored');
    }
  }

  /**
   * Retrieves user by profile identifier
   * @param array $profile
   * @return UserInterface | null
   */
  public function retrieveUser(array $profile)
  {
    return $this->userManager->findUserBy(array('social_identifier' => $profile['identifier']));
  }

  public function loadUser(UserInterface $user)
  {
    if (!$this->supportsClass(get_class($user))) {
      throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
    }

    return $this->loadUserByUsername($user->getId());
  }

  public function refreshUser(UserInterface $user)
  {
    if (!$user instanceof UserInterface) {
      throw new UnsupportedUserException('Account is not supported.');
    }

    return $this->loadUserByUsername($user->getUsername());
  }
}