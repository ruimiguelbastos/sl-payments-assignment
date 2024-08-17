<?php

namespace App;

use App\Money\Money;
use App\Util\Date;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property Carbon $current_period_end
 * @property Carbon|null $trial_start
 * @property Carbon|null $trial_end
 * @property Carbon|null $cancel_at
 */
class Subscription extends Model
{
    protected $casts = [
        'current_period_end' => 'date',
        'trial_start'        => 'date',
        'trial_end'          => 'date',
        'cancel_at'          => 'date',
    ];

    private Money|null $calculatedPrice = null;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function discount(): HasOne
    {
        return $this->hasOne(Discount::class);
    }

    public function getCalculatedPrice(): Money
    {
        return $this->calculatedPrice;
    }

    public function belongsToProduct(Product $product): bool
    {
        return $this->plan->belongsToProduct($product);
    }

    public function getCustomerEmail(): string
    {
        return $this->customer->email;
    }

    public function calculateAmoutForPeriod(Carbon $periodToCalculate): Money
    {
        $this->calculatedPrice = Money::fromFloat($this->plan->amount);

        return $this->withCalculatedTrialPeriod($periodToCalculate)
            ->withCalculatedCancelation($periodToCalculate)
            ->withCalculatedMonthBillingWithoutDiscounts($periodToCalculate)
            ->withCalculatedDiscounts($periodToCalculate)
            ->calculatedPrice;
    }

    private function withCalculatedTrialPeriod(Carbon $periodToCalculate): self
    {
        $self = clone $this;

        if ($this->trial_end === null) {
            return $self;
        }

        if($this->trial_end->greaterThan($periodToCalculate) && $this->trial_end->month !== $periodToCalculate->month) {
            $self->calculatedPrice = Money::zero();
        }

        return $self;
    }

    private function withCalculatedCancelation(Carbon $periodToCalculate): self
    {
        $self = clone $this;

        if ($this->cancel_at === null || $this->cancel_at->isAfter($periodToCalculate)) {
            return $self;
        }

        $self->calculatedPrice = Money::zero();

        return $self;
    }

    private function withCalculatedMonthBillingWithoutDiscounts(Carbon $periodToCalculate): self
    {
        $self = clone $this;

        if (Date::isSameMonthFromSameYear($this->current_period_end, $periodToCalculate)) {
            return $self;
        }

        if ($this->plan->isBeingBilledInPeriod($periodToCalculate, $this->calculateTrialPeriodInMonths())) {
            return $self;
        }

        $self->calculatedPrice = Money::zero();

        return $self;
    }

    private function withCalculatedDiscounts(Carbon $periodToCalculate): self
    {
        $self = clone $this;

        if ($this->discount === null) {
            return $self;
        }

        $self->calculatedPrice = $this->discount->calculateForPeriodAndPrice(
            $periodToCalculate,
            $self->calculatedPrice
        );

        return $self;
    }

    private function calculateTrialPeriodInMonths(): int
    {
        $trialStart = $this->trial_start;
        $trialEnd   = $this->trial_end;

        if ($trialStart === null || $trialEnd === null) {
            return 0;
        }

        return round($trialStart->diffInMonths($trialEnd));
    }
}
