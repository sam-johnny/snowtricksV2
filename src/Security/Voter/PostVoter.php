<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 13/10/2022
 * Time: 13:10
 */

namespace App\Security\Voter;

use App\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{

    public const EDIT = 'post_edit';
    public const DELETE = 'post_delete';


    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($user === null) {
            return false;
        }

        $post = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($post, $user);
                break;
            case self::DELETE:
                return $this->canDelete($post, $user);
                break;
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param Post $post
     * @param $user
     * @return bool
     */
    private function canEdit(Post $post, $user): bool
    {
        return $user === $post->getUser();
    }

    /**
     * @param Post $post
     * @param $user
     * @return bool
     */
    private function canDelete(Post $post, $user): bool
    {
        return $user === $post->getUser();
    }
}