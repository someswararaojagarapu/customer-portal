<?php

namespace App\CustomerPortal\Manager;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class FileReaderManager
{
    const JSON_FILE_NAME = 'coding-challenge.json';

    private KernelInterface $appKernel;

    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }

    public function readJson(): string | Response
    {
        try {
            $projectRoot = $this->appKernel->getProjectDir();

            return file_get_contents($projectRoot . '/public/assets/json/' . self::JSON_FILE_NAME);
        } catch (FileNotFoundException $e) {
            return new Response('File Not found', Response::HTTP_NOT_FOUND);
        }
    }
}
