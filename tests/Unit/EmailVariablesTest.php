<?php

namespace Tests\Unit;

use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Modules\Communication\Entities\Email;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Communication\Services\EmailService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;
use Faker\Factory as Faker;

class EmailVariablesTest extends TestCase
{
    use WithoutMiddleware;
    use TestDataTrait;

    protected $emailService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emailService = \App::make(EmailService::class);
    }

    public function testSendDepositEmail()
    {
        $investor = $this->prepareInvestor('investor_email_test' . time() . '@test.com');

        $data = [
            'timestamp' => Carbon::now(),
            'Transaction' => [
                'amount' => 100,
                'transaction_id' => 20
            ]
        ];

        $sendEmail = $this->emailService->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['deposit_template']['id'],
            $investor->email,
            Carbon::now(),
            $data
        );

        $this->assertTrue($sendEmail);

        $email = Email::where(
            [
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['deposit_template']['id'],
                'investor_id' => $investor->investor_id,
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertStringContainsString($investor->first_name, $email->text);
        $this->assertStringContainsString($data['timestamp']->toDateTimeString(), $email->text);
        $this->assertStringContainsString((string)$data['Transaction']['amount'], $email->text);
        $this->assertStringContainsString((string)$data['Transaction']['transaction_id'], $email->text);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testSendLoginEmail()
    {
        $investor = $this->prepareInvestor('investor_email_test' . time() . '@test.com');
        $faker = Faker::create();

        $data = [
            'location' => $faker->ipv4,
            'timestamp' => Carbon::now()
        ];

        $sendEmail = $this->emailService->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['login_template']['id'],
            $investor->email,
            Carbon::now(),
            $data
        );

        $this->assertTrue($sendEmail);

        $email = Email::where(
            [
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['login_template']['id'],
                'investor_id' => $investor->investor_id,
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertStringContainsString($investor->first_name, $email->text);
        $this->assertStringContainsString($data['timestamp']->toDateTimeString(), $email->text);
        $this->assertStringContainsString((string)$data['location'], $email->text);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testSendPasswordChangedEmail()
    {
        $investor = $this->prepareInvestor('investor_email_test' . time() . '@test.com');

        $sendEmail = $this->emailService->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['password_changed']['id'],
            $investor->email,
            Carbon::now()
        );

        $this->assertTrue($sendEmail);

        $email = Email::where(
            [
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['password_changed']['id'],
                'investor_id' => $investor->investor_id,
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertStringContainsString($investor->first_name, $email->text);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testSendWelcomeEmail()
    {
        $investor = $this->prepareInvestor('investor_email_test' . time() . '@test.com');

        $sendEmail = $this->emailService->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['welcome_template']['id'],
            $investor->email,
            Carbon::now()
        );

        $this->assertTrue($sendEmail);

        $email = Email::where(
            [
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['welcome_template']['id'],
                'investor_id' => $investor->investor_id,
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertStringContainsString($investor->first_name, $email->text);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testSendWithdrawEmail()
    {
        $investor = $this->prepareInvestor('investor_email_test' . time() . '@test.com');

        $data = [
            'timestamp' => \Illuminate\Support\Carbon::now(),
            'Transaction' => [
                'amount' => 50,
            ]
        ];

        $sendEmail = $this->emailService->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['withdrawal_template']['id'],
            $investor->email,
            Carbon::now(),
            $data
        );

        $this->assertTrue($sendEmail);

        $email = Email::where(
            [
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['withdrawal_template']['id'],
                'investor_id' => $investor->investor_id,
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertStringContainsString($investor->first_name, $email->text);
        $this->assertStringContainsString($data['timestamp']->toDateTimeString(), $email->text);
        $this->assertStringContainsString((string)$data['Transaction']['amount'], $email->text);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testSendForgotPasswordEmail()
    {
        $investor = $this->prepareInvestor('investor_email_test' . time() . '@test.com');

        $faker = Faker::create();
        $data = [
            'restorePasswordUrl' => $faker->md5,
        ];

        $sendEmail = $this->emailService->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['forgot_password']['id'],
            $investor->email,
            Carbon::now(),
            $data
        );

        $this->assertTrue($sendEmail);

        $email = Email::where(
            [
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['forgot_password']['id'],
                'investor_id' => $investor->investor_id,
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertStringContainsString($investor->first_name, $email->text);
        $this->assertStringContainsString((string)$data['restorePasswordUrl'], $email->text);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testSendContinueRegistrationEmail()
    {
        $investor = $this->prepareInvestor('investor_email_test' . time() . '@test.com');

        $sendEmail = $this->emailService->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_registration']['id'],
            $investor->email,
            Carbon::now()
        );

        $this->assertTrue($sendEmail);

        $email = Email::where(
            [
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_registration']['id'],
                'investor_id' => $investor->investor_id,
            ]
        )->first();

        $this->assertNotEmpty($email);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testSendContinueVerificationEmail()
    {
        $investor = $this->prepareInvestor('investor_email_test' . time() . '@test.com');

        $sendEmail = $this->emailService->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_verification']['id'],
            $investor->email,
            Carbon::now()
        );

        $this->assertTrue($sendEmail);

        $email = Email::where(
            [
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_verification']['id'],
                'investor_id' => $investor->investor_id,
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertStringContainsString($investor->first_name, $email->text);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testSendReferralEmail()
    {
        $investor = $this->prepareInvestor('investor_email_test' . time() . '@test.com');

        $faker = Faker::create();
        $data = [
            'referral_link' => $faker->url,
        ];

        $sendEmail = $this->emailService->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['referral_link']['id'],
            $investor->email,
            Carbon::now(),
            $data
        );

        $this->assertTrue($sendEmail);

        $email = Email::where(
            [
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['referral_link']['id'],
                'investor_id' => $investor->investor_id,
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertStringContainsString($investor->first_name, $email->text);
        $this->assertStringContainsString((string)$data['referral_link'], $email->text);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testSendWrongLoginAttemptsEmail()
    {
        $investor = $this->prepareInvestor('investor_email_test' . time() . '@test.com');

        $sendEmail = $this->emailService->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['wrong_login_attempts']['id'],
            $investor->email,
            Carbon::now()
        );

        $this->assertTrue($sendEmail);

        $email = Email::where(
            [
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['wrong_login_attempts']['id'],
                'investor_id' => $investor->investor_id,
            ]
        )->first();

        $this->assertNotEmpty($email);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testSendVerificationEmail()
    {
        $investor = $this->prepareInvestor('investor_email_test' . time() . '@test.com');

        $sendEmail = $this->emailService->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['verification']['id'],
            $investor->email,
            Carbon::now()
        );

        $this->assertTrue($sendEmail);

        $email = Email::where(
            [
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['verification']['id'],
                'investor_id' => $investor->investor_id,
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertStringContainsString($investor->first_name, $email->text);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testSendReferralRegisteredEmail()
    {
        $investor = $this->prepareInvestor('investor_email_test' . time() . '@test.com');

        $faker = Faker::create();
        $data = [
            'referralFirstName' => $faker->name,
        ];

        $sendEmail = $this->emailService->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['referral_email']['id'],
            $investor->email,
            Carbon::now(),
            $data
        );

        $this->assertTrue($sendEmail);

        $email = Email::where(
            [
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['referral_email']['id'],
                'investor_id' => $investor->investor_id,
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertStringContainsString((string)$data['referralFirstName'], $email->text);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }
}
