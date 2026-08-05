<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardAggregateTest extends TestCase
{
    use RefreshDatabase;

    public function test_development_seed_matches_dashboard_reference_aggregates_for_monday(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(39, DB::table('members')->count());

        $reportTotals = DB::table('daily_operational_reports')
            ->whereDate('report_date', '2026-07-20')
            ->selectRaw('SUM(total_target_amount) as target, SUM(drop_amount) as drop_amount, SUM(storting) as storting')
            ->first();

        $this->assertSame(123500000, (int) $reportTotals->target);
        $this->assertSame(91000000, (int) $reportTotals->drop_amount);
        $this->assertSame(37700000, (int) $reportTotals->storting);

        $recapTotals = DB::table('operational_recaps')
            ->join('operational_recap_rows', 'operational_recap_rows.operational_recap_id', '=', 'operational_recaps.id')
            ->whereDate('operational_recaps.recap_date', '2026-07-20')
            ->selectRaw('COUNT(*) as rows_count, SUM(target_s) as target, SUM(drop_total) as drop_total, SUM(storting_total) as storting_total, SUM(CASE WHEN percentage IS NULL THEN 1 ELSE 0 END) as null_percentages, SUM(previous_circulation) as previous_circulation, SUM(current_circulation) as current_circulation, SUM(CASE WHEN current_circulation = target_s + drop_total THEN 1 ELSE 0 END) as formula_like_circulation_rows')
            ->first();

        $this->assertSame(13, (int) $recapTotals->rows_count);
        $this->assertSame(123500000, (int) $recapTotals->target);
        $this->assertSame(91000000, (int) $recapTotals->drop_total);
        $this->assertSame(37700000, (int) $recapTotals->storting_total);
        $this->assertSame(13, (int) $recapTotals->null_percentages);
        $this->assertSame(0, (int) $recapTotals->previous_circulation);
        $this->assertSame(0, (int) $recapTotals->current_circulation);
        $this->assertSame(0, (int) $recapTotals->formula_like_circulation_rows);
    }
}
