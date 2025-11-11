<?php

namespace App\Security;

use App\AccountNotVerifiedException;
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
            $request->getSession()->getFlashBag()->add('error', $exception->getMessage());
        } else {
            $request->getSession()->getFlashBag()->add('error', 'Invalid credentials');
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}
