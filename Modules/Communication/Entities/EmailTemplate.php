<?php

namespace Modules\Communication\Entities;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Common\Observers\EmailTemplateObserver;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class EmailTemplate extends BaseModel implements LoggerInterface
{
    public const TEMPLATE_SOCIAL_LINK = [
        'layout_contacts_link',
        'layout_contacts_link_title',
        'layout_about_us_link',
        'layout_about_us_link_title',
        'layout_facebook_link',
        'layout_facebook_link_title',
        'layout_twitter_link',
        'layout_twitter_link_title',
        'layout_home_link_title'
    ];
    public const TEMPLATE_MOST_USED_VARIABLES = [
        'logo',
        'firmName',
        'firmPhone',
        'firmWebSite',
        'Investor.first_name',
        'Investor.middle_name',
        'Investor.last_name',
        'loan_id',
        'signature_first',
        'signature_last',
        'timestamp'
    ];

    public const TEMPLATE_GENDER = 'common';
    public const TEMPLATE_TYPE = 'system';
    public const TEMPLATE_SEEDER_ARRAY = [
        'deposit_template' => [
            'id' => 1,
            'name' => 'Deposit',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
                'Investor.investor_id',
                'Transaction.amount',
                'Transaction.transaction_id'
            ]
        ],
        'login_template' => [
            'id' => 2,
            'name' => 'Login',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
                'location'
            ]
        ],
        'password_changed' => [
            'id' => 3,
            'name' => 'Password changed',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
                'Investor.investor_id',
            ]
        ],
        'welcome_template' => [
            'id' => 4,
            'name' => 'Welcome template',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
                'Investor.first_name',
                'siteImgUrl'
            ]
        ],
        'withdrawal_template' => [
            'id' => 5,
            'name' => 'Withdrawal',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
                'Transaction.amount',
                'Transaction.transaction_id',
            ]
        ],
        'forgot_password' => [
            'id' => 6,
            'name' => 'Forgot password',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
                'restorePasswordUrl'
            ]
        ],
        'continue_registration' => [
            'id' => 7,
            'name' => 'Continue registration',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
            ]
        ],
        'continue_verification' => [
            'id' => 8,
            'name' => 'Continue verification',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
            ]
        ],
        'referral_link' => [
            'id' => 9,
            'name' => 'Referral invitation',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
            ]
        ],
        'wrong_login_attempts' => [
            'id' => 10,
            'name' => 'Wrong login attempts',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
                'restorePasswordUrl'
            ]
        ],
        'verification' => [
            'id' => 11,
            'name' => 'Verification',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
            ]
        ],
        'referral_email' => [
            'id' => 12,
            'name' => 'Referral Email',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
            ]
        ],
        'email_changed' => [
            'id' => 13,
            'name' => 'Email changed',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
                'Investor.investor_id',
            ]
        ],
        'withdrawal_cancelled_insufficient_funds' => [
            'id' => 14,
            'name' => 'Withdrawal cancelled insufficient funds',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
                'Investor.investor_id',
                'Transaction.amount',
            ]
        ],
        'verification_rejected' => [
            'id' => 15,
            'name' => 'Verification rejected',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
            ]
        ],
        'withdrawal_processed' => [
            'id' => 16,
            'name' => 'Withdrawal processed',
            'variables' => [
                self::TEMPLATE_MOST_USED_VARIABLES,
                self::TEMPLATE_SOCIAL_LINK,
            ]
        ],
    ];

    /**
     * @var string
     */
    protected $table = 'email_template';

    /**
     * @var string
     */
    protected $primaryKey = 'email_template_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'key',
        'description',
        'variables',
        'title',
        'body',
        'text',
        'gender',
        'type'
    ];


     /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        self::observe(EmailTemplateObserver::class);
    }

    /**
     * @return HasMany
     */
    public function email(): HasMany
    {
        return $this->hasMany(
            Email::class,
            'email_id'
        );
    }

    /**
     * @return HasMany
     */
    public function emailCampaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class);
    }
}
