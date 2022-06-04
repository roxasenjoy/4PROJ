<?php

namespace App\Security;

use App\Entity\User;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private EntityManagerInterface $em;

    public function __construct(UrlGeneratorInterface $urlGenerator, EntityManagerInterface $em, AuthService $authService)
    {
        $this->urlGenerator = $urlGenerator;
        $this->em = $em;
        $this->authService = $authService;
    }

    /**
     * On écrase les setups déjà présent car sinon ça ne fonctionne pas du tout
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * Quand on se login, on regarde les credentials que l'utilisateur vient de rentrer (ses informations).
     * Après avoir fait ça, on chercher un utilisateur qui se trouve dans notre base de données avec l'email en question.
     * Si on possède l'email, on regarde si le mot de passe correspond, si c'est le cas c'est good !
     * @param Request $request
     * @return PassportInterface
     */
    public function authenticate(Request $request): PassportInterface
    {
        $credentials = $this->getCredentials($request);

        return new Passport(
            new UserBadge($credentials['email'], function($userIdentifier) { /** Vérification de l'adresse email de l'utilisateur **/
                // optionally pass a callback to load the User manually
                $user = $this->em->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
                if (!$user) {
                    throw new UserNotFoundException();
                }

                $token = $this->authService->generateToken($user);
                $_SESSION['token'] = $token;

                return $user;
            }),
            new PasswordCredentials($credentials['password']) /** Vérification du password de l'utilisateur **/
        );
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        $request->getSession()->getFlashBag()->add('success', "You are now signed in. Greetings, commander.");

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        $url = $this->getLoginUrl($request);

        return new RedirectResponse($url);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    /**
     * On écrase la méthode START qui se trouve dans AbstractLoginFormAuthenticator afin de
     * redirigier l'utilisateur vers la page de login.
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {

        if(in_array('application/json', $request->getAcceptableContentTypes())){
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }

        $url = $this->getLoginUrl($request);

        return new RedirectResponse($url);
    }
}
