<?php

namespace App\CustomerPortal\Service;

use App\CustomerPortal\Dto\Request\SearchQuery;

class ServerInformationService
{
    public function getQuery(SearchQuery $searchQuery):array
    {
        return [
            'storage' => $searchQuery->getStorage(),
            'ram' => $searchQuery->getRam(),
            'hardDiskType' => $searchQuery->getHardDiskType(),
            'location' => $searchQuery->getLocation()
        ];
    }
    public function getServerInformationResult(array $selectedFilters, string $serverInfoJson): array
    {
        $data = json_decode($serverInfoJson, true);
        $inputData = $this->prepareInputData($data);

//        $storage = $selectedFilters['storage'];
//        $ram = $selectedFilters['ram'];
//        $hardDiskType = $selectedFilters['hardDiskType'];
//        $locations = $selectedFilters['location'];

        // Filter the servers based on selected filters
        $filteredServers = array_filter($inputData, function ($server) use ($selectedFilters) {
            return (
                in_array($selectedFilters['ram'], [$server['RAM']]) ||
                in_array($selectedFilters['location'], [$server['Location']]) ||
                in_array($selectedFilters['storage'], [$server['Storage']]) ||
                in_array($selectedFilters['hardDiskType'], [$server['HardDiskType']])
            );
        });

        return $filteredServers;
    }

    public function prepareInputData(array $data): array
    {
        $result = [];

        foreach ($data as $item) {
            preg_match('/(\d+)x(\d+)(GB|TB)([A-Z]+)/', $item['HDD'], $matches);

            if (count($matches) === 5) {
                $quantity = (int)$matches[1];
                $capacity = (int)$matches[2];
                $storageType = $matches[3];
                $hardDiskType = $matches[4];
                switch ($storageType) {
                    case 'TB':
                        $storage = $quantity * $capacity * 1024; // Convert TB to GB
                        break;
                    case 'GB':
                        $storage = $quantity * $capacity;
                        break;
                    default:
                        $storage = 0;
                        break;
                }
                $result[] = [
                    'Model' => $item['Model'],
                    'RAM' => $item['RAM'],
                    'HDD' => $item['HDD'],
                    'Storage' => $storage . 'GB',
                    'HardDiskType' => ($hardDiskType === 'SSD' ? 'SSD' : ($hardDiskType === 'SATA' ? 'SATA' : 'SAS')),
                    'Location' => $item['Location'],
                    'Price' => $item['Price']
                ];
            } else {
                // Handle invalid input data
                echo "Invalid input: $item\n";
            }
        }
        return $result;
    }
}