<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportBarangays extends Command
{
    protected $signature = 'barangays:import';

    protected $description = 'Import Philippine barangays from GeoJSON';

    public function handle(): int
    {
        $path = storage_path('app/barangays.geojson');

        if (!File::exists($path)) {
            $this->error('GeoJSON file not found!');
            return self::FAILURE;
        }

        $this->info('Reading GeoJSON...');

        $data = json_decode(File::get($path), true);

        if (!isset($data['features'])) {
            $this->error('Invalid GeoJSON file.');
            return self::FAILURE;
        }

        $batch = [];
        $count = 0;

        foreach ($data['features'] as $feature) {
            $p = $feature['properties'] ?? [];
            $geometry = $feature['geometry'] ?? null;

            $psgc = $p['ADM4_PCODE'] ?? $p['psgc_id'] ?? null;
            $name = $p['ADM4_EN'] ?? $p['psgc_name'] ?? null;

            if (!$psgc || !$name) {
                continue;
            }

            $batch[] = [
                'psgc_code' => $psgc,
                'name' => $name,
                'city' => $p['ADM3_EN'] ?? 'Unknown',
                'province' => $p['ADM2_EN'] ?? null,
                'region' => $p['ADM1_EN'] ?? null,
                'boundary' => $geometry ? json_encode($geometry) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= 500) {
                $this->saveBatch($batch);
                $count += count($batch);
                $batch = [];

                $this->info("Imported: {$count}");
            }
        }

        if (!empty($batch)) {
            $this->saveBatch($batch);
            $count += count($batch);
        }

        $this->info("Import complete! Total processed: {$count}");

        return self::SUCCESS;
    }

    private function saveBatch(array $batch): void
    {
        DB::table('barangays')->upsert(
            $batch,
            ['psgc_code'],
            ['name', 'city', 'province', 'region', 'boundary', 'updated_at']
        );
    }
}
