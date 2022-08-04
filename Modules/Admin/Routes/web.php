<?php

Route::group(
    ['middleware' => ['auth']],
    function () {
        Route::prefix('admin')->group(
            function () {
                $idPattern = '[1-9][0-9]{0,5}';

                Route::get('/dashboard', 'DashboardController@index')
                    ->name('admin.dashboard');

                 Route::get('/dashboard/registeredPerDay', 'DashboardController@registeredPerDay')
                    ->name('admin.dashboard.registeredPerDay');
                  Route::get('/dashboard/transactionPerDay', 'DashboardController@transactionPerDay')
                    ->name('admin.dashboard.transactionPerDay');

                Route::get('/file/{id}', 'FileController@getFileById')->name('file.get');

                Route::get('/administrators', 'AdminController@list')
                    ->name('admin.administrators.list')
                    ->defaults('description', 'View administrators');

                // ajax load of table with admins
                Route::get(
                    '/administrators/refresh',
                    'AdminController@refresh'
                )
                    ->name('admin.administrators.refresh')
                    ->defaults('description', 'Ajax refresh administrator table');

                // administrators
                Route::get('/administrators/create', 'AdminController@create')
                    ->name('admin.administrators.create')
                    ->defaults('description', 'Create administrator');
                Route::post('/administrators/store', 'AdminController@store')
                    ->name('admin.administrators.store');

                Route::get('/administrators/edit/{id}', 'AdminController@edit')
                    ->name('admin.administrators.edit')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Edit administrator');

                Route::post('/administrators/update/{id}', 'AdminController@update')
                    ->name('admin.administrators.update')
                    ->where('id', $idPattern);

                Route::get('/administrators/delete/{id}', 'AdminController@delete')
                    ->name('admin.administrators.delete')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Delete administrator');

                Route::get('/administrators/enable/{id}', 'AdminController@enable')
                    ->name('admin.administrators.enable')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Enable administrator');

                Route::get('/administrators/disable/{id}', 'AdminController@disable')
                    ->name('admin.administrators.disable')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Disable administrator');

                // ajax load of table with roles
                Route::get(
                    '/roles/refresh',
                    'RoleController@refresh'
                )
                    ->name('admin.roles.refresh')
                    ->defaults('description', 'Ajax refresh roles table');

                // roles
                Route::get('/roles', 'RoleController@list')
                    ->name('admin.roles.list')
                    ->defaults('description', 'View roles');
                Route::get('/roles/create', 'RoleController@create')
                    ->name('admin.roles.create')
                    ->defaults('description', 'Create role');
                Route::post('/roles/store', 'RoleController@store')
                    ->name('admin.roles.store');
                Route::get('/roles/edit/{id}', 'RoleController@edit')
                    ->name('admin.roles.edit')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Edit role');
                Route::post('/roles/update/{id}', 'RoleController@update')
                    ->name('admin.roles.update')
                    ->where('id', $idPattern);
                Route::get('/roles/delete/{id}', 'RoleController@delete')
                    ->name('admin.roles.delete')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Delete role');
                Route::get('/roles/enable/{id}', 'RoleController@enable')
                    ->name('admin.roles.enable')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Enable role');
                Route::get('/roles/disable/{id}', 'RoleController@disable')
                    ->name('admin.roles.disable')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Disable role');

                //Settings
                Route::get('/settings', 'SettingController@list')
                    ->name('admin.settings.list')
                    ->defaults('description', 'View settings');

                Route::get('/settings/create', 'SettingController@create')
                    ->name('admin.settings.create')
                    ->defaults('description', 'Create  setting');

                Route::post('/settings/store', 'SettingController@store')
                    ->name('admin.settings.store');

                Route::get('/settings/edit/{settingKey}', 'SettingController@edit')
                    ->name('admin.settings.edit')
                    ->defaults('description', 'Update setting');

                Route::post('/settings/update/{settingKey}', 'SettingController@update')
                    ->name('admin.settings.update');

                Route::get('/settings/delete/{settingKey}', 'SettingController@delete')
                    ->name('admin.settings.delete')
                    ->defaults('description', 'Delete setting');

                Route::get('/settings/enable/{settingKey}', 'SettingController@enable')
                    ->name('admin.settings.enable')
                    ->defaults('description', 'Enable setting');

                Route::get('/settings/disable/{settingKey}', 'SettingController@disable')
                    ->name('admin.settings.disable')
                    ->defaults('description', 'Disable setting');


                Route::get(
                    '/settings/refresh',
                    'SettingController@refresh'
                )->name('admin.settings.refresh')
                    ->defaults('description', 'Ajax refresh settings table');

                Route::get('/investors', 'InvestorController@list')
                    ->name('admin.investors.list')
                    ->defaults('description', 'View investors');

                Route::get('/investors-referrals', 'InvestorController@showReferrals')
                    ->name('admin.investors-referrals.list')
                    ->defaults('description', 'View all referrals');

                Route::get('/investors-referrals-refresh', 'InvestorController@showReferralsRefresh')
                    ->name('admin.investors-referrals.list-refresh')
                    ->defaults('description', 'Ajax refresh investor referrals');

                Route::post('/investors-referrals-refresh', 'InvestorController@referralBonus')
                    ->name('admin.investors-referrals.give-bonus')
                    ->defaults('description', 'Give a bonus for investor');

                Route::post('/investors-comment', 'InvestorController@comment')
                    ->name('admin.investors-comment')
                    ->defaults('description', 'Save comment for Investor');

                Route::get(
                    '/investors-wallet-transaction-refresh/{id}',
                    'InvestorController@refreshInvestorWalletTransactions'
                )
                    ->name('admin.investors-wallet-transaction-refresh')
                    ->defaults('description', 'Ajax refresh investor transactions');

                Route::get(
                    '/investors-investments-refresh/{id}',
                    'InvestorController@refreshInvestorInvestments'
                )
                    ->name('admin.investors-investments-refresh')
                    ->defaults('description', 'Ajax refresh investor investments');

                Route::get(
                    '/investors-changeLogs-refresh/{id}',
                    'InvestorController@refreshTableDataInvestorChangeLogs'
                )
                    ->name('admin.investors-change-logs-refresh')
                    ->defaults('description', 'Ajax refresh investor change logs');

                Route::get(
                    '/investors/refresh',
                    'InvestorController@refresh'
                )->name('admin.investors.refresh')
                    ->defaults('description', 'Ajax refresh investor table');

                Route::get('/investors/{id}', 'InvestorController@overview')
                    ->name('admin.investors.overview')
                    ->defaults('description', 'View investor');

                Route::get('/investors/contract/{contractId}', 'InvestorController@agreementDownload')
                    ->name('admin.investor.downloadAgreement');

                Route::post('/investors/{id}', 'InvestorController@addFunds')
                    ->name('admin.investors.add-funds')
                    ->defaults('description', 'Add funds on investor');

                Route::post('/investors-document/{id}', 'InvestorController@addDocument')
                    ->name('admin.investors.add-document')
                    ->defaults('description', 'Add document on investor');

                Route::get(
                    '/investors/investment-export/{id}',
                    'InvestorController@investorInvestmentExport'
                )->name('admin.investor-investment.export')
                    ->defaults('description', 'Investor investment export');

                 Route::get(
                    '/investor/show-referral',
                    'InvestorController@showReferral'
                )->name('admin.investor.show-referral');

                Route::get('/tasks', 'TaskController@list')
                    ->name('admin.tasks.list')
                    ->defaults('description', 'View tasks');
                // ajax load of table with offices
                Route::get(
                    '/tasks/refresh',
                    'TaskController@refresh'
                )->name('admin.tasks.refresh')
                    ->defaults('description', 'Ajax refresh task table');

                Route::get(
                    '/tasks/{id}/exit',
                    'TaskController@exitTask'
                )->name('admin.tasks.exit-task');

                Route::get(
                    '/tasks/cancel/{id}',
                    'TaskController@cancelTask'
                )->name('admin.tasks.cancel-task');


                Route::get(
                    '/tasks/process',
                    'TaskController@updateProcessBy'
                )->name('admin.tasks.update-process-by');

                Route::get(
                    '/tasks/delete/{id}',
                    'TaskController@delete'
                )->name('admin.tasks.delete');

                Route::post(
                    '/tasks/{id}/withdraw',
                    'TaskController@withdraw'
                )->name('admin.tasks.withdraw');

                Route::post(
                    '/tasks/{id}/match-deposit',
                    'TaskController@matchDeposit'
                )->name('admin.tasks.match-deposit');

                Route::post(
                    '/tasks/{id}/first-deposit',
                    'TaskController@firstDeposit'
                )->name('admin.tasks.first-deposit');

                Route::post(
                    '/tasks/{id}/rejected-verification',
                    'TaskController@rejectedVerification'
                )->name('admin.tasks.rejected-verification');
                Route::post(
                    '/tasks/{id}/not-verified',
                    'TaskController@notVerified'
                )->name('admin.tasks.not-verified');

                Route::post(
                    '/tasks/{id}/addBonus',
                    'TaskController@addBonus'
                )->name('admin.task.addBonus');


                Route::post(
                    '/tasks/{id}/verify',
                    'TaskController@verify'
                )->name('admin.tasks.verify');


                Route::get('/loans', 'NewLoansController@list')
                    ->name('admin.loans.list')
                    ->defaults('description', 'View loans');

                Route::get(
                    '/loans/refresh',
                    'NewLoansController@refresh'
                )->name('admin.loans.refresh')
                    ->defaults('description', 'Ajax refresh loan table');

                Route::get(
                    '/loans/{loanId}',
                    'NewLoansController@overview'
                )->name('admin.loans.overview')
                    ->defaults('description', 'View loan');

                Route::get('/transactions', 'TransactionController@list')
                    ->name('admin.transactions.list')
                    ->defaults('description', 'View transactions');

                Route::get(
                    '/transactions/refresh',
                    'TransactionController@refresh'
                )->name('admin.transactions.refresh')
                    ->defaults('description', 'Ajax refresh transaction table');

                Route::get('/wallets', 'WalletController@list')
                    ->name('admin.wallets.list')
                    ->defaults('description', 'View wallets');
                Route::get(
                    '/wallets/refresh',
                    'WalletController@refresh'
                )->name('admin.wallets.refresh');


                Route::post('/crons/execute', 'CronController@execute')
                    ->name('admin.crons.execute')
                    ->defaults('description', 'Execute cron');

                Route::get('/crons', 'CronController@list')
                    ->name('admin.crons.list')
                    ->defaults('description', 'View crons');

                // Route for log
                Route::get('/cron-logs', 'LogController@list')
                    ->name('admin.cron-logs.list')
                    ->defaults('description', 'View import log history');

                Route::get('/history-logs/refresh', 'LogController@refresh')
                    ->name('admin.cron-logs.refresh')
                    ->defaults('description', 'Ajax refresh history cron logs table');

                //Routes for /admin/new-loans-upload page
                Route::post('/new-loans-upload/execute/{fileId}', 'NewLoansController@execute')
                    ->name('admin.new-loans.execute')
                    ->defaults('description', 'Execute new loans import');

                Route::get('/new-loans-upload', 'NewLoansController@uploadNewLoans')
                    ->name('admin.loans.upload.list')
                    ->defaults('description', 'Upload new loan files');

                Route::post('/new-loan-upload', 'NewLoansController@storeFile')
                    ->name('admin.upload-loan-document')
                    ->defaults('description', 'Upload document for loan');

                Route::get('/delete-loan-upload/{id}', 'NewLoansController@deleteDocumentWithNewLoans')
                    ->name('admin.delete-loan-document')
                    ->defaults('description', 'Delete loan document');
                // End routes for /admin/new-loans-upload page

                Route::get('/download-loan-document/{id}', 'NewLoansController@downloadDocumentWithNewLoans')
                    ->name('admin.download-loan-document')
                    ->defaults('description', 'Download loan document');


                Route::get('/transactions', 'TransactionController@list')
                    ->name('admin.transactions.list')
                    ->defaults('description', 'View transactions');
                Route::get(
                    '/transactions/refresh',
                    'TransactionController@refresh'
                )->name('admin.transactions.refresh')
                    ->defaults('description', 'Ajax refresh transaction table');

                Route::post('/transactions', 'TransactionController@storeFile')
                    ->name('admin.transactions.upload-payments')
                    ->defaults('description', 'Upload payments');

                Route::get('/re-buying-loans', 'ReBuyingLoanController@list')
                    ->name('admin.re-buying-loans.list')
                    ->defaults('description', 'View re-buying loans');
                Route::get(
                    '/re-buying-loans/refresh',
                    'ReBuyingLoanController@refresh'
                )->name('admin.re-buying-loans.refresh')
                    ->defaults('description', 'Ajax refresh re-buying loan table');

                //Routes for /admin/re-buying-loans-upload page
                Route::get('/re-buying-loans-upload', 'ReBuyingLoanController@getFiles')
                    ->name('admin.re-buying-loans.upload.list')
                    ->defaults('description', 'View re-buying-loan files');

                Route::post('/re-buying-loans-upload', 'ReBuyingLoanController@storeFile')
                    ->name('admin.re-buying-loans.store')
                    ->defaults('description', 'Upload document for re-buying-loan');

                Route::get('/delete-re-buying-loans-delete/{id}', 'ReBuyingLoanController@deleteLoanDocument')
                    ->name('admin.re-buying-loans.delete')
                    ->defaults('description', 'Delete re-buying-loan document');

                Route::get('/download-re-buying-loans-download/{id}', 'ReBuyingLoanController@downloadLoanDocument')
                    ->name('admin.re-buying-loans.download')
                    ->defaults('description', 'Download re-buying-loan document');
                // End routes for /admin/re-buying-loans-upload page

                // User agreement template
                Route::get('/user-agreements', 'UserAgreementController@list')
                    ->name('admin.user-agreement.list')
                    ->defaults('description', 'Show user agreement templates');

                Route::get('/user-agreements/create', 'UserAgreementController@create')
                    ->name('admin.user-agreement.create')
                    ->defaults('description', 'Create user agreement template');

                Route::post('/user-agreements/store', 'UserAgreementController@store')
                    ->name('admin.user-agreement.store');

                Route::get('/user-agreements/edit/{id}', 'UserAgreementController@edit')
                    ->name('admin.user-agreement.edit')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Update user agreement template');

                Route::post('/user-agreements/update/{id}', 'UserAgreementController@update')
                    ->name('admin.user-agreement.update')
                    ->where('id', $idPattern);

                Route::get('/user-agreements/delete/{id}', 'UserAgreementController@delete')
                    ->name('admin.user-agreement.delete')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Delete user agreement template');

                Route::get('/user-agreements/enable/{id}', 'UserAgreementController@enable')
                    ->name('admin.user-agreement.enable')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Enable user agreement template');

                Route::get('/user-agreements/refresh', 'UserAgreementController@refresh')
                    ->name('admin.user-agreement.refresh')
                    ->defaults('description', 'Ajax refresh user agreement template table');

                //Blocked Ip
                Route::get('/blocked/list', 'BlockedController@list')
                    ->name('admin.blocked-ip.list')
                    ->defaults('description', 'Browse blocked ips');

                Route::get('/blocked/remove/{id}', 'BlockedController@remove')
                    ->name('admin.blocked-ip.remove')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Remove blocked ip');

                Route::get('/blocked-refresh', 'BlockedController@refresh')
                    ->name('admin.blocked-ip.refresh')
                    ->defaults('description', 'Ajax refresh blocked ip table');

                Route::get('/blocked-ip-remove-all', 'BlockedController@deleteAll')
                    ->name('admin.blocked-ip.delete-all')
                    ->defaults('description', 'Delete all blocked ips');
                //BLocked Ip

                //Login Attempts
                Route::get('/login-attempts', 'LoginAttempController@list')
                    ->name('admin.login-attempt.list')
                    ->defaults('description', 'View login attempts');

                Route::get('/login-attempt-remove/{id}', 'LoginAttempController@remove')
                    ->name('admin.login-attempt.remove')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Remove login attempt');

                Route::get('/login-attempt-refresh', 'LoginAttempController@refresh')
                    ->name('admin.login-attempt.refresh')
                    ->defaults('description', 'Ajax refresh blocked login attempt table');

                Route::get('/login-attempts-delete-all', 'LoginAttempController@deleteAll')
                    ->name('admin.login-attempt.delete-all')
                    ->defaults('description', 'Delete all login attempts');
                //Login Attempts


                //Registration Attempts
                Route::get('/registration-attempts', 'RegistrationAttemptController@list')
                    ->name('admin.registration-attempt.list')
                    ->defaults('description', 'View registration attempts');

                Route::get('/registration-attempts-remove/{id}', 'RegistrationAttemptController@remove')
                    ->name('admin.registration-attempt.remove')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Remove registration attempt');

                Route::get('/registration-attempt-refresh', 'RegistrationAttemptController@refresh')
                    ->name('admin.registration-attempt.refresh')
                    ->defaults('description', 'Ajax refresh registration attempt table');

                Route::get('/registration-attempts-delete-all', 'RegistrationAttemptController@deleteAll')
                    ->name('admin.registration-attempt.delete-all')
                    ->defaults('description', 'Delete all registration attempts');
                //Registration Attempts


                //Investor Login Log
                Route::get('/investor-login-logs', 'InvestorLoginLogController@list')
                    ->name('admin.investor-login-log.list')
                    ->defaults('description', 'View registration attempts');

                Route::get('/investor-login-log-remove/{id}', 'InvestorLoginLogController@remove')
                    ->name('admin.investor-login-log.remove')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Remove investor login log');

                Route::get('/investor-login-log-refresh', 'InvestorLoginLogController@refresh')
                    ->name('admin.investor-login-log.refresh')
                    ->defaults('description', 'Ajax refresh investor login logs');
                //Investor Login Log


                Route::get('/invest-strategy', 'InvestStrategyController@list')
                    ->name('admin.invest-strategy.list')
                    ->defaults('description', 'Invest strategy list');

                Route::get('/invest-strategy/{id}', 'InvestStrategyController@overview')
                    ->name('admin.invest-strategy.overview')
                    ->where('id', $idPattern)
                    ->defaults('description', 'View invest strategy');

                Route::get(
                    '/invest-strategy/refresh',
                    'InvestStrategyController@refresh'
                )->name('admin.invest-strategy.refresh')
                    ->defaults('description', 'Ajax refresh invest strategy table');

                Route::get(
                    '/invest-strategy/refresh-history/{id}',
                    'InvestStrategyController@refreshHistory'
                )->name('admin.invest-strategy.refreshHistory')
                    ->defaults('description', 'Ajax refresh invest strategy history table');

                Route::get(
                    '/invest-strategy/refresh-loan/{id}',
                    'InvestStrategyController@refreshLoan'
                )->name('admin.invest-strategy.refreshLoan')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Ajax refresh loan table');

                Route::get('/invest-strategy/export', 'InvestStrategyController@export')
                    ->name('admin.invest-strategy.export')
                    ->defaults('description', 'Export invest strategy');

                Route::get('/blog-page', 'BlogPageController@list')
                    ->name('admin.blog-page.list')
                    ->defaults('description', 'Blog page list');

                Route::get(
                    '/blog-page/refresh',
                    'BlogPageController@refresh'
                )->name('admin.blog-page.refresh')
                    ->defaults('description', 'Ajax refresh blog pages table');

                Route::get('/blog-page/edit/{id}', 'BlogPageController@edit')
                    ->name('admin.blog-page.edit')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Edit blog-page');

                Route::post('/blog-page/update/{id}', 'BlogPageController@update')
                    ->name('admin.blog-page.update')
                    ->where('id', $idPattern);

                Route::get('/blog-page/delete/{id}', 'BlogPageController@delete')
                    ->name('admin.blog-page.delete')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Delete blog-post');

                Route::get('/blog-page/delete-image/{imageId}', 'BlogPageController@ajaxDeleteImage')
                    ->name('admin.blog-page-delete-image')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Destroy image/s from blog page');

                Route::get('/blog-page/enable/{id}', 'BlogPageController@enable')
                    ->name('admin.blog-page.enable')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Enable blog-post');

                Route::get('/blog-page/disable/{id}', 'BlogPageController@disable')
                    ->name('admin.blog-page.disable')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Disable blog-post');

                Route::get('/blog-page/create', 'BlogPageController@create')
                    ->name('admin.blog-page.create')
                    ->defaults('description', 'Create blog-pages');

                Route::post('/blog-page/store', 'BlogPageController@store')
                    ->name('admin.blog-page.store');

                Route::get('/investor/export/{id}', 'InvestorController@exportWallet')
                    ->name('admin.investor.export')
                    ->defaults('description', 'Export wallet');


                Route::post('/mongo-logs/{adapterKey}/{id}', 'MongoLogController@delete')
                    ->name('admin.mongo-logs.delete')
                    ->defaults('description', 'Delete mongo log');

                Route::get('/mongo-logs/{adapterKey}', 'MongoLogController@list')
                    ->name('admin.mongo-logs.list')
                    ->defaults('description', 'View mongo logs');

                // ajax load of table with admins
                Route::get(
                    '/mongo-logs/refresh/{adapterKey}',
                    'MongoLogController@refresh'
                )
                    ->name('admin.mongo-logs.refresh')
                    ->defaults('description', 'Ajax refresh mongo log table');
            }
        );
    }
);
