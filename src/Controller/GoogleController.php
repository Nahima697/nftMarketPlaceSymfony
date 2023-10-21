<?php

# Controller/GoogleController
namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Exception\IdentityProviderAuthenticationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{

    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        //Redirect to google
        return $clientRegistry->getClient('google')->redirect([], []);
    }

    /**
     * After going to Google, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     */
    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient $client */
        $client = $clientRegistry->getClient('google');

        try {
            // the exact class depends on which provider you're using
            /** @var \League\OAuth2\Client\Provider\GoogleUser $user */
            $user = $client->fetchUser();

            // do something with all this new power!
            // e.g. $name = $user->getFirstName();

            return new JsonResponse([
//                'access_token' => $client->getAccessToken(),
                'id'=>$user->getId(),
                'avatar'=>$user->getAvatar(),
                'firstName'=>$user->getFirstName(),
            ]);
            // ...
        } catch (IdentityProviderAuthenticationException $e) {
            // something went wrong!
            // probably you should return the reason to the user
            var_dump($e->getMessage()); die;
        }
    }
}