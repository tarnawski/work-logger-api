<?php

namespace WorkLoggerBundle\Model;

use WorkLoggerBundle\Entity\User;

class UserFactory
{
    public function buildAfterRegistration($firstName, $lastName, $username, $email, $password)
    {
        $user = $this->createUser($firstName, $lastName, $username, $email);
        $user->setPlainPassword($password);

        return $user;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $username
     * @param string $email
     * @return User
     */
    private function createUser($firstName, $lastName, $username, $email)
    {
        $user = new User();
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles(['ROLE_API']);
        $user->setEnabled(true);

        return $user;
    }
}
