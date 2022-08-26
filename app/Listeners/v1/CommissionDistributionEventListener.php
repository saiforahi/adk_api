<?php

namespace App\Listeners\v1;

use App\Events\v1\CommissionDistributionEvent as CommissionDistributionEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\{TycoonCommissionHistory, TycoonWallet};
use Illuminate\Support\Facades\Log;

class CommissionDistributionEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CommissionDistributionEvent  $event
     * @return void
     */
    public function handle(CommissionDistributionEvent $event)
    {
        $data = $event->data;
        TycoonCommissionHistory::create([
            'bonus_type' => $data['bonus_type'],
            'amount' => $data['amount'],
            'tycoon_id' => $data['tycoon_id']
        ]);
        TycoonWallet::where('tycoon_id', $data['tycoon_id'])->update([
            $data['wallet_type'] => auth()->user()->wallet[$data['wallet_type']] + (float) $data['amount']
        ]);
    }
}
