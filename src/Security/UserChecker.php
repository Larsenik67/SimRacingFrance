<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

    }

    public function checkPostAuth(UserInterface $user): void
    { 
        
        $ban = $user->getBan();
        $today = new \DateTime();

        if( $ban > $today ){
        
            throw new CustomUserMessageAccountStatusException("Vous êtes bannis jusqu'au ".$ban->format('d/m/Y')."");

        } elseif ( $user->getIsVerified() == 0 ) {

            throw new CustomUserMessageAccountStatusException('Veuillez validez votre email');

        }
    }
}