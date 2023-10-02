<?php
namespace App\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AddOwnerGroupsNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        // TODO: Implement normalize() method.
        dd('ici');
    }
    public function supportsNormalization(mixed $data, string $format = null)
    {
        // TODO: Implement supportsNormalization() method.
    }
}