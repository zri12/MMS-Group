<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Enums\DayName;
use App\Enums\MemberApprovalStatus;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use App\Models\Member;
use App\Models\OperationalRecap;
use App\Models\OperationalRecapRow;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GenerateOperationalRecapAction
{
    public function execute(CarbonImmutable $date, User $admin): OperationalRecap
    {
        return DB::transaction(function () use ($date, $admin): OperationalRecap {
            $profiles = MarketingProfile::query()->orderBy('code')->get();
            $profileIds = $profiles->pluck('id');
            $reports = $this->reportsByMarketing($date)->keyBy('marketing_profile_id');
            $memberCounts = $this->memberCountsByMarketing($date);
            $previousRows = $this->previousRowsByMarketing($date, $profileIds);

            $attributes = [
                'report_number' => 'RKP-'.$date->format('Ymd'),
                'day_name' => $this->dayName($date)->value,
                'status' => 'Selesai',
                'created_by' => $admin->id,
            ];
            $recap = OperationalRecap::query()
                ->whereDate('recap_date', $date->toDateString())
                ->first();

            if ($recap) {
                $recap->fill($attributes)->save();
            } else {
                $recap = OperationalRecap::query()->create([
                    ...$attributes,
                    'recap_date' => $date->toDateString(),
                ]);
            }

            foreach ($profiles as $profile) {
                $report = $reports->get($profile->id);
                $previous = $previousRows->get($profile->id);
                $members = $memberCounts->get($profile->id, ['previous' => 0, 'incoming' => 0]);

                $targetPrevious = $this->integer($report?->target_previous);
                $targetIncoming = $this->integer($report?->target_incoming);
                $targetOutgoing = $this->integer($report?->target_outgoing);
                $targetTotal = $this->integer($report?->target_total);
                $dropPrevious = $this->integer($previous?->drop_total);
                $dropCurrent = $this->integer($report?->drop_current);
                $stortingPrevious = $this->integer($previous?->storting_total);
                $stortingCurrent = $this->integer($report?->storting_current);
                $dropTotal = $dropPrevious + $dropCurrent;
                $stortingTotal = $stortingPrevious + $stortingCurrent;
                $membersPrevious = $members['previous'];
                $membersIncoming = $members['incoming'];

                OperationalRecapRow::query()->updateOrCreate(
                    [
                        'operational_recap_id' => $recap->id,
                        'marketing_profile_id' => $profile->id,
                    ],
                    [
                        'mg' => $profile->code,
                        'members_l' => $membersPrevious,
                        'members_m' => $membersIncoming,
                        // The active data model has no member-out event. Keep
                        // this at zero until that event is captured explicitly.
                        'members_k' => 0,
                        'members_s' => $membersPrevious + $membersIncoming,
                        'target_previous' => $targetPrevious,
                        'target_incoming' => $targetIncoming,
                        'target_outgoing' => $targetOutgoing,
                        'target_s' => $targetTotal,
                        'drop_previous' => $dropPrevious,
                        'drop_current' => $dropCurrent,
                        'drop_total' => $dropTotal,
                        'storting_previous' => $stortingPrevious,
                        'storting_current' => $stortingCurrent,
                        'storting_total' => $stortingTotal,
                        'percentage' => $targetTotal > 0 ? round(($dropTotal / $targetTotal) * 100, 2) : null,
                        'previous_circulation' => $this->integer($previous?->current_circulation),
                        'current_circulation' => $targetTotal + $dropTotal,
                        'followed_by' => $admin->name,
                        'morning_cash' => 0,
                    ],
                );
            }

            return $recap->fresh(['rows.marketingProfile.user']);
        });
    }

    /**
     * @return Collection<int, object>
     */
    private function reportsByMarketing(CarbonImmutable $date): Collection
    {
        return DailyOperationalReport::query()
            ->selectRaw('marketing_profile_id')
            ->selectRaw('COALESCE(SUM(previous_target_amount), 0) as target_previous')
            ->selectRaw('COALESCE(SUM(incoming_target_amount), 0) as target_incoming')
            ->selectRaw('COALESCE(SUM(outgoing_target_amount), 0) as target_outgoing')
            ->selectRaw('COALESCE(SUM(total_target_amount), 0) as target_total')
            ->selectRaw('COALESCE(SUM(drop_amount), 0) as drop_current')
            ->selectRaw('COALESCE(SUM(storting), 0) as storting_current')
            ->whereDate('report_date', $date->toDateString())
            ->groupBy('marketing_profile_id')
            ->get();
    }

    /**
     * @return Collection<int, array{previous: int, incoming: int}>
     */
    private function memberCountsByMarketing(CarbonImmutable $date): Collection
    {
        $approved = MemberApprovalStatus::Approved->value;
        $previous = Member::query()
            ->selectRaw('marketing_profile_id, COUNT(*) as total')
            ->where('approval_status', $approved)
            ->whereDate('input_date', '<', $date->toDateString())
            ->groupBy('marketing_profile_id')
            ->pluck('total', 'marketing_profile_id');
        $incoming = Member::query()
            ->selectRaw('marketing_profile_id, COUNT(*) as total')
            ->where('approval_status', $approved)
            ->whereDate('input_date', $date->toDateString())
            ->groupBy('marketing_profile_id')
            ->pluck('total', 'marketing_profile_id');

        return $previous->keys()
            ->merge($incoming->keys())
            ->unique()
            ->mapWithKeys(fn ($id): array => [(int) $id => [
                'previous' => (int) $previous->get($id, 0),
                'incoming' => (int) $incoming->get($id, 0),
            ]]);
    }

    /**
     * @param  Collection<int, int>  $profileIds
     * @return Collection<int, OperationalRecapRow>
     */
    private function previousRowsByMarketing(CarbonImmutable $date, Collection $profileIds): Collection
    {
        return OperationalRecapRow::query()
            ->with('operationalRecap')
            ->whereIn('marketing_profile_id', $profileIds)
            ->whereHas('operationalRecap', fn ($query) => $query->whereDate('recap_date', '<', $date->toDateString()))
            ->get()
            ->sortByDesc(fn (OperationalRecapRow $row) => $row->operationalRecap->recap_date)
            ->unique('marketing_profile_id')
            ->keyBy('marketing_profile_id');
    }

    private function dayName(CarbonImmutable $date): DayName
    {
        return match ($date->dayOfWeekIso) {
            1 => DayName::Monday,
            2 => DayName::Tuesday,
            3 => DayName::Wednesday,
            4 => DayName::Thursday,
            5 => DayName::Friday,
            6 => DayName::Saturday,
            7 => DayName::Sunday,
        };
    }

    private function integer(mixed $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }
}
