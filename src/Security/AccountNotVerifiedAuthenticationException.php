<?php

namespace App\Security;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AccountNotVerifiedAuthenticationException extends AuthenticationException
{

    public function getMessageKey()
    {
        return 'Votre compte n\'est pas encore vérifié. Veuillez cliquer sur le lien de vérification envoyé par mail.';
    }
}