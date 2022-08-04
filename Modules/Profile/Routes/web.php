<?php

Route::group(
    ['middleware' => ['guest:investor']],
    function () {
        Route::get('/login', '\Modules\Profile\Http\Controllers\LoginController@loginFrom')
            ->name('profile');
        Route::post('/login', '\Modules\Profile\Http\Controllers\LoginController@login')
            ->name('profile.login');
        Route::get('/register', '\Modules\Profile\Http\Controllers\RegisterController@registerFrom')
            ->name('profile.registerFrom');
        Route::post('/register', '\Modules\Profile\Http\Controllers\RegisterController@register')
            ->name('profile.register');
        Route::get('/register/type/{type}', '\Modules\Profile\Http\Controllers\RegisterController@investorTypeHtml')
            ->name('profile.register.type');
        Route::get('/create-account', '\Modules\Profile\Http\Controllers\RegisterController@createAccountForm')
            ->name('profile.createAccount');
        Route::post('/create-account', '\Modules\Profile\Http\Controllers\RegisterController@createAccount')
            ->name('profile.createAccount.Submit');
        Route::get('/forgot-password', '\Modules\Profile\Http\Controllers\ForgotPasswordController@forgotPasswordForm')
            ->name('profile.forgotPassword');
        Route::post('/forgot-password', '\Modules\Profile\Http\Controllers\ForgotPasswordController@forgotPassword')
            ->name('profile.forgotPasswordSubmit');
        Route::get(
            '/restore-password/{hash}',
            '\Modules\Profile\Http\Controllers\ForgotPasswordController@restorePasswordForm'
        )
            ->name('profile.restorePassword');
        Route::post('/restore-password', '\Modules\Profile\Http\Controllers\ForgotPasswordController@restorePassword')
            ->name('profile.restorePasswordSubmit');


        Route::get('/referral/{hash}', '\Modules\Profile\Http\Controllers\ReferralController@referralLink')
            ->name('profile.hash');
    }
);


