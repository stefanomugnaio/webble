<?php
// src/Service/RecaptchaService.php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class RecaptchaService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire('%env(RECAPTCHA_SECRET_KEY)%')]
        private string $secretKey,
    ) {
    }

    /**
     * Vérifie le token reCAPTCHA v3.
     *
     * @param string $token          token envoyé par le front
     * @param string $expectedAction l'action attendue (ex: 'contact')
     * @param float  $minScore       seuil minimum (0.0–1.0)
     */
    public function verify(string $token, string $expectedAction, float $minScore = 0.5): bool
    {
        if (empty($token)) {
            return false;
        }

        $response = $this->httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret'   => $this->secretKey,
                'response' => $token,
                // 'remoteip' => $ipEventuel
            ],
        ]);

        $data = $response->toArray(false);

        // 1) succès global
        if (!($data['success'] ?? false)) {
            return false;
        }

        // 2) action cohérente
        if (($data['action'] ?? null) !== $expectedAction) {
            return false;
        }

        // 3) score suffisant
        if (($data['score'] ?? 0) < $minScore) {
            return false;
        }

        return true;
    }
}
