<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;

class DateAfterOneDayAtLeastRule implements ValidationRule
{
    protected string $dateFrom;

    public function __construct(string $dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dateFrom = Carbon::parse($this->dateFrom);
        $dateTo = Carbon::parse($value);

        if (!$dateTo->gt($dateFrom->addDay())) {
            $fail(__('تاريخ نهاية الفاتورة يجب أن يكون على الأقل بعد يوم واحد من تاريخ بداية الفاتورة '));
        }
    }
}
