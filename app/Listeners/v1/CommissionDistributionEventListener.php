<?php

namespace App\Listeners\v1;

use App\Events\v1\CommissionDistributionEvent as CommissionDistributionEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\{TycoonCommissionHistory, TycoonWallet, TycoonGroupBonusConfig, TycoonBonusConfig, Tycoon, AdminWallet};
use Illuminate\Support\Facades\Log;

class CommissionDistributionEventListener
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
     * @param  \App\Events\CommissionDistributionEvent  $event
     * @return void
     */
    public function handle(CommissionDistributionEvent $event)
    {

        $tycoon_bonus = TycoonBonusConfig::get();
        $instant_sale = $tycoon_bonus->firstWhere('bonus_type', 'instant_sale')->bonus_percentage;
        $group_bonus = $tycoon_bonus->firstWhere('bonus_type', 'group_bonus')->bonus_percentage;
        // $adk_provident_fund = $tycoon_bonus->firstWhere('bonus_type', 'adk_provident_fund')->bonus_percentage;
        // $tycoon_provident_fund = $tycoon_bonus->firstWhere('bonus_type', 'tycoon_provident_fund')->bonus_percentage;
        // $dealer_ref_comm = $tycoon_bonus->firstWhere('bonus_type', 'dealer_ref_comm')->bonus_percentage;
        // $monthly_star_bonus = $tycoon_bonus->firstWhere('bonus_type', 'monthly_star_bonus')->bonus_percentage;
        // $monthly_sallary = $tycoon_bonus->firstWhere('bonus_type', 'monthly_sallary')->bonus_percentage;
       
        $data = $event->data;
        $amount = 0;
        switch ($data['bonus_type']) {
            case 'instant_sale':
                $this->instant_sale_distribution($data, $instant_sale);
            break;
            case 'group_bonus':
                $this->group_bonus_distribution($data, $group_bonus);
            break;
        }
    }

    // group bonus distribution here
    private function group_bonus_distribution($data = [], $percentage = 0) {
        $toal_bonus = ($data['amount'] * $percentage) / 100;
        $tycoons = Tycoon::get();
        $group_1 = Tycoon::where('user_id', auth()->user()->placement_id)->first();

        if(!$group_1) {
            // update adk wallet
            $masterTycoon = AdminWallet::first();
            $masterTycoon->tycoon_group_commission_gap = $masterTycoon->tycoon_group_commission_gap + $toal_bonus;
            $masterTycoon->save();
            $data['tycoon_id'] = 1;
            $data['type'] = 1;
            $this->storeBonudHistory($data, $toal_bonus);
            return true;
        }

        array_push($this->datas, $group_1);
        foreach($tycoons as $tycoon) {
            if ($tycoon->user_id == $group_1->placement_id) {
                array_push($this->datas, $tycoon);
                // $this->tycoon($tycoons, $tycoon);
            }
        }
        $groupBonus= TycoonGroupBonusConfig::orderBy('group_no', 'asc')->get();
        $total_pay = 0;
        foreach($this->datas as $key=>$value) {
            if(20 > $key) {
                $amount = ($toal_bonus * $groupBonus[$key]->bonus_percentage) / 100;
                $this->updateWallet('group_commission', $amount, $data['tycoon_id']);
                $data['tycoon_id'] = $value->id;
                $this->storeBonudHistory($data, $amount);
                $total_pay += $amount;
            }
        }

        // update adk wallet
        $total_gap = $toal_bonus - $total_pay;
        if ($total_gap > 0) {
            $masterTycoon = AdminWallet::first();
            $masterTycoon->tycoon_group_commission_gap = $masterTycoon->tycoon_group_commission_gap + $total_gap;
            $masterTycoon->update();
        }

    }

    private function tycoon ($tycoons, $tycoon) {
        foreach($tycoons as $tyc) {
            if ($tyc->user_id == $tycoon->placement_id) {
                array_push($this->datas, $tyc);
                $this->tycoon($tycoons, $tyc);
            }
        }
    }

    // instant sale distribution here
    private function instant_sale_distribution($data = [], $percentage = 0) :void {
        $amount = ($data['amount'] * $percentage) / 100;
        $this->updateWallet('sales_commission', $amount, $data['tycoon_id']);
        $this->storeBonudHistory($data, $amount);
    }

    /**  commission and wallet balance update here  **/

    // update tycoon wallet
    private function updateWallet($wallet_type = null, $amount = 0, $tycoon_id=0) :void {
        $tycoonWallet = TycoonWallet::where('tycoon_id', $tycoon_id)->first();
        $tycoonWallet[$wallet_type] = ($tycoonWallet[$wallet_type] +  (float) $amount);
        $tycoonWallet->save();
    }

    // store commission history
    private function storeBonudHistory($data, $amount=0) :void {
        TycoonCommissionHistory::create([
            'bonus_type' => $data['bonus_type'],
            'product_id' => $data['product_id'],
            'amount' => $amount,
            'tycoon_id' => $data['tycoon_id']
        ]);
    }
}
