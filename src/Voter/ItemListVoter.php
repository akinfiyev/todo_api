<?php

namespace App\Voter;

use App\Entity\ItemList;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ItemListVoter extends Voter
{
    const CREATE = 'create';
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const ADD_ITEM = 'add_item';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE, self::ADD_ITEM])) {
            return false;
        }

        if (!$subject instanceof ItemList) {
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

        /** @var ItemList $itemList */
        $itemList = $subject;

        switch ($attribute) {
            case self::CREATE:
                return true;
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
            case self::ADD_ITEM:
                return $itemList->getUser() == $user;
        }
    }
}