<?php

namespace App\CustomerPortal\Service;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServerInfoValidationService
{
    private array $exceptionMessages = [];

    public function checkRequestPayloadOptions(array $queryArguments): array
    {
        $resolver = new OptionsResolver();
        $this->serverInfoRequestPayloadOptions($resolver);
        try {
            $resolver->resolve($queryArguments);
        } catch (InvalidOptionsException $exception) {
            $this->exceptionMessages[] = $exception->getMessage();
        }

        return $this->exceptionMessages;
    }

    private function serverInfoRequestPayloadOptions(OptionsResolver $resolver): void
    {
        $serverInfoRequestPayloadFields = $this->serverInfoRequestPayloadFields();
        $ramOptions = FilterInformationService::RAM_OPTIONS;
        $hardDiskOptions = FilterInformationService::HARD_DISK_OPTIONS;
        $resolver->setRequired($serverInfoRequestPayloadFields)
            ->setDefined($serverInfoRequestPayloadFields)
            ->setAllowedTypes('storage', 'string')
            ->setAllowedTypes('ram', 'array')
            ->setAllowedValues('ram', function ($ram) use ($ramOptions) {
                if (!empty(array_diff($ram, $ramOptions))) {
                    return false;
                }
                return true;
            })
            ->setAllowedTypes('hardDiskType', 'string')
            ->setAllowedValues('hardDiskType', function ($hardDiskType) use ($hardDiskOptions) {
                if (!empty($hardDiskType) && !in_array($hardDiskType, array_values($hardDiskOptions))) {
                    return false;
                }
                return true;
            })
            ->setAllowedTypes('location', 'string')
        ;
    }
    private function serverInfoRequestPayloadFields(): array
    {
        return ['storage', 'ram', 'hardDiskType', 'location'];
    }
}
