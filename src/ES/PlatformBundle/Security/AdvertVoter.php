<?php

namespace ES\PlatformBundle\Security;

use ES\PlatformBundle\Entity\Advert;
use ES\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class AdvertVoter extends Voter
{
	const VIEW = 'view';
	const EDIT = 'edit';
	private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

	protected function supports($attribute, $subject)
	{
		if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
            return false;
        }
        if (!$subject instanceof Advert) {
            return false;
        }

        return true;

	}

	protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        /** @var Advert $advert */
        $advert = $subject;

        if (!$user instanceof User) {
            switch ($attribute) {
	            case self::VIEW:
	                return $this->canViewAnon($advert);
	            case self::EDIT:
	                return false;
        	}
        }

        if ($this->decisionManager->decide($token, array('ROLE_MODERATEUR'))) 
        {
            return true;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($advert, $user);
            case self::EDIT:
                return $this->canEdit($advert, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Advert $advert, User $user)
    {
    	if ($this->canEdit($advert, $user)) 
    	{
    		return true;
    	}

    	return $advert->getProjectAccepted();
    }

    private function canEdit(Advert $advert, User $user)
    {
    	return $user === $advert->getAuthor();
    }

    private function canViewAnon(Advert $advert)
    {
    	return $advert->getProjectAccepted();
    }
}
?>