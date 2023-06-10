<?php

namespace App\EventsSubscriber;
use App\Entity\User;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Security\AccountNotVerifiedAuthenticationException;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            CheckPassportEvent::class => [
                ['onCheckPassport', -10],
            ],
            LoginFailureEvent::class => [
                ['onLoginFailure', -10],
            ],
        ];
    }
    
    public function onCheckPassport(CheckPassportEvent $event)
    {
        $user = $event->getPassport()->getUser();
        if (!$user instanceof User) {
            throw new \Exception('Type de User incorrect');
        }

        if (!$user->getIsVerified()) {
            throw new AccountNotVerifiedAuthenticationException();
        }
    }

    public function onLoginFailure(LoginFailureEvent $event)
    {
        if(!$event->getException() instanceof AccountNotVerifiedAuthenticationException) {
            return;
        }
        $response = new RedirectResponse($this->router->generate('app_resend_verify_email'));
        $event->setResponse($response);
    }
    
}
