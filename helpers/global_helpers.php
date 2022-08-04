<?php

use App\Http\Middleware\VerifyCsrfToken;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\ImportedPayment;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Transaction;

if (!function_exists('isProdOrStage')) {
    /**
     * Check if out environment should execute real checks for ThirdParty apps
     *
     * @return bool
     */
    function isProdOrStage(): bool
    {
        $env = env('APP_ENV', 'dev');
        return in_array(
            $env,
            [
                'prod',
                'stage',
            ]
        );
    }
}

if (!function_exists('isProd')) {
    /**
     * Check if out environment should execute real checks for ThirdParty apps
     *
     * @return bool
     */
    function isProd(): bool
    {
        $env = env('APP_ENV', 'test');
        return in_array(
            $env,
            [
                'prod',
            ]
        );
    }
}

if (!function_exists('amount')) {
    /**
     * Transform: 1000.00 -> € 1 000.00
     */
    function amount(?float $amount, $currencySign = '€'): string
    {
        return $currencySign . ' ' . number_format($amount, 2, '.', ' ');
    }
}

if (!function_exists('amountReport')) {
    /**
     * Transform: 1000.00 -> € 1,000.00
     */
    function amountReport(float $amount = null, $currencySign = ''): string
    {
        if (empty($amount)) {
            return '0.00';
        }

        return (!empty($currencySign) ? $currencySign . ' ' : '')
            . number_format($amount, 2, '.', ',');
    }
}

if (!function_exists('percentReport')) {
    /**
     * Transform: 1000.00 -> € 1,000.00
     */
    function percentReport(float $amount = null, $percentSign = ''): string
    {
        if (empty($amount)) {
            return '0.0';
        }

        return (!empty($percentSign) ? $percentSign . ' ' : '')
            . number_format($amount, 1, '.', ',');
    }
}

if (!function_exists('payStatus')) {
    /**
     * Transform: current -> Current
     * Transform: 15-30 days -> 15-30 days late
     * Transform: late -> Late
     *
     * @param string|null $paymentStatus
     * @param null $loan
     *
     * @return string
     */
    function payStatus(string $paymentStatus = null, $loan = null): string
    {
        if (empty($paymentStatus)) {
            return '';
        }

        if ($paymentStatus == Loan::PAY_STATUS_LATE ) {
            return Loan::PAY_STATUS_60_PLUS_DAYS_LATE;
        }
        if (
            preg_match("/(\s|\-)/", $paymentStatus)
            && !preg_match('/late/i', $paymentStatus)
        ) {
            return $paymentStatus . ' late';
        }

        return ucfirst($paymentStatus);
    }
}


if (!function_exists('paymentStatus')) {
    /**
     * Transform: late -> Late
     * Transform: scheduled -> Scheduled
     * Transform: paid late -> Paid late
     *
     * @param string|null $paymentStatus
     *
     * @return string
     */
    function paymentStatus(string $paymentStatus = null): string
    {
        if (empty($paymentStatus)) {
            return '';
        }

        if (
            preg_match("/(\s|\-)/", $paymentStatus)
            && !preg_match('/late/i', $paymentStatus)
        ) {
            return $paymentStatus . ' late';
        }

        return ucfirst($paymentStatus);
    }
}

if (!function_exists('payStatusCharts')) {
    /**
     * Transform: current -> Current
     * Transform: 15-30 days -> 15-30 Days
     * Transform: late -> 60+ Days
     *
     * @param string|null $paymentStatus
     * @param bool|false $late
     *
     * @return string
     */
    function payStatusCharts(string $paymentStatus = null, $late = false): string
    {
        if (empty($paymentStatus)) {
            return '';
        }

        if ($paymentStatus == Loan::PAY_STATUS_LATE && $late == true
        ) {
            return Loan::PAY_STATUS_60_PLUS_DAYS_LATE;
        } elseif ($paymentStatus == Loan::PAY_STATUS_LATE) {
            return Loan::PAY_STATUS_60_PLUS_DAYS;
        } else {
            $paymentStatus = ucwords($paymentStatus);
        }

        return $paymentStatus;
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, $format = Setting::SHOW_FORMAT)
    {
        return Carbon::parse($date)->format($format);
    }
}

if (!function_exists('showDate')) {
    function showDate($date, string $additional = null): string
    {
        if (empty($date)) {
            return '';
        }

        $format = Setting::SHOW_FORMAT;
        if (!empty($additional)) {
            $format = $format . ' ' . $additional;
        }

        return formatDate($date, $format);
    }
}

if (!function_exists('dbDate')) {
    function dbDate($date, string $additional = null)
    {
        $format = Setting::DB_DATE_FORMAT;
        if (!empty($additional)) {
            $format = $format . ' ' . $additional;
        }

        return formatDate($date, $format);
    }
}

if (!function_exists('loanType')) {
    function loanType(string $type, bool $viceVersa = false)
    {
        $map = [
            Loan::TYPE_INSTALLMENTS => Loan::LABEL_TYPE_INSTALLMENT,
            Loan::TYPE_PAYDAY => Loan::LABEL_TYPE_PAYDAY
        ];

        if ($viceVersa) {
            $map = array_flip($map);
        }
        if (empty($map[$type])) {
            return null;
        }
        return $map[$type];
    }
}

