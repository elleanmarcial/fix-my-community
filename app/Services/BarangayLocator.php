<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class BarangayLocator
{
    /**
     * Find the barangay containing the supplied coordinates.
     *
     * Coordinates must be WGS84:
     * longitude first, latitude second.
     */
    public function findByCoordinates(
        float $latitude,
        float $longitude
    ): ?int {
        if (
            $latitude < -90 || $latitude > 90 ||
            $longitude < -180 || $longitude > 180
        ) {
            return null;
        }

        $point = "ST_GeomFromText(?, 4326)";

        $results = DB::select(
            "
            SELECT id
            FROM barangays
            WHERE MBRIntersects(
                boundary_geom,
                {$point}
            )
            AND ST_Intersects(
                boundary_geom,
                {$point}
            )
            ORDER BY id
            LIMIT 1
            ",
            [
                "POINT({$longitude} {$latitude})",
                "POINT({$longitude} {$latitude})",
            ]
        );

        return isset($results[0])
            ? (int) $results[0]->id
            : null;
    }
}
