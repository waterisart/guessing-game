<?php
namespace App\Mercure;

use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;
use Symfony\Component\Mercure\Jwt\LcobucciFactory;

final class MyTokenFactory implements TokenFactoryInterface
{
    private $mercureJwtSecret;

    public function __construct(string $mercureJwtSecret)
    {
        $this->mercureJwtSecret = $mercureJwtSecret;
    }

    public function create(array $subscribe = [], array $publish = [], array $additionalClaims = []): string
    {
        $jwtFactory = new LcobucciFactory($this->mercureJwtSecret);

        return $jwtFactory->create($subscribe, $publish, $additionalClaims);
    }
}