if (!function_exists('loanTypeJson')) {
    function loanTypeJson(string $types)
    {
        $map = [
            Loan::TYPE_INSTALLMENTS => Loan::LABEL_TYPE_INSTALLMENT,
            Loan::TYPE_PAYDAY => Loan::LABEL_TYPE_PAYDAY
        ];

        $typesArr = json_decode($types, true);
        if (!isset($typesArr['type'])) {
            return '';
        }
        $c = '';

        foreach ($typesArr['type'] as $type) {
            $c .= $map[$type];
            if (next($typesArr['type']) == true) {
                $c .= ", ";
            }
        }

        return $c;
    }
}

if (!function_exists('loanPaymentStatusJson')) {
    function loanPaymentStatusJson(string $types)
    {
        $typesArr = json_decode($types, true);

        if (!isset($typesArr['payment_status'])) {
            return '';
        }
        $c = '';

        foreach ($typesArr['payment_status'] as $type) {
            $c .= payStatus($type);
            if (next($typesArr['payment_status']) == true) {
                $c .= ", ";
            }
        }

        return $c;
    }
}

if (!function_exists('rate')) {

    function rate(float $value = null)
    {
        if (empty($value)) {
            return '';
        }

        return number_format($value, 1) . ' ' . '%';
    }
}

if (!function_exists('rateExport')) {

    function rateExport(float $value = null) :string
    {
        if (empty($value)) {
            return '';
        }
        return number_format($value,2);
    }
}


if (!function_exists('termFormat')) {
    function termFormat(string $date): string
    {
        if (empty($date)) {
            return '';
        }

        $date = Carbon::parse($date);
        $now = Carbon::now();
        if ($now->gt($date)) {
            return '0m. 0d.';
        }

        $diff = $date->diff($now);
        $month = $diff->m;
        if ($diff->y > 0) {
            $month += ($diff->y * 12);
        }
        return $month . 'm. ' . $diff->d . 'd.';
    }
}

if (!function_exists('shuffle_assoc')) {
    function shuffle_assoc(&$array)
    {
        $keys = array_keys($array);
        shuffle($keys);

        $new = [];
        foreach ($keys as $key) {
            $new[$key] = $array[$key];
        }

        $array = $new;
        return true;
    }
}

if (!function_exists('autoInvestRate')) {
    function autoInvestRate(float $minRate = null, float $maxRate = null)
    {
        $value = '';

        if (empty($maxRate)) {
            $value .= 'from ';
        }
        if (!empty($minRate)) {
            $value .= rate($minRate);
        }
        if (empty($minRate)) {
            $value .= 'up to ';
        }

        if (!empty($maxRate) && !empty($minRate)) {
            $value .= ' - ';
        }

        if (!empty($maxRate)) {
            $value .= rate($maxRate);
        }

        if (empty($maxRate) && empty($minRate)) {
            $value = ' - ';
        }

        return $value;
    }
}

if (!function_exists('autoInvestPeriod')) {
    function autoInvestPeriod(float $minPeriod = null, float $maxPeriod = null)
    {
        $value = '';

        if (empty($maxPeriod)) {
            $value .= 'from ';
        }
        if (!empty($minPeriod)) {
            $value .= $minPeriod . ' m.';
        }
        if (empty($minPeriod)) {
            $value .= 'up to ';
        }

        if (!empty($maxPeriod) && !empty($minPeriod)) {
            $value .= ' - ';
        }

        if (!empty($maxPeriod)) {
            $value .= $maxPeriod . ' m.';
        }

        if (empty($maxPeriod) && empty($minPeriod)) {
            $value = ' - ';
        }

        return $value;
    }
}

if (!function_exists('loanStatus')) {
    function loanStatus(string $status): string
    {
        if ($status == Loan::STATUS_NEW) {
            return Loan::LABEL_NOT_READY;
        }

        if ($status == Loan::STATUS_ACTIVE) {
            return Loan::LABEL_ACTIVE;
        }

        return Loan::LABEL_FINISHED;
    }
}


if (!function_exists('admin_csrf')) {
    function admin_csrf()
    {
        $session = app('session');

        if (isset($session)) {
            return $session->get(VerifyCsrfToken::ADMIN_CSRF_TOKEN_NAME);
        }

        throw new RuntimeException('Application session store not set.');
    }
}

if (!function_exists('admin_csrf_field')) {
    /**
     * Generate a CSRF admin token form field.
     *
     * @return HtmlString
     */
    function admin_csrf_field()
    {
        return new HtmlString(
            '<input type="hidden" name="_token_admin" value="' . (admin_csrf()) . '">'
        );
    }
}

if (!function_exists('assets_version')) {
    function assets_version(string $path): string
    {
        return ($path . '?v=' . config('app.assets_version', 1));
    }
}

if (!function_exists('get_device')) {
    /**
     * @return \Mobile_Detect
     */
    function get_device()
    {
        return new \Mobile_Detect();
    }
}

if (!function_exists('getNameFromBasis')) {
    /**
     * @param string $basis
     *
     * @return string
     */
    function getNameFromBasis(string $basis)
    {
        preg_match(ImportedPayment::NAME_REGEX, $basis, $matches);

        return count($matches) > 3 ? $matches[3] : '';
    }
}

if (!function_exists('paidBeforeListing')) {
    function paidBeforeListing($installment)
    {
        if ($installment->paid === 1 && empty($installment->paid_at)) {
            return true;
        }

        if ($installment->paid === 1 && !empty($installment->paid_at)) {
            return false;
        }
    }
}
