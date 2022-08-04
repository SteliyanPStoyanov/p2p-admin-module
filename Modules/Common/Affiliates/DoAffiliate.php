<?php

namespace Modules\Common\Affiliates;

use Carbon\Carbon;
use Exception;
use Modules\Common\Entities\Transaction;
use Modules\Common\Repositories\AffiliateStatsRepository;

class DoAffiliate
{

    private const TYPE_CPL = 'CPL';
    private const TYPE_CPS = 'CPS';
    public const AFFILIATE_DAYS = 120;

    public array $queryParameters;

    /**
     * AffiliateEvents constructor.
     * @param $queryParameters
     */
    public function __construct($queryParameters)
    {
        $this->queryParameters = $queryParameters;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->queryParameters['v'];
    }

    /**
     * @return mixed
     */
    public function getCampaign()
    {
        return $this->queryParameters['utm_campaign'];
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->queryParameters['utm_source'];
    }

    /**
     * @param Transaction $transaction
     * @return bool
     */
    public function sendDepositPost(Transaction $transaction): bool
    {
        $data = [
            'type' => self::TYPE_CPL,
            'lead' => $transaction->transaction_id,
            'v' => $transaction->investor->getAffiliateClientId(),
            'investorId' => $transaction->investor_id
        ];

        return self::sendCurl($transaction, $data);
    }

    /**
     * @param Transaction $transaction
     * @return bool
     */
    public function sendInvestmentPost(Transaction $transaction): bool
    {
        $data = [
            'type' => self::TYPE_CPS,
            'lead' => $transaction->transaction_id,
            'v' => $transaction->investor->getAffiliateClientId(),
            'totalCost' => $transaction->amount,
            'investorId' => $transaction->investor_id
        ];

        return self::sendCurl($transaction, $data);
    }

    /**
     * @param Transaction $transaction
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function sendCurl(Transaction $transaction, array $data): bool
    {
        $apiUrl = env('DOAFFILIATE_CALLBACK_URL') . '?' . http_build_query($data);

        $affiliate = \App::make(AffiliateStatsRepository::class)->create(
            [
                'investor_id' => $transaction->investor_id,
                'affiliate_id' => $transaction->investor->affiliateInvestors->first()->affiliate_id,
                'send_data' => json_encode($data),
                'api_address' => $apiUrl,
                'send_at' => Carbon::now()
            ]
        );


        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                ),
            )
        );

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            return false;
        }

        if (empty($response)) {
            $response = 'no response';
        }

        $affiliate->received_at = Carbon::now();
        $affiliate->response = self::formatResponse($response);
        $affiliate->save();

        return true;
    }

    /**
     * @param $response
     * @return false|string
     */
    public function formatResponse($response)
    {
        $str = '<xml>' . htmlspecialchars_decode(strip_tags($response)) . '</xml>';

        $str = simplexml_load_string($str);

        return json_encode($str);
    }
}
