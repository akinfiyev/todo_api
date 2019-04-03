<?php

namespace App\Voter;

use App\Entity\Item;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ItemVoter extends Voter
{
    const CREATE = 'create';
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Item) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Item $item */
        $item = $subject;

        switch ($attribute) {
            case self::CREATE:
                return true;
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                return $item->getItemList()->getUser() == $user;
        }
    }
}