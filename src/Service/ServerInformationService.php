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

    public function getServerInformationResult(array $selectedFilters, array $inputData): array
    {
        // Filter the servers based on selected filters
        $filteredServers = array_filter($inputData, function ($server) use ($selectedFilters) {
            $storage = $selectedFilters['storage'] ?? '';
            $inputStorageValues = $this->getStorageFromFilters($storage);
            $ram = $selectedFilters['ram'] ?? '';
            $hardDiskType = $selectedFilters['hardDiskType'] ?? '';
            $locations = $selectedFilters['location'] ?? '';
            $isMatch = true;
            // Ram checkbox filter
            if (!empty($ram)) {
                $isMatch = $isMatch && empty(array_diff($ram, [$server['RamValue']]));
            }
            // Location dropdown filter
            if (!empty($locations)) {
                $isMatch = $isMatch && in_array($locations, [$server['Location']]);
            }
            // Storage range slider filter
            if (!empty($storage)) {
                $isMatch = $isMatch && ($server['Storage'] >= $inputStorageValues['from'] && $server['Storage'] <= $inputStorageValues['to']) ?? false;
            }
            // hardDisk dropdown filter
            if (!empty($hardDiskType)) {
                $isMatch = $isMatch && in_array($hardDiskType, [$server['HardDiskType']]);
            }

            return $isMatch;
        });

        return array_values($filteredServers);
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
                    'RamValue' => $this->extractGBValue($item['RAM']),
                    'HDD' => $item['HDD'],
                    'Storage' => $storage, // . 'GB'
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

    private function extractGBValue(string $value): string
    {
        preg_match('/(\d+)GB/', $value, $matches);
        if (!empty($matches)) {
            return $matches[1] . 'GB';
        }
        return '';
    }

    public function getStorageFromFilters(string $inputStorage): array
    {
        if (empty($inputStorage)) {
            return ['from' => null, 'to' => null];
        }

        $explodeInputStorage = explode(' ', $inputStorage);
        $from = isset($explodeInputStorage[0]) ? (int) $explodeInputStorage[0] : null;
        $to = isset($explodeInputStorage[2]) ? (int) $explodeInputStorage[2] : null;

        return ['from' => $from, 'to' => $to];
    }
}
