<?php

namespace Tests\Unit\ImportedPayments;

use App;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Modules\Common\Entities\ImportedPayment;
use Modules\Common\Observers\ImportedPaymentObserver;
use Tests\TestCase;

class MatchInvestorTest extends TestCase
{
    use WithoutMiddleware;

    protected $observer;

    public function setUp(): void
    {
        parent::setUp();
        $this->observer = App::make(ImportedPaymentObserver::class);
    }

    public function testParsingPaymentDetails()
    {
        $sampleRows = $this->getDetailsSample();

        foreach ($sampleRows as $basis => $answer) {
            // dump($basis);


            // remove: 'SEPA получен кредитен превод - ', before starting regex match
            $basis = $this->observer->clearBasis($basis);


            // matches
            preg_match(ImportedPayment::INVESTOR_ID_REGEX, $basis, $mInvestorId);
            preg_match(ImportedPayment::IBAN_REGEX, $basis, $mIban);
            preg_match(ImportedPayment::BIC_REGEX, $basis, $mBic);



            // do validation
            $this->check($answer, 'investorId', $mInvestorId);
            $this->check($answer, 'iban', $mIban);
            $this->check($answer, 'bic', $mBic);


            // dd( $mInvestorId, $mBic, $mIban );
        }
    }

    private function check(array $answer, string $type, array $match): void
    {
        $method = 'extract' . ucfirst($type);
        $value = $this->observer->$method($match);
        // dump('type = ' . $type . ', sample:' . $answer[$type] . ', value:' . $value);

        if (empty($answer[$type])) {
            $this->assertEmpty($value);
        } else {
            $this->assertNotEmpty($value);
            $this->assertEquals($answer[$type], $value);
        }
    }

