<?php

namespace App\Listeners\v1;

use App\Events\v1\DealerCommissionDistributionEvent as DealerCommissionDistributionEvent;
use App\Models\{TycoonCommissionHistory, TycoonWallet, TycoonGroupBonusConfig, TycoonBonusConfig, Tycoon, AdminWallet, Dealer, DealerBonusConfig, DealerCommission, DealerWallet};
use Illuminate\Support\Facades\Log;
use DB;

class DealerCommissionDistributionListener
{
    private $datas = [];

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
     * @param  \App\Events\DealerCommissionDistributionEvent  $event
     * @return void
     */
    public function handle(DealerCommissionDistributionEvent $event)
    {
        $data = $event->data;
        $dealer_bonus = DealerBonusConfig::where('dealer_type_id', Dealer::find($data['to_dealer_id'])->dealer_type_id)->first();
        if (!$dealer_bonus) {
            return false;
        }
        $this->sale_bonus_distribution($data, $dealer_bonus->commission);
    }

    // group bonus distribution here
    private function sale_bonus_distribution($data = [], $percentage = 0) {
        $toal_bonus = ($data['amount'] * $percentage) / 100;
        // dealer profit store
        DealerWallet::where('dealer_id', $data['to_dealer_id'])->update([
            'profit' => DB::raw('profit+'. $toal_bonus)
        ]);
        $this->storeBonusHistory($data, $toal_bonus);
    }

    // store commission history
    private function storeBonusHistory($data, $amount=0) :void {
        DealerCommission::create([
            'product_id' => $data['product_id'],
            'amount' => $amount,
            'to_dealer_id' => $data['to_dealer_id'],
            'tycoon_id' => $data['tycoon_id'],
            'from_dealer_id' => $data['from_dealer_id']
        ]);
    }

}
