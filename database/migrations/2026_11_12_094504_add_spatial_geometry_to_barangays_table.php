<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add a temporary nullable geometry column.
        DB::statement("
            ALTER TABLE barangays
            ADD COLUMN boundary_geom GEOMETRY NULL
        ");

        // 2. Convert existing GeoJSON boundaries into
        // spatial geometries using SRID 4326.
        DB::statement("
            UPDATE barangays
            SET boundary_geom = ST_GeomFromText(
                ST_AsText(
                    ST_GeomFromGeoJSON(boundary)
                ),
                4326
            )
            WHERE boundary IS NOT NULL
        ");

        // 3. Ensure every barangay has a geometry.
        $missing = DB::table('barangays')
            ->whereNull('boundary_geom')
            ->count();

        if ($missing > 0) {
            throw new RuntimeException(
                "{$missing} barangay boundaries could not be converted."
            );
        }

        // 4. Make the geometry column mandatory.
        DB::statement("
            ALTER TABLE barangays
            MODIFY boundary_geom GEOMETRY NOT NULL
        ");

        // 5. Add the spatial index.
        DB::statement("
            ALTER TABLE barangays
            ADD SPATIAL INDEX idx_barangays_boundary_geom
            (boundary_geom)
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE barangays
            DROP INDEX idx_barangays_boundary_geom
        ");

        DB::statement("
            ALTER TABLE barangays
            DROP COLUMN boundary_geom
        ");
    }
};