    private function getDetailsSample(): array
    {
        return [
            'SEPA получен кредитен превод - BIC: IBAN EE327700771005767305 No. 103833' => [
                'bic' => '',
                'iban' => 'EE327700771005767305',
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - BIC=LHVBEE22XXX IBAN EE327700771005767305 No. 103833' => [
                'bic' => 'LHVBEE22XXX',
                'iban' => 'EE327700771005767305',
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - BIC-LHVBEE22XXX IBAN EE327700771005767305 No. 103833'=> [
                'bic' => 'LHVBEE22XXX',
                'iban' => 'EE327700771005767305',
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - BIC LHVBEE22XXX IBAN EE327700771005767305 No. 103833'=> [
                'bic' => 'LHVBEE22XXX',
                'iban' => 'EE327700771005767305',
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - IBAN-CSDBDE7122XXX05767305 No. 103833'=> [
                'bic' => '',
                'iban' => '', // wrong iban
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - IBAN:CSDBDE7122XXX05767305 No. 103833'=> [
                'bic' => '',
                'iban' => '', // wrong iban
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - IBAN=CSDBDE7122XXX05767305 No. 103833'=> [
                'bic' => '',
                'iban' => '', // wrong iban
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - BICLHVBEE22XXX IBANEE327700771005767305 No. 103833'=> [
                'bic' => 'LHVBEE22XXX',
                'iban' => 'EE327700771005767305',
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - IBANEE327700771005767305 BICLHVBEE22XXX No. 103833'=> [
                'bic' => 'LHVBEE22XXX',
                'iban' => 'EE327700771005767305',
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - No. 103833 IBANEE327700771005767305 BICLHVBEE22XXX'=> [
                'bic' => 'LHVBEE22XXX',
                'iban' => 'EE327700771005767305',
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - No.103833 IBANEE327700771005767305 BICLHVBEE22XXX'=> [
                'bic' => 'LHVBEE22XXX',
                'iban' => 'EE327700771005767305',
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - N.103833 IBANEE327700771005767305 BICLHVBEE22XXX'=> [
                'bic' => 'LHVBEE22XXX',
                'iban' => 'EE327700771005767305',
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - #103833 IBANEE327700771005767305 BICLHVBEE22XXX'=> [
                'bic' => 'LHVBEE22XXX',
                'iban' => 'EE327700771005767305',
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - BIC LHVBEE22XXX IBAN EE327700771005767305 AppWolves OU - Investor No. 103833'=> [
                'bic' => 'LHVBEE22XXX',
                'iban' => 'EE327700771005767305',
                'investorId' => '103833',
            ],
            'SEPA получен кредитен превод - BIC COBADEHD044 IBAN DE90200411440667809800 Max Felix Mittelmaier - Investor No. 104095'=> [
                'bic' => 'COBADEHD044',
                'iban' => 'DE90200411440667809800',
                'investorId' => '104095',
            ],
            'SEPA получен кредитен превод - BIC COBADEHD001 IBAN DE49200411330677798100 Helmut Rieger - Investor No. 104020'=> [
                'bic' => 'COBADEHD001',
                'iban' => 'DE49200411330677798100',
                'investorId' => '104020',
            ],
            'SEPA получен кредитен превод - BIC EVIULT2VXXX IBAN LT573500010004749388 Stefan Stefanov - Investor No. 103896'=> [
                'bic' => 'EVIULT2VXXX',
                'iban' => 'LT573500010004749388',
                'investorId' => '103896',
            ],
            'SEPA получен кредитен превод - BIC EVIULT2VXXX IBAN LT963500010009907049 Veselin Georgiev - Investor No. 103771'=> [
                'bic' => 'EVIULT2VXXX',
                'iban' => 'LT963500010009907049',
                'investorId' => '103771',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT383250061694334175 Tomas Holub - Investor No. 104075'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT383250061694334175',
                'investorId' => '104075',
            ],
            'SEPA получен кредитен превод - BIC CAHMESMM IBAN ES1320386007133000903085 MARCOS FERRER BOU - Investor No. 104085'=> [
                'bic' => 'CAHMESMM',
                'iban' => 'ES1320386007133000903085',
                'investorId' => '104085',
            ],
            'SEPA получен кредитен превод - BIC NDEAFIHH IBAN FI4218853500016284 ETELAAHO ANNA XENIA TUULIKKI - Investor No. 103943'=> [
                'bic' => 'NDEAFIHH',
                'iban' => 'FI4218853500016284',
                'investorId' => '103943',
            ],
            'SEPA получен кредитен превод - BIC SBANFIHH IBAN FI1439390025878505 ANNA ETELAAHO - Investor No. 103943'=> [
                'bic' => 'SBANFIHH',
                'iban' => 'FI1439390025878505',
                'investorId' => '103943',
            ],
            'SEPA получен кредитен превод - IBAN FI1439390025878505 BIC SBANFIHH ANNA ETELAAHO - Investor No. 103943'=> [
                'bic' => 'SBANFIHH',
                'iban' => 'FI1439390025878505',
                'investorId' => '103943',
            ],
            'SEPA получен кредитен превод - IBANFI1439390025878505 BICSBANFIHH ANNA ETELAAHO - Investor No. 103943'=> [
                'bic' => 'SBANFIHH',
                'iban' => 'FI1439390025878505',
                'investorId' => '103943',
            ],
            'SEPA получен кредитен превод - IBANFI1439390025878505 - BICSBANFIHH ANNA ETELAAHO (103943)'=> [
                'bic' => 'SBANFIHH',
                'iban' => 'FI1439390025878505',
                'investorId' => '103943',
            ],
            'SEPA получен кредитен превод - BIC CSDBDE71 IBAN DE65760300800833178783 Ulrich Huefner - Investor No. 103762'=> [
                'bic' => 'CSDBDE71',
                'iban' => 'DE65760300800833178783',
                'investorId' => '103762',
            ],
            'SEPA получен кредитен превод - BIC CSDBDE71 IBAN DE94760300800803321467 Ulrike Biedermann - Investor No. 103986'=> [
                'bic' => 'CSDBDE71',
                'iban' => 'DE94760300800803321467',
                'investorId' => '103986',
            ],
            'SEPA получен кредитен превод - BIC NTSBDEB1XXX IBAN DE28100110012623024810 Jordi Estrada Ribes - Investor No. 103915'=> [
                'bic' => 'NTSBDEB1XXX',
                'iban' => 'DE28100110012623024810',
                'investorId' => '103915',
            ],
            'SEPA получен кредитен превод - BICNTSBDEB1XXX IBANDE56100110012620469423 Herve Duborjal - Investor No. 103763'=> [
                'bic' => 'NTSBDEB1XXX',
                'iban' => 'DE56100110012620469423',
                'investorId' => '103763',
            ],
            'SEPA получен кредитен превод - BIC NTSBDEB1XXX IBAN DE56100110012620469423 Herve Duborjal - Investor No. 103763'=> [
                'bic' => 'NTSBDEB1XXX',
                'iban' => 'DE56100110012620469423',
                'investorId' => '103763',
            ],
            'SEPA получен кредитен превод - BIC NTSBDEB1XXX IBANDE56100110012620469423 Herve Duborjal - Investor No. 103763'=> [
                'bic' => 'NTSBDEB1XXX',
                'iban' => 'DE56100110012620469423',
                'investorId' => '103763',
            ],
            'SEPA получен кредитен превод - BICGKCCBEBB IBAN BE70795564563025 ASSETTA MASCIA - Investor No. 104086'=> [
                'bic' => 'GKCCBEBB',
                'iban' => 'BE70795564563025',
                'investorId' => '104086',
            ],
            'SEPA получен кредитен превод - BIC GKCCBEBB IBAN BE70795564563025 ASSETTA MASCIA - Investor No. 104086'=> [
                'bic' => 'GKCCBEBB',
                'iban' => 'BE70795564563025',
                'investorId' => '104086',
            ],
            'SEPA получен кредитен превод - BIC SRLGGB3L IBAN GB29SRLG60837160364633 Nikola Popov - Investor #. 103761'=> [
                'bic' => 'SRLGGB3L',
                'iban' => 'GB29SRLG60837160364633',
                'investorId' => '103761',
            ],
            'SEPA получен кредитен превод - BIC BYLADEM1001 IBAN DE81120300001035948684 ALEXEY PUPYSHEV - Investor # 103867'=> [
                'bic' => 'BYLADEM1001',
                'iban' => 'DE81120300001035948684',
                'investorId' => '103867',
            ],
            'SEPA получен кредитен превод - BIC BYLADEM1001 IBAN DE60120300000012068375 DR. SVEN FRORMANN - Investor #104062'=> [
                'bic' => 'BYLADEM1001',
                'iban' => 'DE60120300000012068375',
                'investorId' => '104062',
            ],
            'SEPA получен кредитен превод - BIC OPENESMMXXX IBAN ES0200730100530154113296 MENDEZ VAQUERIZO LUIS - Investor No. 104103'=> [
                'bic' => 'OPENESMMXXX',
                'iban' => 'ES0200730100530154113296',
                'investorId' => '104103',
            ],
            'SEPA получен кредитен превод - BIC OPENESMMXXX IBAN ES4500730100510451267755 LOZANO TORRALVO ADOLFO - Investor No. 104005'=> [
                'bic' => 'OPENESMMXXX',
                'iban' => 'ES4500730100510451267755',
                'investorId' => '104005',
            ],
            'SEPA получен кредитен превод - BIC CGDIPTPL IBAN PT50003503100000689490003 RUI MIGUEL B COSTA GONCALVES - Investor No 103765'=> [
                'bic' => 'CGDIPTPL',
                'iban' => 'PT50003503100000689490003',
                'investorId' => '103765',
            ],
            'SEPA получен кредитен превод - BIC NORSDE51XXX IBAN DE92100777770246916100 Dustin Lukas Schreder - Investor No. 104059'=> [
                'bic' => 'NORSDE51XXX',
                'iban' => 'DE92100777770246916100',
                'investorId' => '104059',
            ],
            'SEPA получен кредитен превод - BIC BOFIIE2D IBAN IE80BOFI90276874845292 CHRISTOPH BERNATZKY - NOTPROVIDED'=> [
                'bic' => 'BOFIIE2D',
                'iban' => 'IE80BOFI90276874845292',
                'investorId' => '',
            ],
            'SEPA получен кредитен превод - BIC INGDDEFFXXX IBAN DE89500105175409552308 Dr. Michael Hofmann - Investor No. 104037'=> [
                'bic' => 'INGDDEFFXXX',
                'iban' => 'DE89500105175409552308',
                'investorId' => '104037',
            ],
            'SEPA получен кредитен превод - BIC DABBDEMM IBAN DE51701204007913641002 Dustin Lukas Schreder - Investor No. 104051'=> [
                'bic' => 'DABBDEMM',
                'iban' => 'DE51701204007913641002',
                'investorId' => '104051',
            ],
            'SEPA получен кредитен превод - BIC RZBCCZPPXXX IBAN CZ8755000000003750046001 Vladimir Ludik - Investor c. 104097'=> [
                'bic' => 'RZBCCZPPXXX',
                'iban' => 'CZ8755000000003750046001',
                'investorId' => '104097',
            ],
            'SEPA получен кредитен превод - BIC AIRACZPP IBAN CZ0830300000001881374022 David Kunssberger - Investor No. 103948'=> [
                'bic' => 'AIRACZPP',
                'iban' => 'CZ0830300000001881374022',
                'investorId' => '103948',
            ],
            'SEPA получен кредитен превод - BIC PBNKDEFFXXX IBAN DE68600100700190972708 THORSTEN SCHMIDT - Investor No. 104111'=> [
                'bic' => 'PBNKDEFFXXX',
                'iban' => 'DE68600100700190972708',
                'investorId' => '104111',
            ],
            'SEPA получен кредитен превод - BIC CEPAFRPP751 IBAN FR7617515000920405253561720 MR VALENTIN PATIER - INVESTOR NO. 104096 REGISTRATION NUMBER 202557159'=> [
                'bic' => 'CEPAFRPP751',
                'iban' => 'FR7617515000920405253561720',
                'investorId' => '104096',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen 103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen-103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen -103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen - 103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen - N103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen - N.103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen - #103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen - #.103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen - No103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen - No.103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen Investor 103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen Investor:103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen Investor - 103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC REVOLT21 IBAN LT423250007615558359 Brian Eg Hansen Investor.103902'=> [
                'bic' => 'REVOLT21',
                'iban' => 'LT423250007615558359',
                'investorId' => '103902',
            ],
            'SEPA получен кредитен превод - BIC NTSBDEB1XXX IBAN DE47100110012620901817 Stanislas Andre - Investor no 103738'=> [
                'bic' => 'NTSBDEB1XXX',
                'iban' => 'DE47100110012620901817',
                'investorId' => '103738',
            ],
            'SEPA получен кредитен превод - BIC RZBCCZPPXXX IBAN CZ8755000000003750046001 Vladimir Ludik - Investor c. 104097'=> [
                'bic' => 'RZBCCZPPXXX',
                'iban' => 'CZ8755000000003750046001',
                'investorId' => '104097',
            ],
            'SEPA получен кредитен превод  - BIC CEPAFRPP751 IBAN FR7617515000920405253561721 LILI DIDI - INVESTOR NO. 103749'=> [
                'bic' => 'CEPAFRPP751',
                'iban' => 'FR7617515000920405253561721',
                'investorId' => '103749',
            ],
            'SEPA получен кредитен превод  - BIC CEPAFRPP751 IBAN FR7617515000920405253561721 LILI DIDI - INVESTOR NO. 103749'=> [
                'bic' => 'CEPAFRPP751',
                'iban' => 'FR7617515000920405253561721',
                'investorId' => '103749',
            ],
            'SEPA получен кредитен превод  - BIC CEPAFRPP751 IBAN FR7617515000920405253561721 LILI DIDI - INVESTOR NO. 103749 REG NOMER 203044'=> [
                'bic' => 'CEPAFRPP751',
                'iban' => 'FR7617515000920405253561721',
                'investorId' => '103749',
            ],
            'SEPA получен кредитен превод - BIC CECBBGSFXXX IBAN BG42CECB979010F7694200 ZDRAVKO HRISTOV TODOROV - Investor 104203'=> [
                'bic' => 'CECBBGSFXXX',
                'iban' => 'BG42CECB979010F7694200',
                'investorId' => '104203',
            ],
       ];
    }
}
