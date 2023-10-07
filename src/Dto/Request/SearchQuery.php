<?php

namespace App\CustomerPortal\Dto\Request;

class SearchQuery
{
    public ?string $storage = '';
    public ?array $ram = [];
    public ?string $hardDiskType = '';
    public ?string $location = '';

    public function getStorage(): ?string
    {
        return $this->storage;
    }

    public function setStorage(?string $storage): void
    {
        $this->storage = $storage;
    }

    public function getRam(): ?array
    {
        return $this->ram;
    }

    public function setRam(?array $ram): void
    {
        $this->ram = $ram;
    }

    public function getHardDiskType(): ?string
    {
        return $this->hardDiskType;
    }

    public function setHardDiskType(?string $hardDiskType): void
    {
        $this->hardDiskType = $hardDiskType;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }
}
