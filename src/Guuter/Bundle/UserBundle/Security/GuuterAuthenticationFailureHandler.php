<?php

namespace Guuter\Bundle\UserBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use FOS\UserBundle\Model\UserManagerInterface;
use Guuter\Bundle\UserBundle\Node\PeopleManager;
use Guuter\Bundle\UserBundle\Node\PersonalAttributeManager;
use Symfony\Component\Security\Core\SecurityContext;

class GuuterAuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    protected $userManager;
    protected $peopleManager;
    protected $personalAttributeManager;
    protected $securityContext;

    public function __construct(HttpKernelInterface $httpKernel, HttpUtils $httpUtils, UserManagerInterface $userManager, PeopleManager $peopleManager, PersonalAttributeManager $personalAttributeManager, SecurityContext $securityContext, array $options, LoggerInterface $logger = null)
    {
        $this->userManager = $userManager;
        $this->peopleManager = $peopleManager;
        $this->personalAttributeManager = $personalAttributeManager;
        $this->securityContext = $securityContext;

        parent::__construct($httpKernel, $httpUtils, $options, $logger);
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($exception instanceof UsernameNotFoundException) {
            $token = $exception->getToken();
            if ($token instanceof UsernamePasswordToken) {
                $username = $token->getUser();
                $password = $token->getCredentials();

                $user = $this->userManager->createUser();
                $user->setEmail($username);
                $user->setPlainPassword($password);
                $user->setEnabled(true);

                $people = $this->peopleManager->initPeople();
                $people->save();

                $this->personalAttributeManager->changeEmail($people, $user->getEmail());

                $user->setGraphId($people->getId());

                $this->userManager->updateUser($user);

                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->securityContext->setToken($token);

                return $this->httpUtils->createRedirectResponse($request, 'homepage');
            }
        }

        return parent::onAuthenticationFailure($request, $exception);
    }
}
