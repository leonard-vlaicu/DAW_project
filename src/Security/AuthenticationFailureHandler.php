<?php

namespace App\Security;

use App\Exceptions\AccountNotVerifiedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface {
    public function __construct(private UrlGeneratorInterface $urlGenerator) {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response {
        if ($exception instanceof AccountNotVerifiedException) {
            $email = $request->getSession()->get('_username');

            $request->getSession()->getFlashBag()->add('error', $exception->getMessage());
            $request->getSession()->set('unverified_email', $email);
        } else {
            $request->getSession()->remove('unverified_email');
            $request->getSession()->getFlashBag()->add('error', 'Invalid credentials');
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}
