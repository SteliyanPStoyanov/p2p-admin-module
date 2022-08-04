<?php

Route::group(
    ['middleware' => ['auth']],
    function () {
        Route::prefix('communication')->group(
            function () {
                $idPattern = '[1-9][0-9]{0,9}';

                Route::get('/email', 'EmailController@list')
                    ->name('admin.email.list')
                    ->defaults('description', 'Show list');

                Route::post('/email/sendEmail', 'EmailController@sendEmail')
                    ->name('communication.email.sendEmail')
                    ->defaults('description', 'Send email');

                // Email session
                Route::get('/email/refresh', 'EmailController@refresh')
                    ->name('admin.email.refresh')
                    ->defaults('description', 'Ajax refresh email table');

                // Email templates
                Route::get('/email-templates', 'EmailTemplateController@list')
                    ->name('admin.emailTemplate.list')
                    ->defaults('description', 'Show dashboard');

                Route::get('/email-template/create', 'EmailTemplateController@create')
                    ->name('communication.emailTemplate.create')
                    ->defaults('description', 'Create email template');

                Route::post('/email-template/store', 'EmailTemplateController@store')
                    ->name('communication.emailTemplate.store');

                Route::get('/email-template/edit/{id}', 'EmailTemplateController@edit')
                    ->name('communication.emailTemplate.edit')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Update email template');

                Route::post('/email-template/update/{id}', 'EmailTemplateController@update')
                    ->name('communication.emailTemplate.update')
                    ->where('id', $idPattern);

                Route::get('/email-template/delete/{id}', 'EmailTemplateController@delete')
                    ->name('communication.emailTemplate.delete')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Delete email template');

                Route::get('/email-template/enable/{id}', 'EmailTemplateController@enable')
                    ->name('communication.emailTemplate.enable')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Enable email template');

                Route::get('/email-template/disable/{id}', 'EmailTemplateController@disable')
                    ->name('communication.emailTemplate.disable')
                    ->where('id', $idPattern)
                    ->defaults('description', 'Disable email template');

                // Email template session
                Route::get('/email-template/refresh', 'EmailTemplateController@refresh')
                    ->name('admin.emailTemplate.refresh')
                    ->defaults('description', 'Ajax refresh email template table');
            }
        );
    }
);

