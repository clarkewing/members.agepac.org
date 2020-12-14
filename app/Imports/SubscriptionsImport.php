<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class SubscriptionsImport extends LegacyDBImport implements OnEachRow
{
    /**
     * @param  \Maatwebsite\Excel\Row  $row
     */
    public function onRow(Row $row)
    {
        $user = User::find($row['userid']);

        $plan = [
            30 => 'agepac',
            60 => 'agepac+alumni',
        ][$row['montant']];

        $expiry = Carbon::createFromTimestamp($row['d_expiry'])->startOfDay();

        $diffYears = $expiry->diffInYears(today()->addDay());
        $prepaidCycles = min($diffYears + 1, 3);

        $anchor = $expiry->subYears($diffYears);
        $coupon = 'legacy-migration-' . $prepaidCycles;

        $user->createOrGetStripeCustomer(); // Ensure user is customer.

        $user->newSubscription(
            'default',
            config("council.plans.$plan")
        )
            ->anchorBillingCycleOn($anchor)
            ->withCoupon($coupon)
            ->add();
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'userid' => ['required', 'integer', Rule::exists('users', 'id')],
            'montant' => ['required', 'integer', Rule::in([30, 60])],
            'd_expiry' => ['required', 'date_format:U', 'after_or_equal:now'],
            'statut' => ['required', 'boolean', 'accepted'],
        ];
    }
}
