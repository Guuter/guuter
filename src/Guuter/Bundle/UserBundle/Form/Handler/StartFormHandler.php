<?php

namespace Guuter\Bundle\UserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Guuter\Bundle\UserBundle\Node\PeopleManager;

class StartFormHandler extends BaseHandler
{
    protected $peopleManager;

    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, PeopleManager $peopleManager)
    {
        $this->peopleManager = $peopleManager;

        parent::__construct($form, $request, $userManager, $mailer, $tokenGenerator);
    }

    protected function onSuccess(UserInterface $user, $confirmation)
    {
        $userNode = $this->peopleManager->initUser();
        $userNode->addEmail($user->getEmail());
        $userNode->save();

        $user->setGraphId($userNode->getId());

        parent::onSuccess($user, $confirmation);
    }
}
