<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Member;
use App\Models\OperationalRecapRow;
use App\Models\Prospect;
use App\Models\TrackingPoint;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class ModelConfigurationTest extends TestCase
{
    public function test_user_has_expected_security_configuration(): void
    {
        $user = new User;

        $this->assertContains(SoftDeletes::class, class_uses_recursive($user));
        $this->assertContains('password', $user->getHidden());
        $this->assertContains('remember_token', $user->getHidden());
        $this->assertContains('username', $user->getFillable());
        $this->assertContains('role', $user->getFillable());
    }

    public function test_soft_delete_models_match_schema(): void
    {
        $this->assertContains(SoftDeletes::class, class_uses_recursive(new Prospect));
        $this->assertContains(SoftDeletes::class, class_uses_recursive(new Member));
    }

    public function test_domain_models_use_explicit_fillable(): void
    {
        $this->assertContains('local_uuid', (new Prospect)->getFillable());
        $this->assertContains('member_number', (new Member)->getFillable());
        $this->assertContains('percentage', (new OperationalRecapRow)->getFillable());
        $this->assertNotContains('id', (new Prospect)->getFillable());
    }

    public function test_tracking_point_does_not_expect_updated_at_column(): void
    {
        $this->assertNull(TrackingPoint::UPDATED_AT);
    }
}
