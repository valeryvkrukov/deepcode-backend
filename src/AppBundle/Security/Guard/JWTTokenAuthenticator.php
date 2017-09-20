<?php
namespace AppBundle\Security\Guard;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator as BaseAuthenticator;

class JWTTokenAuthenticator extends BaseAuthenticator
{
	public function checkCredentials($credentials, UserInterface $user)
    {
    	return false;
    	//var_dump($user);die();
        return true;
    }
}