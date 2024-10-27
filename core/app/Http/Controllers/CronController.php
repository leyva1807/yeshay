<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\CronJob;
use App\Lib\CurlRequest;
use App\Models\Currency;
use App\Constants\Status;
use App\Models\CronJobLog;

class CronController extends Controller
{
    public function fiatRate()
    {

        if (!gs('automatic_currency_rate_update')) return 0;

        try {
            $currencies = Currency::get();
            if ($currencies->count() > 0) {
                foreach ($currencies as $currency) {
                    $currencyRate              = $this->currencyRate($currency->cur_sym);
                    $percentDecValue           = $currencyRate/100*$currency->percent_decrease;
                    $percentInValue            = $currencyRate/100*$currency->percent_increase;
                    $currency->conversion_rate = $currencyRate;
                    $currency->buy_at          = $currencyRate - $percentDecValue;
                    $currency->sell_at         = $currencyRate + $percentInValue;
                    $currency->save();
                }
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    protected function currencyRate($currency)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://v6.exchangerate-api.com/v6/' . gs('currency_api_key') . '/pair/' . $currency . '/' . gs('cur_text'),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: text/plain",
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        return $response->conversion_rate;
    }

    public function cron()
    {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::YES);
        }
        $crons = $crons->get();
        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            $cron->next_run = now()->addSeconds($cron->schedule->interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }
        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }
        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }
}
