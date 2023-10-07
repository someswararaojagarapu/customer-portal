<?php

namespace App\CustomerPortal\Service;

class FilterInformationService
{
    public function getFilterResult(string $serverInfoJson): array
    {
        $data = json_decode($serverInfoJson, true);
        // Extract unique locations from the servers information array
        $uniqueLocations = array_unique(array_column($data, 'Location'));

        return [
            'Storage' => ['0', '250GB', '500GB', '1TB', '2TB', '3TB', '4TB', '8TB', '12TB', '24TB', '48TB', '72TB'],
            'Ram' => ['2GB', '4GB', '8GB', '12GB', '16GB', '24GB', '32GB', '48GB', '64GB', '96GB'],
            'HardDisk type' => ['SAS', 'SATA', 'SSD'],
            'Location' => array_values($uniqueLocations)
        ];
    }
}