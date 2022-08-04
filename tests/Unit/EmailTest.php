<?php

namespace Tests\Unit;

use App;
use Artisan;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Modules\Admin\Entities\Administrator;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Agreement;
use Modules\Common\Entities\BlockedIp;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestorAgreement;
use Modules\Common\Entities\LoginAttempt;
use Modules\Common\Entities\Task;
use Modules\Common\Events\RestorePassword;
use Modules\Common\Services\InvestorLoginLogService;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\LoginAttemptService;
use Modules\Common\Services\TaskService;
use Modules\Communication\Entities\Email;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Communication\Services\EmailService;
use Tests\TestCase;
use Faker\Factory as Faker;
use Tests\Traits\TestDataTrait;

class EmailTest extends TestCase
{
    use TestDataTrait;
    use WithoutMiddleware;

    protected $investorLoginLogService;
    protected $investorService;
    protected $loginAttemptService;
    protected $taskService;
    protected $emailService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->investorLoginLogService = App::make(InvestorLoginLogService::class);
        $this->investorService = App::make(InvestorService::class);
        $this->loginAttemptService = App::make(LoginAttemptService::class);
        $this->taskService = App::make(TaskService::class);
        $this->emailService = App::make(EmailService::class);
    }

    public function testDailyRegisterCallSendEmail()
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');
        $investor->status = Investor::INVESTOR_STATUS_UNREGISTERED;
        $investor->created_at = Carbon::yesterday();
        $investor->save();

        Artisan::call('script:daily-register-recall');

        $email = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_registration']['id'],
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertEquals($investor->email, $email->sender_to);

        // Check will we send him another email the next day
        $investor->created_at = Carbon::today()->subDays(2);
        $investor->unregistered_recall_at = Carbon::yesterday();
        $investor->save();
        Artisan::call('script:daily-register-recall');
        $emails = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_registration']['id'],
            ]
        )->get();
        $this->assertCount(1, $emails);

        $investor->created_at = Carbon::today()->subDays(3);
        $investor->save();
        Artisan::call('script:daily-register-recall');
        $emails = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_registration']['id'],
            ]
        )->get();
        $this->assertCount(2, $emails);

        $investor->created_at = Carbon::today()->subDays(5);
        $investor->unregistered_recall_at = Carbon::yesterday();
        $investor->save();
        Artisan::call('script:daily-register-recall');
        $emails = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_registration']['id'],
            ]
        )->get();
        $this->assertCount(2, $emails);

        $investor->created_at = Carbon::today()->subDays(14);
        $investor->unregistered_recall_at = Carbon::yesterday();
        $investor->save();
        Artisan::call('script:daily-register-recall');
        $emails = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_registration']['id'],
            ]
        )->get();
        $this->assertCount(3, $emails);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testDailyVerifyCallSendEmail()
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');
        $investor->status = Investor::INVESTOR_STATUS_REGISTERED;
        $investor->created_at = Carbon::yesterday();
        $investor->save();

        Artisan::call('script:daily-verify-recall');

        $email = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_verification']['id'],
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertEquals($investor->email, $email->sender_to);

        // Check will we send him another email the next day
        $investor->created_at = Carbon::today()->subDays(2);
        $investor->registered_recall_at = Carbon::yesterday();
        $investor->save();
        Artisan::call('script:daily-verify-recall');
        $emails = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_verification']['id'],
            ]
        )->get();
        $this->assertCount(1, $emails);

        $investor->created_at = Carbon::today()->subDays(7);
        $investor->save();
        Artisan::call('script:daily-verify-recall');
        $emails = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_verification']['id'],
            ]
        )->get();
        $this->assertCount(2, $emails);

        $investor->created_at = Carbon::today()->subDays(10);
        $investor->registered_recall_at = Carbon::yesterday();
        $investor->save();
        Artisan::call('script:daily-verify-recall');
        $emails = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_verification']['id'],
            ]
        )->get();
        $this->assertCount(2, $emails);

        $investor->created_at = Carbon::today()->subDays(21);
        $investor->registered_recall_at = Carbon::yesterday();
        $investor->save();
        Artisan::call('script:daily-verify-recall');
        $emails = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_verification']['id'],
            ]
        )->get();
        $this->assertCount(3, $emails);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testForgottenPasswordSendEmail()
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');

        event(new RestorePassword($investor));

        $email = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['forgot_password']['id'],
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertEquals($investor->email, $email->sender_to);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testReferralSendEmail()
    {
        $parentInvestor = $this->prepareInvestor('investor_email' . time() . '@test.bg');

        $faker = Faker::create();
        $investor = new Investor();
        $investor->fill(
            [
                'referral_id' => $parentInvestor->investor_id,
                'email' => $faker->email,
            ]
        );
        $investor->save();
        $investor->refresh();
        $investor->status = Investor::INVESTOR_STATUS_REGISTERED;
        $investor->first_name = $faker->firstName;
        $investor->save();

        $email = Email::where(
            [
                'investor_id' => $parentInvestor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['referral_email']['id'],
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertEquals($parentInvestor->email, $email->sender_to);

        DB::table('email')->where('investor_id', $parentInvestor->investor_id)->delete();
        $this->removeTestData($parentInvestor);
        $this->removeTestData($investor);
    }

    public function testLoginLogSendEmail()
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');

        $faker = Faker::create();

        $this->investorLoginLogService->create($faker->word, $investor->investor_id, true);

        $email = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['login_template']['id'],
            ]
        )->first();

        $this->assertNotEmpty($email);
        $this->assertEquals($investor->email, $email->sender_to);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testWithdrawRequestMadeSendEmail()
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');
        $bankAccount = $investor->mainBankAccount();
        $wallet = $this->prepareWallet($investor);
        $portfolios = $this->preparePortfolios($investor);

        $faker = Faker::create();

        $this->actingAs($investor, 'investor');
        $this->investorService->makeWithdrawTask($faker->randomFloat(2, 10, 120), $bankAccount->bank_account_id);

        $email = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['withdrawal_template']['id'],
            ]
        )->first();

        // Dont have user agreement. Must not have email
        $this->assertEmpty($email);

        // Lets create user agreement
        $userAgreement = new InvestorAgreement();
        $userAgreement->fill(
            [
                'investor_id' => $investor->investor_id,
                'agreement_id' => Agreement::WITHDRAW_REQUEST_NOTIFICATION_ID,
                'value' => 1,
            ]
        );
        $userAgreement->save();

        $this->investorService->makeWithdrawTask($faker->randomFloat(2, 10, 120), $bankAccount->bank_account_id);

        $email = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['withdrawal_template']['id'],
            ]
        )->first();
        $this->assertNotEmpty($email);
        $this->assertEquals($investor->email, $email->sender_to);

        DB::table('investor_agreement')->where('investor_id', $investor->investor_id)->delete();
        DB::table('task')->where('investor_id', $investor->investor_id)->delete();
        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testNewPasswordSendEmail()
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');

        $faker = Faker::create();
        $this->investorService->update(
            $investor,
            [
                'new-password' => $faker->md5,
                'email' => $investor->email,
            ]
        );

        $email = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['password_changed']['id'],
            ]
        )->first();
        $this->assertNotEmpty($email);
        $this->assertEquals($investor->email, $email->sender_to);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testAddFundsSendEmail()
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');
        $bankAccount = $investor->mainBankAccount();
        $wallet = $this->prepareWallet($investor);
        $portfolios = $this->preparePortfolios($investor);

        $faker = Faker::create();

        $this->actingAs($investor, 'investor');
        $this->investorService->prepareDataAndAddFunds(
            $investor->investor_id,
            [
                'bank_account_id' => $bankAccount->bank_account_id,
                'bank_transaction_id' => $faker->randomNumber(5),
                'amount' => $faker->randomFloat(2, 10, 20),
            ]
        );
        $email = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['deposit_template']['id'],
            ]
        )->first();

        // Dont have user agreement. Must not have email
        $this->assertEmpty($email);

        // Lets create user agreement
        $userAgreement = new InvestorAgreement();
        $userAgreement->fill(
            [
                'investor_id' => $investor->investor_id,
                'agreement_id' => Agreement::RECEIVE_FUNDS_NOTIFICATION_ID,
                'value' => 1,
            ]
        );
        $userAgreement->save();

        $this->investorService->prepareDataAndAddFunds(
            $investor->investor_id,
            [
                'bank_account_id' => $bankAccount->bank_account_id,
                'bank_transaction_id' => $faker->randomNumber(5),
                'amount' => $faker->randomFloat(2, 10, 20),
            ]
        );

        $email = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['deposit_template']['id'],
            ]
        )->first();
        $this->assertNotEmpty($email);
        $this->assertEquals($investor->email, $email->sender_to);

        DB::table('investor_agreement')->where('investor_id', $investor->investor_id)->delete();
        DB::table('task')->where('investor_id', $investor->investor_id)->delete();
        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testWrongLoginAttemptsBlockSendEmail()
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');

        $faker = Faker::create();

        $ip = $faker->ipv4;

        for ($i = 0; $i < \SettingFacade::getSettingValue(Setting::MAX_WRONG_LOGIN_ATTEMPTS_KEY); $i++) {
            $loginAttempt = new LoginAttempt();
            $loginAttempt->fill(
                [
                    'datetime' => Carbon::now(),
                    'email' => $investor->email,
                    'ip' => $ip,
                    'device' => $faker->word,
                ]
            );
            $loginAttempt->save();
        }

        $bool = $this->loginAttemptService->isAttemptCountExceeded($ip, $investor, BlockedIp::BLOCKED_IP_REASON_LOGIN);

        $this->assertTrue($bool);

        $email = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['wrong_login_attempts']['id'],
            ]
        )->first();
        $this->assertNotEmpty($email);
        $this->assertEquals($investor->email, $email->sender_to);

        DB::table('login_attempt')->where('email', $investor->email)->delete();
        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testVerifyInvestorSendEmail()
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');
        $investor->status = Investor::INVESTOR_STATUS_REGISTERED;
        $investor->save();

        $task = new Task();
        $task->fill(
            [
                'investor_id' => $investor->investor_id,
                'task_type' => Task::TASK_TYPE_VERIFICATION,
                'status' => Task::TASK_STATUS_NEW,
            ]
        );
        $task->save();

        $this->actingAs(Administrator::where('administrator_id', Administrator::DEFAULT_UNIT_TEST_USER_ID)->first());
        $this->taskService->verify($task->task_id, ['action' => 'mark_verified']);

        $email = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['verification']['id'],
            ]
        )->first();
        $this->assertNotEmpty($email);
        $this->assertEquals($investor->email, $email->sender_to);

        DB::table('task')->where('investor_id', $investor->investor_id)->delete();
        DB::table('verification')->where('investor_id', $investor->investor_id)->delete();
        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testReferralLinkSendEmail()
    {
        $faker = Faker::create();

        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');
        $investor->referral_hash = $faker->sha256;
        $investor->save();

        $data = ['email' => $faker->email];
        $bool = $this->emailService->sendReferralLink(
            $investor,
            $data
        );
        $this->assertTrue($bool);

        $email = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['referral_link']['id'],
            ]
        )->first();
        $this->assertNotEmpty($email);
        $this->assertEquals($data['email'], $email->sender_to);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }

    public function testWelcomeSendEmail()
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');


        $bool = $this->emailService->sendWelcomeEmail(
            $investor,
        );
        $this->assertTrue($bool);

        $email = Email::where(
            [
                'investor_id' => $investor->investor_id,
                'email_template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['welcome_template']['id'],
            ]
        )->first();
        $this->assertNotEmpty($email);
        $this->assertEquals($investor->email, $email->sender_to);

        DB::table('email')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }
}