Route::group(
    ['middleware' => ['auth:investor']],
    function () {
        Route::prefix('profile')->group(
            function () {
                $idPattern = '[1-9][0-9]{0,5}';

                Route::post('/profile/logout', '\Modules\Profile\Http\Controllers\LoginController@logout')
                    ->name('profile.logout');

                Route::get('/overview', 'DashboardController@index')
                    ->name('profile.dashboard.overview');
                Route::get('/overview/earnedChart', 'DashboardController@earnedIncomeChart')
                    ->name('profile.dashboard.earnedIncomeChart');
                Route::get('/overview/outstandingChart', 'DashboardController@outstandingBalanceChart')
                    ->name('profile.dashboard.outstandingBalanceChart');
                Route::get('/overview/loanByAmount', 'DashboardController@loanByAmount')
                    ->name('profile.dashboard.loanByAmount');
                Route::get('/overview/loanByAmountTerm', 'DashboardController@loanByAmountTerm')
                    ->name('profile.dashboard.loanByAmountTerm');


                // profile
                Route::get('/my-profile', 'ProfileController@index')
                    ->name('profile.profile.index');
                Route::post('/my-profile', 'ProfileController@update')
                    ->name('profile.profile.update');
                Route::get('/my-profile-referral', 'ProfileController@referral')
                    ->name('profile.profile.referral');
                Route::post('/send-link', 'ProfileController@sendReferralLink')
                    ->name('profile.profile.sendReferralLink');
                Route::get('/account-statement', 'ProfileController@accountStatement')
                    ->name('profile.profile.accountStatement');
                Route::get('/account-statement/refresh', 'ProfileController@refresh')
                    ->name('profile.transaction.refresh');
                Route::get('/account-statement/export', 'ProfileController@export')
                    ->name('profile.accountStatement.export');
                Route::get('/my-profile/{contractId}', 'ProfileController@agreementDownload')
                    ->name('profile.profile.downloadAgreement');


                // verify
                Route::get('/verify/investor', 'VerifyController@verify')
                    ->name('profile.verify.verify');
                Route::post('/verify/investor', 'VerifyController@verifySubmit')
                    ->name('profile.verify.verifySubmit');
                Route::get('/verify/upload-personal-doc', 'VerifyController@uploadPersonalDoc')
                    ->name('profile.verify.uploadPersonalDoc');
                Route::post('/verify/upload-personal-doc', 'VerifyController@uploadPersonalDocSubmit')
                    ->name('profile.verify.uploadPersonalDocSubmit');
                Route::get('/verify/reviewing', 'VerifyController@reviewing')
                    ->name('profile.verify.reviewing');
                Route::get('/verify/company', 'VerifyController@company')
                    ->name('profile.verify.company');
                Route::post('/verify/reviewing', 'VerifyController@reviewingSubmit')
                    ->name('profile.verify.reviewingSubmit');
                Route::post('/verify/upload-company-doc', 'VerifyController@uploadCompanyDoc')
                    ->name('profile.verify.uploadCompanyDoc');

                //deposit
                Route::get('/deposit', 'DepositController@index')
                    ->name('profile.deposit');

                //withdraw
                Route::get('/withdraw', 'WithdrawController@index')
                    ->name('profile.withdraw');
                Route::post('/withdraw', 'WithdrawController@withdraw')
                    ->name('profile.withdraw.amount');

                //invest
                Route::get('/invest', 'InvestController@investView')
                    ->name('profile.invest');
                Route::get('/invest/unsuccessful', 'InvestController@investView')
                    ->name('profile.invest.view-unsuccessful');
                Route::get('/invest/list', 'InvestController@list')
                    ->name('profile.invest.list');
                Route::get('/invest/refresh', 'InvestController@refresh')
                    ->name('profile.invest.refresh');
                Route::get('/invest/{id}', 'InvestController@view')
                    ->where('id', '[0-9]+')
                    ->name('profile.invest.view');
                Route::post('/invest/{id}', 'InvestController@invest')
                    ->where('id', '[0-9]+')
                    ->name('profile.invest.invest');
                Route::post('/invest-all', 'InvestController@investAll')
                    ->name('profile.invest.investAll');
                Route::get('/invest-getBunchStatus', 'InvestController@getBunchStatus')
                    ->name('profile.invest.getBunchStatus');
                Route::get('/invest/assignment-agreement/{contractId}', 'InvestController@downloadAssignmentAgreement')
                    ->name('profile.invest.assignment-agreement.download');
                Route::get('/invest/assignment-agreement/loan/{loanId}', 'InvestController@assignmentAgreementTemplate')
                    ->name('profile.invest.assignment-agreement.template');

                Route::get('/invest/checkBunch/{bunchId}', 'InvestController@ajaxCheckBunch')
                    ->name('profile.ajax.check.investmentBunch');

                Route::get('/invest/check/ActiveBunch', 'InvestController@investorHasActiveBunch')
                    ->name('profile.ajax.check.investorHasActiveBunch');

                //My invest
                Route::get('/my-investments', 'MyInvestmentController@list')
                    ->name('profile.myInvest');
                Route::get('/my-investments/refresh', 'MyInvestmentController@refresh')
                    ->name('profile.myInvest.refresh');
                Route::get('/my-investments/export', 'MyInvestmentController@export')
                    ->name('profile.myInvest.export');

                //Auto invest
                Route::get('/auto-invest', 'AutoInvestController@index')
                    ->name('profile.autoInvest');
                Route::get('/auto-invest/refresh', 'AutoInvestController@refresh')
                    ->name('profile.autoInvest.refresh');

                // Auto invest Crud
                Route::get('/auto-invest/create', 'AutoInvestController@create')
                    ->name('profile.autoInvest.create');
                Route::post('/auto-invest/store', 'AutoInvestController@store')
                    ->name('profile.autoInvest.store');
                Route::get('/auto-invest/edit/{id}', 'AutoInvestController@edit')
                    ->name('profile.autoInvest.edit')
                    ->where('id', $idPattern);
                Route::post('/auto-invest/update/{id}', 'AutoInvestController@update')
                    ->name('profile.autoInvest.update')
                    ->where('id', $idPattern);
                Route::get('/auto-invest/delete/{id}', 'AutoInvestController@delete')
                    ->name('profile.autoInvest.delete')
                    ->where('id', $idPattern);
                Route::get('/auto-invest/enable/{id}', 'AutoInvestController@enable')
                    ->name('profile.autoInvest.enable')
                    ->where('id', $idPattern);
                Route::get('/auto-invest/disable/{id}', 'AutoInvestController@disable')
                    ->name('profile.autoInvest.disable')
                    ->where('id', $idPattern);
                Route::get('/auto-invest/priority-change', 'AutoInvestController@priorityChange')
                    ->name('profile.priority.change');
                Route::get('/auto-invest/loan-count', 'AutoInvestController@loanCount')
                    ->name('profile.autoInvest.loanCount');

                Route::get('/help', 'HelpController@index')
                    ->name('profile.help.index');
                Route::get('/user-agreement', 'UserAgreementController@template')
                    ->name('profile.user-agreement.index');


                // Secondary Market

                // Sell
                Route::post('/my-investments/sell', 'SecondaryMarketSellController@addToCartSingle')
                    ->name('profile.my-investments.sell');
                Route::post('/my-investments/sellMultiple', 'SecondaryMarketSellController@addToCartMultiple')
                    ->name('profile.my-investments.sellMultiple');

                // List
                Route::get(
                    '/cart-secondary/list',
                    'SecondaryMarketSellController@list'
                ) // need to separate cart for buying and selling ?
                ->name('profile.cart-secondary.list');

                Route::get(
                    '/cart-secondary/list-buy',
                    'SecondaryMarketSellController@listBuy'
                ) // need to separate cart for buying and selling ?
                ->name('profile.cart-secondary.list-buy');

                Route::get(
                    '/cart-secondary/cart',
                    'SecondaryMarketSellController@cart'
                ) // need to separate cart for buying and selling ?
                ->name('profile.cart-secondary.cart');

                Route::get(
                    '/cart-secondary/refresh',
                    'SecondaryMarketSellController@refresh'
                ) // need to separate cart for buying and selling ?
                ->name('profile.cart-secondary.refresh');

                Route::get('/cart-secondary/refresh-buy', 'SecondaryMarketSellController@refreshBuy')
                    ->name('profile.cart-secondary.refresh-buy');

                Route::get(
                    '/cart-secondary-seller/delete-loan/{id}',
                    'SecondaryMarketSellController@deleteLoan'
                )
                ->name('profile.cart-secondary.delete');

                Route::get(
                    '/cart-secondary-buyer/delete-loan/{id}',
                    'SecondaryMarketSellController@deleteLoanBuyer'
                )
                ->name('profile.cart-secondary.delete-buyers-loan');

                Route::get('/cart-secondary/deleteAll/{id}', 'SecondaryMarketSellController@deleteAll')
                    ->name('profile.cart-secondary.deleteAll');

                Route::post(
                    '/cart-secondary/sellAll/{cart_id}',
                    'SecondaryMarketSellController@sellAll'
                ) // need to separate cart for buying and selling ?
                ->name('profile.cart-secondary.submit');


                Route::post(
                    '/cart-secondary/buyAll/{cart_id}',
                    'SecondaryMarketSellController@buyAll'
                ) // need to separate cart for buying and selling ?
                ->name('profile.cart-secondary.buy');

                Route::get(
                    '/cart-secondary/buyAllSuccess',
                    'SecondaryMarketSellController@buyAllSuccess'
                )->name('profile.cart-secondary.buySuccess');

                 Route::get(
                    '/cart-secondary/buyAllSuccessRefresh',
                    'SecondaryMarketSellController@buyAllSuccessRefresh'
                )->name('profile.cart-secondary.buyAllSuccessRefresh');


                // Market
                Route::get('/market-secondary/list', 'SecondaryMarketController@list')
                    ->name('profile.market-secondary.list');

                Route::get('/market-secondary/refresh', 'SecondaryMarketController@refresh')
                    ->name('profile.market-secondary.refresh');

                Route::post('/market-secondary/invest-single', 'SecondaryMarketController@investSingle')
                    ->name('profile.market-secondary.invest-single');

                Route::post('/market-secondary/invest-all', 'SecondaryMarketController@addToCartMultiple')
                    ->name('profile.market-secondary.invest-all');

                Route::get('market-secondary/unsuccessful', 'SecondaryMarketController@listUnsuccessful')
                    ->name('profile.market-secondary.list-unsuccessful');


                Route::get(
                    '/market-secondary/delete/{id}',
                    'SecondaryMarketController@delete'
                )->name('profile.market-secondary.delete');

                // File pond
                Route::post('/file-pond/store', 'FilePondController@store')
                    ->name('file.pond.store');
                Route::delete('/file-pond/remove', 'FilePondController@remove')
                    ->name('file.pond.remove');
                Route::get('/file-pond/removeOldFile', 'FilePondController@removeOldFile')
                    ->name('file.pond.removeOldFile');
            }
        );
    }
);
