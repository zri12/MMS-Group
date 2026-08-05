<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Tests\TestCase;

/**
 * Test health-check endpoint GET /api/v1/health.
 *
 * Test ini tidak memerlukan database, user, atau migration.
 */
class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_returns_200(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200);
    }

    public function test_health_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_health_endpoint_has_correct_structure(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'service',
                'version',
                'timestamp',
            ],
        ]);
    }

    public function test_health_endpoint_returns_correct_values(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertJson([
            'success' => true,
            'message' => 'MMS Monitoring API aktif.',
            'data' => [
                'service' => 'MMS Marketing Monitoring API',
                'version' => 'v1',
            ],
        ]);
    }

    public function test_health_endpoint_timestamp_is_not_empty(): void
    {
        $response = $this->getJson('/api/v1/health');

        $data = $response->json('data');

        $this->assertNotEmpty($data['timestamp']);
    }

    public function test_health_endpoint_does_not_require_authentication(): void
    {
        // Request tanpa token atau session
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
