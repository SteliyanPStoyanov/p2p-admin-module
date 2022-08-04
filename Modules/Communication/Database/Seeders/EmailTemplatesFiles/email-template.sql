INSERT INTO public.email_template (email_template_id,"key",description,variables,title,body,"text",gender,"type",active,deleted,created_at,created_by,updated_at,updated_by,deleted_at,deleted_by,enabled_at,enabled_by,disabled_at,disabled_by) VALUES
	 (13,'your_afranga_email_changed','Email changed','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title","Investor.investor_id"]','Your Afranga email changed','Email changed','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Password changed</title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .wrapper h1 {
            }

            .wrapper h1 {
                font-size: 30px !important;
                line-height: 43px !important
            }

            .wrapper h2 {
            }

            .wrapper h2 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper h3 {
            }

            .wrapper h3 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }

            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->

    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="Password changed"/>
</head>
<body>
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                            <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="https://afranga.com/"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                                <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>{Investor.first_name}, your Afranga email has been changed</strong></h1>
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;<br />This is to confirm that the email address used for your Afranga account has been changed successfully.</p>
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">If you didn''t change your email and you believe that someone else may have accessed your account, please contact customer support for assistance.</p>
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">support@afranga.com</p>
</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-email-footer">
                                <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://afranga.com/"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top:2px;">9700 Shumen,</p>
<p class="company-info" style="margin-bottom: 0;margin-top:2px;">Bulgaria</p>
</div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd; text-decoration: underline;" href="#">Unsubscribe</a></div>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
<p>&nbsp;</p>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-16 10:34:02',2,NULL,NULL,NULL,NULL,NULL,NULL),
	 (9,'youre_invited_to_join_afranga','Referral invitation','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title"]','You''re invited to join Afranga','Referral invitation','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>You''re invited to afranga!</title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .wrapper h1 {
            }

            .wrapper h1 {
                font-size: 30px !important;
                line-height: 43px !important
            }

            .wrapper h2 {
            }

            .wrapper h2 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper h3 {
            }

            .wrapper h3 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }

            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->

    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="You''re invited to afranga!"/>
</head>
<body>
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="https://afranga.com/"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                        <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                            <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>{Investor.first_name} invited you to join Afranga</strong></h1>
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;<br />Great! You can now join Afranga and begin your investing journey. Click the button below or follow the link to register an account.<br /><br />{referral_link}</p>
</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div class="btn btn--flat btn--large" style="margin: 1rem 0; text-align: center;"><a style="-webkit-box-direction: normal; box-sizing: inherit; font-family: ''Arial'' !important; font-weight: 400 !important; border-radius: .38571429rem !important; letter-spacing: 0.5px; cursor: pointer; display: inline-block; min-height: 1em; outline: 0; border: none; vertical-align: baseline; padding: .78571429em 1.5em .78571429em; text-transform: none; line-height: 1em; font-style: normal; text-align: center; text-decoration: none; user-select: none; transition: opacity .1s ease,background-color .1s ease,color .1s ease,box-shadow .1s ease,background .1s ease,-webkit-box-shadow .1s ease; -webkit-tap-highlight-color: transparent; font-size: 1.1rem; color: #fff; text-shadow: none; box-shadow: 0 0 0 0 rgba(34,36,38,.15) inset; background-color: #009193;" href="{referral_link}">Register now</a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;</p>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-email-footer">
                                <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://afranga.com/"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top:2px;">9700 Shumen,</p>
<p class="company-info" style="margin-bottom: 0;margin-top:2px;">Bulgaria</p>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-15 19:11:10',2,NULL,NULL,NULL,NULL,NULL,NULL),
	 (11,'account_verified','Verification','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title"]','Account verified','Verification','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Successful Verification</title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }

            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->

    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="Successful Verification"/>
</head>
<body>
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                            <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="https://afranga.com/"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                                <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>{Investor.first_name}, your Afranga account is now verified</strong></h1>
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;<br />Begin your investing journey by depositing funds to your account. See how by visiting the Deposit/Withdraw page in your account.<br /><br /></p>
</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div class="btn btn--flat btn--large" style="margin-bottom: 20px; text-align: center;"><a style="-webkit-box-direction: normal; box-sizing: inherit; font-family: ''Arial'' !important; font-weight: 400 !important; border-radius: .38571429rem !important; letter-spacing: 0.5px; cursor: pointer; display: inline-block; min-height: 1em; outline: 0; border: none; vertical-align: baseline; margin: 0 .25em 0 0; padding: .78571429em 1.5em .78571429em; text-transform: none; line-height: 1em; font-style: normal; text-align: center; text-decoration: none; user-select: none; transition: opacity .1s ease,background-color .1s ease,color .1s ease,box-shadow .1s ease,background .1s ease,-webkit-box-shadow .1s ease; -webkit-tap-highlight-color: transparent; font-size: 1.1rem; color: #fff; text-shadow: none; box-shadow: 0 0 0 0 rgba(34,36,38,.15) inset; background-color: #009193;" href="{siteUrl}login">Log in to my account</a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #ff7e79;" href="https://afranga.com/refer-a-friend/"><img style="border: 0; margin-right: 3px;" src="{siteImgUrl}images/refer-a-friend-orange.png" alt="Facebook" width="20" height="20" /><span style="margin-top: 0; margin-bottom: 0; font-family: ''Arial''; text-align: justify; vertical-align: top; line-height: 1.5rem;"> Share with a friend and earn up to &euro;500.<br /></span></a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<p style="margin-top: 0; margin-bottom: 0; text-align: left; font-size: 12px;">&nbsp;</p>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-email-footer">
                                <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://afranga.com/"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top:2px;">9700 Shumen,</p>
<p class="company-info" style="margin-bottom: 0;margin-top:2px;">Bulgaria</p>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-16 10:32:52',2,NULL,NULL,NULL,NULL,NULL,NULL),
	 (3,'your_password_was_changed','Password changed','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title","Investor.investor_id"]','Your password was changed','Password changed','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Password changed</title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .wrapper h1 {
            }

            .wrapper h1 {
                font-size: 30px !important;
                line-height: 43px !important
            }

            .wrapper h2 {
            }

            .wrapper h2 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper h3 {
            }

            .wrapper h3 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }

            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->

    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="Password changed"/>
</head>
<body>
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                            <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="https://afranga.com/"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                                <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>Your Afranga password was changed</strong></h1>
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;<br />{Investor.first_name},<br /><br />This is a confirmation that the password for your Afranga account ID {Investor.investor_id} has been successfully changed.<br /><br />If you didn''t change your password, please contact our customer support for immediate assistance.<br /><br /><a style="display: block;" href="mailto:support@afranga.com"> support@afranga.com</a></p>
</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-email-footer">
                                <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://afranga.com/"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top:2px;">9700 Shumen,</p>
<p class="company-info" style="margin-bottom: 0;margin-top:2px;">Bulgaria</p>
</div>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-15 16:53:44',2,NULL,NULL,NULL,NULL,NULL,NULL),
	 (5,'withdrawal_request_received','Withdrawal','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title","Transaction.amount","Transaction.transaction_id"]','Withdrawal request received','Withdrawal','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Withdrawal request</title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .wrapper h1 {
            }

            .wrapper h1 {
                font-size: 30px !important;
                line-height: 43px !important
            }

            .wrapper h2 {
            }

            .wrapper h2 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper h3 {
            }

            .wrapper h3 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }


            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->

    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="Withdrawal request"/>
</head>
<body>
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                            <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="https://afranga.com/"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                                <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>We''ve received your withdrawal request</strong></h1>
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;<br />{Investor.first_name},<br /><br />We''ve received your withdrawal request for {Transaction.amount} EUR on {timestamp} and we''ll process it shortly.&nbsp;<br /><br /><span style="font-size: 14px; vertical-align: top;">●</span> Withdrawal amount: <strong>&euro; {Transaction.amount}</strong><br /><span style="font-size: 14px; vertical-align: top;">●</span> Date: <strong>{timestamp} </strong><br /><br /></p>
</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div class="btn btn--flat btn--large" style="margin-bottom: 20px; text-align: center;"><a style="-webkit-box-direction: normal; box-sizing: inherit; font-family: ''Arial'' !important; font-weight: 400 !important; border-radius: .38571429rem !important; letter-spacing: 0.5px; cursor: pointer; display: inline-block; min-height: 1em; outline: 0; border: none; vertical-align: baseline; margin: 0 .25em 0 0; padding: .78571429em 1.5em .78571429em; text-transform: none; line-height: 1em; font-style: normal; text-align: center; text-decoration: none; user-select: none; transition: opacity .1s ease,background-color .1s ease,color .1s ease,box-shadow .1s ease,background .1s ease,-webkit-box-shadow .1s ease; -webkit-tap-highlight-color: transparent; font-size: 1.1rem; color: #fff; text-shadow: none; box-shadow: 0 0 0 0 rgba(34,36,38,.15) inset; background-color: #009193;" href="{siteUrl}profile/invest">Manage my account</a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #ff7e79;" href="{siteUrl}refer-a-friend/"><img style="border: 0; margin-right: 3px;" src="{siteImgUrl}images/refer-a-friend-orange.png" alt="Facebook" width="20" height="20" /><span style="margin-top: 0; margin-bottom: 0; font-family: ''Arial''; text-align: justify; vertical-align: top; line-height: 1.5rem;"> Share with a friend and earn up to &euro;500.<br /></span></a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<p style="margin-top: 0; margin-bottom: 0; text-align: left; font-size: 12px;">&nbsp;</p>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-email-footer">
                                <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://afranga.com/"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top:2px;">9700 Shumen,</p>
<p class="company-info" style="margin-bottom: 0;margin-top:2px;">Bulgaria</p>
</div>
</div>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-15 16:53:29',2,NULL,NULL,NULL,NULL,NULL,NULL),
	 (12,'new_referral_registered','Referral Email','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title"]','New referral registered','Referral Email','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>New referral gained - Successful registration through your link</title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .wrapper h1 {
            }

            .wrapper h1 {
                font-size: 30px !important;
                line-height: 43px !important
            }

            .wrapper h2 {
            }

            .wrapper h2 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper h3 {
            }

            .wrapper h3 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }


            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->

    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="New referral gained - Successful registration through your link"/>
</head>
<body>
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                            <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="https://afranga.com/"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                                <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>{referralFirstName} successfully registered through your link</strong></h1>
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;<br />An affiliate bonus will automatically be added to your account. It is equal to 1% of your referral&rsquo;s invested funds in the first 30 days of registering their account. <br /><br />Keep in mind - to successfully receive the bonus your friend should deposit and invest at least 500 EUR. <br /><br /></p>
</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div class="btn btn--flat btn--large" style="margin-bottom: 20px; text-align: center;"><a style="-webkit-box-direction: normal; box-sizing: inherit; font-family: ''Arial'' !important; font-weight: 400 !important; border-radius: .38571429rem !important; letter-spacing: 0.5px; cursor: pointer; display: inline-block; min-height: 1em; outline: 0; border: none; vertical-align: baseline; margin: 0 .25em 0 0; padding: .78571429em 1.5em .78571429em; text-transform: none; line-height: 1em; font-style: normal; text-align: center; text-decoration: none; user-select: none; transition: opacity .1s ease,background-color .1s ease,color .1s ease,box-shadow .1s ease,background .1s ease,-webkit-box-shadow .1s ease; -webkit-tap-highlight-color: transparent; font-size: 1.1rem; color: #fff; text-shadow: none; box-shadow: 0 0 0 0 rgba(34,36,38,.15) inset; background-color: #009193;" href="{siteUrl}profile/my-profile-referral">Invite more people</a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                                <tr class="layout-email-footer">
                                    <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://afranga.com/"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top:2px;">9700 Shumen,</p>
<p class="company-info" style="margin-bottom: 0;margin-top:2px;">Bulgaria</p>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-16 10:33:48',2,NULL,NULL,NULL,NULL,NULL,NULL),
	 (6,'reset_your_password','Forgot password','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title","restorePasswordUrl"]','Reset your password','Forgot password','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Forgotten password</title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .wrapper h1 {
            }

            .wrapper h1 {
                font-size: 30px !important;
                line-height: 43px !important
            }

            .wrapper h2 {
            }

            .wrapper h2 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper h3 {
            }

            .wrapper h3 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }

            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->

    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="Forgotten password."/>
</head>
<body>
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="https://afranga.com/"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                        <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                            <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>Set your new password for your Afranga account</strong></h1>
<div style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;<br />{Investor.first_name},<br /><br />You can change your password by clicking the button below or by opening the link in your browser.</div>
<div style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">{restorePasswordUrl}</div>
<div style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">
<div style="margin-left: 20px; margin-right: 20px;">
<div class="btn btn--flat btn--large" style="margin: 1rem 0; text-align: center;"><a style="-webkit-box-direction: normal; box-sizing: inherit; font-family: ''Arial'' !important; font-weight: 400 !important; border-radius: .38571429rem !important; letter-spacing: 0.5px; cursor: pointer; display: inline-block; min-height: 1em; outline: 0; border: none; vertical-align: baseline; padding: .78571429em 1.5em .78571429em; text-transform: none; line-height: 1em; font-style: normal; text-align: center; text-decoration: none; user-select: none; transition: opacity .1s ease,background-color .1s ease,color .1s ease,box-shadow .1s ease,background .1s ease,-webkit-box-shadow .1s ease; -webkit-tap-highlight-color: transparent; font-size: 1.1rem; color: #fff; text-shadow: none; box-shadow: 0 0 0 0 rgba(34,36,38,.15) inset; background-color: #009193;" href="{restorePasswordUrl}">Set new password</a></div>
</div>
</div>
</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-email-footer">
                                <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://afranga.com/"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top:2px;">9700 Shumen,</p>
<p class="company-info" style="margin-bottom: 0;margin-top:2px;">Bulgaria</p>
</div>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-15 16:53:20',2,NULL,NULL,NULL,NULL,NULL,NULL),
	 (4,'welcome_to_afranga','Welcome template','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title","Investor.first_name","siteImgUrl"]','Welcome to Afranga','Welcome template','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Welcome to afranga</title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }

            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->
    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="Welcome to afranga"/>
</head>

<body>
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                            <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="https://afranga.com/"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                                <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>{Investor.first_name}, welcome to Afranga</strong></h1>
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;"><span class="font-Arial">&nbsp;<br />We are happy to have you on board! Follow the simple steps below and get your account ready for investing.<br /><br /></span></p>
<h3 style="margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify; line-height: 1;"><span class="font-Arial"> What are the next steps? <br /><br /></span></h3>
<div style="text-align: left;"><img style="border: 0; margin-right: 3px;" src="{siteImgUrl}images/step-1.png" alt="Facebook" width="48" height="48" />
<h4 style="margin-top: 0; margin-bottom: 0; vertical-align: top; line-height: 3rem; margin-left: .7rem; display: inline-block; font-weight: bold; font-size: 1.1rem;">Verify your identity</h4>
<span style="margin-top: -1rem; display: inline-block; text-align: justify; margin-bottom: 1rem; margin-left: 4rem; font-weight: 400; font-size: 1rem;">Verification is quick and easy. Log in to your account and follow the steps. We''ll only ask you for some basic personal information and it will take less than a minute.</span></div>
<div style="text-align: left;"><img style="border: 0; margin-right: 3px;" src="{siteImgUrl}images/step-2.png" alt="Facebook" width="48" height="48" />
<h4 style="margin-top: 0; margin-bottom: 0; vertical-align: top; line-height: 3rem; margin-left: .7rem; display: inline-block; font-weight: bold; font-size: 1.1rem;">Deposit money</h4>
<span style="margin-top: -1rem; display: inline-block; text-align: justify; margin-bottom: 1rem; margin-left: 4rem; font-weight: 400; font-size: 1rem;">You can deposit money via bank transfer. Check the details in the Deposit/Withdraw page in your account.</span></div>
<div style="text-align: left;"><img style="border: 0; margin-right: 3px;" src="{siteImgUrl}images/step-3.png" alt="Facebook" width="48" height="48" />
<h4 style="margin-top: 0; margin-bottom: 0; vertical-align: top; line-height: 3rem; margin-left: .7rem; display: inline-block; font-weight: bold; font-size: 1.1rem;">Invest in loans</h4>
<span style="margin-top: -1rem; display: inline-block; text-align: justify; margin-bottom: 1rem; margin-left: 4rem; font-weight: 400; font-size: 1rem;">Invest in the thousands of loans either manually or by creating automated strategies. Choose to reinvest your payments and enjoy the power of compound interest!&nbsp;</span></div>
</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div class="btn btn--flat btn--large" style="margin-bottom: 20px; text-align: center;"><a style="-webkit-box-direction: normal; box-sizing: inherit; font-family: ''Arial'' !important; font-weight: 400 !important; border-radius: .38571429rem !important; letter-spacing: 0.5px; cursor: pointer; display: inline-block; min-height: 1em; outline: 0; border: none; vertical-align: baseline; margin: 0 .25em 0 0; padding: .78571429em 1.5em .78571429em; text-transform: none; line-height: 1em; font-style: normal; text-align: center; text-decoration: none; user-select: none; transition: opacity .1s ease,background-color .1s ease,color .1s ease,box-shadow .1s ease,background .1s ease,-webkit-box-shadow .1s ease; -webkit-tap-highlight-color: transparent; font-size: 1.1rem; color: #fff; text-shadow: none; box-shadow: 0 0 0 0 rgba(34,36,38,.15) inset; background-color: #009193;" href="{siteUrl}login">Log in my account</a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #ff7e79;" href="https://afranga.com/refer-a-friend/"><img style="border: 0; margin-right: 3px;" src="{siteImgUrl}images/refer-a-friend-orange.png" alt="Facebook" width="20" height="20" /><span style="margin-top: 0; margin-bottom: 0; font-family: ''Arial''; text-align: justify; vertical-align: top; line-height: 1.5rem;"> Share with a friend and earn up to &euro;500.<br /></span></a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<p style="margin-top: 0; margin-bottom: 0; text-align: left; font-size: 12px;">&nbsp;</p>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-email-footer">
                                <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://afranga.com/"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top:2px;">9700 Shumen,</p>
<p class="company-info" style="margin-bottom: 0;margin-top:2px;">Bulgaria</p>
</div>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-15 16:53:36',2,NULL,NULL,NULL,NULL,NULL,NULL),
	 (1,'your_deposit_has_been_credited','Deposit','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title","Investor.investor_id","Transaction.amount","Transaction.transaction_id"]','Your deposit has been credited','Deposit','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Your deposit has been confirmed</title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .wrapper h1 {
            }

            .wrapper h1 {
                font-size: 30px !important;
                line-height: 43px !important
            }

            .wrapper h2 {
            }

            .wrapper h2 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper h3 {
            }

            .wrapper h3 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }


            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->
    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="Your deposit has been confirmed"/>
</head>
<body>
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="https://afranga.com/"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                        <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                            <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>Your deposit is in your Afranga account. Time to start investing!</strong></h1>
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;<br />{Investor.first_name},<br /><br />We are happy to confirm your deposit of&nbsp;<span style="font-size: 12pt; text-align: start; font-family: Arial, sans-serif;">{Transaction.amount}</span>&nbsp;EUR is available in your Afranga account ID&nbsp;<span style="font-size: 12pt; text-align: start; font-family: Arial, sans-serif;">{Investor.investor_id}</span>.<br /><br /><span style="font-size: 14px; vertical-align: top;">●</span> Amount deposited: <strong>&euro;&nbsp;</strong><strong style="caret-color: #000000; color: #000000; text-align: start;"><span style="font-size: 12pt; font-family: Arial, sans-serif; color: #787778;">{Transaction.amount}</span></strong><br /><span style="font-size: 14px; vertical-align: top;">●</span> Date: <strong>{timestamp} </strong><br /><br />If you have any queries relating to this transaction, contact our Customer Support team and quote reference number Transaction ID&nbsp;<span style="font-size: 12pt; text-align: start; font-family: Arial, sans-serif;">{Transaction.transaction_id}</span>. <br /><br /></p>
</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div class="btn btn--flat btn--large" style="margin-bottom: 20px; text-align: center;"><a style="-webkit-box-direction: normal; box-sizing: inherit; font-family: ''Arial'' !important; font-weight: 400 !important; border-radius: .38571429rem !important; letter-spacing: 0.5px; cursor: pointer; display: inline-block; min-height: 1em; outline: 0; border: none; vertical-align: baseline; margin: 0 .25em 0 0; padding: .78571429em 1.5em .78571429em; text-transform: none; line-height: 1em; font-style: normal; text-align: center; text-decoration: none; user-select: none; transition: opacity .1s ease,background-color .1s ease,color .1s ease,box-shadow .1s ease,background .1s ease,-webkit-box-shadow .1s ease; -webkit-tap-highlight-color: transparent; font-size: 1.1rem; color: #fff; text-shadow: none; box-shadow: 0 0 0 0 rgba(34,36,38,.15) inset; background-color: #009193;" href="{siteUrl}profile/invest">Start investing</a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #ff7e79;" href="https://afranga.com/refer-a-friend/"><img style="border: 0; margin-right: 3px;" src="{siteImgUrl}images/refer-a-friend-orange.png" alt="Facebook" width="20" height="20" /><span style="margin-top: 0; margin-bottom: 0; font-family: ''Arial''; text-align: justify; vertical-align: top; line-height: 1.5rem;"> Share with a friend and earn up to &euro;500.<br /></span></a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-email-footer">
                                <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://afranga.com/"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top: 2px;">9700 Shumen,</p>
<p class="company-info" style="margin-bottom: 0; margin-top: 2px;">Bulgaria</p>
</div>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-16 15:54:20',2,NULL,NULL,NULL,NULL,NULL,NULL),
	 (8,'verify_your_identity','Continue verification','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title"]','Verify your identity','Continue verification','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .wrapper h1 {
            }

            .wrapper h1 {
                font-size: 30px !important;
                line-height: 43px !important
            }

            .wrapper h2 {
            }

            .wrapper h2 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper h3 {
            }

            .wrapper h3 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }

            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->

    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="My First Campaign"/>
</head>
<body>
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="../../../"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                        <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                            <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>{Investor.first_name}, verify your identity to start investing</strong></h1>
<div style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;<br />We want to provide a safe environment for our investors and we need to make sure it''s really you creating an account. We will ask you for some basic personal information and it will take less than a minute. &nbsp;</div>
<div class="btn btn--flat btn--large" style="margin: 1rem 0; text-align: center;"><a style="-webkit-box-direction: normal; box-sizing: inherit; font-family: ''Arial'' !important; font-weight: 400 !important; border-radius: .38571429rem !important; letter-spacing: 0.5px; cursor: pointer; display: inline-block; min-height: 1em; outline: 0; border: none; vertical-align: baseline; padding: .78571429em 1.5em .78571429em; text-transform: none; line-height: 1em; font-style: normal; text-align: center; text-decoration: none; user-select: none; transition: opacity .1s ease,background-color .1s ease,color .1s ease,box-shadow .1s ease,background .1s ease,-webkit-box-shadow .1s ease; -webkit-tap-highlight-color: transparent; font-size: 1.1rem; color: #fff; text-shadow: none; box-shadow: 0 0 0 0 rgba(34,36,38,.15) inset; background-color: #009193;" href="http://193.8.4.24/profile/verify/investor">Login now</a></div>
<a style="display: block;" href="mailto:support@afranga.com"><br /></a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-email-footer">
                                <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="../../../"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top: 2px;">9700 Shumen,</p>
<p class="company-info" style="margin-bottom: 0; margin-top: 2px;">Bulgaria</p>
</div>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-23 19:31:32',4,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO public.email_template (email_template_id,"key",description,variables,title,body,"text",gender,"type",active,deleted,created_at,created_by,updated_at,updated_by,deleted_at,deleted_by,enabled_at,enabled_by,disabled_at,disabled_by) VALUES
	 (2,'new_login_location_detected','Login','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title","location"]','New login location detected','Login','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>New login location</title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .wrapper h1 {
            }

            .wrapper h1 {
                font-size: 30px !important;
                line-height: 43px !important
            }

            .wrapper h2 {
            }

            .wrapper h2 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper h3 {
            }

            .wrapper h3 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }

            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->

    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="New login location"/>
</head>
<body>
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="../../../"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                        <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                            <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>Someone accessed your Afranga account from a new location</strong></h1>
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;<br />{Investor.first_name},<br /><br />We have detected that someone accessed your Afranga account from a new location:<br /><br /><span style="font-size: 14px; vertical-align: top;">●</span> IP address: <strong>{location}</strong><br /><span style="font-size: 14px; vertical-align: top;">●</span> Date time: <strong>{timestamp}</strong><br /><br /><strong>If this was you:</strong><br />Great! You can safely ignore this email.<br /><br /><strong>If this was NOT you:</strong><br />If you are not the one who logged in, or this login is suspicious and you believe that someone else may have accessed your account, please:</p>
</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div class="btn btn--flat btn--large" style="margin: 1rem 0; text-align: center;"><a style="-webkit-box-direction: normal; box-sizing: inherit; font-family: ''Arial'' !important; font-weight: 400 !important; border-radius: .38571429rem !important; letter-spacing: 0.5px; cursor: pointer; display: inline-block; min-height: 1em; outline: 0; border: none; vertical-align: baseline; padding: .78571429em 1.5em .78571429em; text-transform: none; line-height: 1em; font-style: normal; text-align: center; text-decoration: none; user-select: none; transition: opacity .1s ease,background-color .1s ease,color .1s ease,box-shadow .1s ease,background .1s ease,-webkit-box-shadow .1s ease; -webkit-tap-highlight-color: transparent; font-size: 1.1rem; color: #fff; text-shadow: none; box-shadow: 0 0 0 0 rgba(34,36,38,.15) inset; background-color: #009193;" href="{siteUrl}forgot-password">Change your password</a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-email-footer">
                                <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="../../../"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top: 2px;">9700 Shumen,</p>
</div>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-27 10:27:26',4,NULL,NULL,NULL,NULL,NULL,NULL),
	 (7,'finish_your_registration','Continue registration','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title"]','Finish your registration','Continue registration','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .wrapper h1 {
            }

            .wrapper h1 {
                font-size: 30px !important;
                line-height: 43px !important
            }

            .wrapper h2 {
            }

            .wrapper h2 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper h3 {
            }

            .wrapper h3 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }

            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->
    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="My First Campaign"/>
</head>
<body >
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="https://afranga.com/"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                        <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                            <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>Finish your Afranga registration&nbsp;</strong></h1>
<div style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;</div>
<div style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">We noticed you didn''t finish your Afranga registration. Fill in your names now to setup an account and you''ll have access to the</div>
<div style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">We noticed that you didn''t finish your Afranga registration. Don''t miss out on another profitable week and set up your account now.</div>
<div class="btn btn--flat btn--large" style="margin: 1rem 0; text-align: center;"><a style="-webkit-box-direction: normal; box-sizing: inherit; font-family: ''Arial'' !important; font-weight: 400 !important; border-radius: .38571429rem !important; letter-spacing: 0.5px; cursor: pointer; display: inline-block; min-height: 1em; outline: 0; border: none; vertical-align: baseline; padding: .78571429em 1.5em .78571429em; text-transform: none; line-height: 1em; font-style: normal; text-align: center; text-decoration: none; user-select: none; transition: opacity .1s ease,background-color .1s ease,color .1s ease,box-shadow .1s ease,background .1s ease,-webkit-box-shadow .1s ease; -webkit-tap-highlight-color: transparent; font-size: 1.1rem; color: #fff; text-shadow: none; box-shadow: 0 0 0 0 rgba(34,36,38,.15) inset; background-color: #009193;" href="https://afranga.com/create-account/">Create account</a></div><a style="display: block;" href="mailto:support@afranga.com"><br /></a></div></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-email-footer">
                                <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://afranga.com/"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top:2px;">9700 Shumen,</p>
<p class="company-info" style="margin-bottom: 0;margin-top:2px;">Bulgaria</p>
</div>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-15 16:50:46',2,NULL,NULL,NULL,NULL,NULL,NULL),
	 (10,'too_many_login_attempts','Wrong login attempts','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title"]','Too many login attempts','Wrong login attempts','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Wrong login attempt</title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .wrapper h1 {
            }

            .wrapper h1 {
                font-size: 30px !important;
                line-height: 43px !important
            }

            .wrapper h2 {
            }

            .wrapper h2 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper h3 {
            }

            .wrapper h3 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }

            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->

    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="Wrong Login attempt"/>
</head>
<body>
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="https://afranga.com/"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                        <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                            <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong> Your account is blocked for 24h</strong></h1>
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;<br />We''ve noticed there are too many attempts to log in to your Afranga account with an incorrect password. As a security measure, we''ve blocked access to your account for 24h.&nbsp;</p>
</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<div class="btn btn--flat btn--large" style="margin: 1rem 0; text-align: center;"><a style="-webkit-box-direction: normal; box-sizing: inherit; font-family: ''Arial'' !important; font-weight: 400 !important; border-radius: .38571429rem !important; letter-spacing: 0.5px; cursor: pointer; display: inline-block; min-height: 1em; outline: 0; border: none; vertical-align: baseline; padding: .78571429em 1.5em .78571429em; text-transform: none; line-height: 1em; font-style: normal; text-align: center; text-decoration: none; user-select: none; transition: opacity .1s ease,background-color .1s ease,color .1s ease,box-shadow .1s ease,background .1s ease,-webkit-box-shadow .1s ease; -webkit-tap-highlight-color: transparent; font-size: 1.1rem; color: #fff; text-shadow: none; box-shadow: 0 0 0 0 rgba(34,36,38,.15) inset; background-color: #009193;" href="{restorePasswordUrl}">Restore password</a></div><a style="display: block;" href="mailto:support@afranga.com"><br /></a></div></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-email-footer">
                                <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://afranga.com/"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top:2px;">9700 Shumen,</p>
<p class="company-info" style="margin-bottom: 0;margin-top:2px;">Bulgaria</p>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-16 10:33:18',2,NULL,NULL,NULL,NULL,NULL,NULL),
     (14,'withdrawal_cancelled_insufficient_funds','Withdrawal cancelled insufficient funds','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title","Transaction.amount","Transaction.transaction_id"]','Withdrawal request received','Withdrawal','<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Withdrawal cancelled insufficient funds</title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .wrapper {
                min-width: 600px !important;
                width: 100%;
            }

            .wrapper h1 {
            }

            .wrapper h1 {
                font-size: 30px !important;
                line-height: 43px !important
            }

            .wrapper h2 {
            }

            .wrapper h2 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper h3 {
            }

            .wrapper h3 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .column {
            }

            .wrapper .size-8 {
                font-size: 8px !important;
                line-height: 14px !important
            }

            .wrapper .size-9 {
                font-size: 9px !important;
                line-height: 16px !important
            }

            .wrapper .size-10 {
                font-size: 10px !important;
                line-height: 18px !important
            }

            .wrapper .size-11 {
                font-size: 11px !important;
                line-height: 19px !important
            }

            .wrapper .size-12 {
                font-size: 12px !important;
                line-height: 19px !important
            }

            .wrapper .size-13 {
                font-size: 13px !important;
                line-height: 21px !important
            }

            .wrapper .size-14 {
                font-size: 14px !important;
                line-height: 21px !important
            }

            .wrapper .size-15 {
                font-size: 15px !important;
                line-height: 23px !important
            }

            .wrapper .size-16 {
                font-size: 16px !important;
                line-height: 24px !important
            }

            .wrapper .size-17 {
                font-size: 17px !important;
                line-height: 26px !important
            }

            .wrapper .size-18 {
                font-size: 18px !important;
                line-height: 26px !important
            }

            .wrapper .size-20 {
                font-size: 20px !important;
                line-height: 28px !important
            }

            .wrapper .size-22 {
                font-size: 22px !important;
                line-height: 31px !important
            }

            .wrapper .size-24 {
                font-size: 24px !important;
                line-height: 32px !important
            }

            .wrapper .size-26 {
                font-size: 26px !important;
                line-height: 34px !important
            }

            .wrapper .size-28 {
                font-size: 28px !important;
                line-height: 36px !important
            }

            .wrapper .size-30 {
                font-size: 30px !important;
                line-height: 38px !important
            }

            .wrapper .size-32 {
                font-size: 32px !important;
                line-height: 40px !important
            }

            .wrapper .size-34 {
                font-size: 34px !important;
                line-height: 43px !important
            }

            .wrapper .size-36 {
                font-size: 36px !important;
                line-height: 43px !important
            }

            .wrapper
            .size-40 {
                font-size: 40px !important;
                line-height: 47px !important
            }

            .wrapper .size-44 {
                font-size: 44px !important;
                line-height: 50px !important
            }

            .wrapper .size-48 {
                font-size: 48px !important;
                line-height: 54px !important
            }

            .wrapper .size-56 {
                font-size: 56px !important;
                line-height: 60px !important
            }

            .wrapper .size-64 {
                font-size: 64px !important;
                line-height: 63px !important
            }
        }
    </style>
    <meta name="x-apple-disable-message-reformatting"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        [x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .wrapper .footer__share-button a:hover,
        .wrapper .footer__share-button a:focus {
            color: #ffffff !important;
        }

        .btn a:hover,
        .btn a:focus,
        .footer__share-button a:hover,
        .footer__share-button a:focus,
        .email-footer__links a:hover,
        .email-footer__links a:focus {
            opacity: 0.8;
        }

        .preheader,
        .header,
        .layout,
        .column {
            transition: width 0.25s ease-in-out, max-width 0.25s ease-in-out;
        }

        .preheader td {
            padding-bottom: 8px;
        }

        .layout,
        div.header {
            max-width: 400px !important;
            -fallback-width: 95% !important;
            width: calc(100% - 20px) !important;
        }

        div.preheader {
            max-width: 360px !important;
            -fallback-width: 90% !important;
            width: calc(100% - 60px) !important;
        }

        .snippet,
        .webversion {
            Float: none !important;
        }

        .stack .column {
            max-width: 400px !important;
            width: 100% !important;
        }

        .fixed-width.has-border {
            max-width: 402px !important;
        }

        .fixed-width.has-border .layout__inner {
            box-sizing: border-box;
        }

        .snippet,
        .webversion {
            width: 50% !important;
        }

        .ie .btn {
            width: 100%;
        }

        .ie .stack .column,
        .ie .stack .gutter {
            display: table-cell;
            float: none !important;
        }

        .ie div.preheader,
        .ie .email-footer {
            max-width: 560px !important;
            width: 560px !important;
        }

        .ie .snippet,
        .ie .webversion {
            width: 280px !important;
        }

        .ie div.header,
        .ie .layout {
            max-width: 600px !important;
            width: 600px !important;
        }

        .ie .two-col .column {
            max-width: 300px !important;
            width: 300px !important;
        }

        .ie .three-col .column,
        .ie .narrow {
            max-width: 200px !important;
            width: 200px !important;
        }

        .ie .wide {
            width: 400px !important;
        }

        .ie .stack.fixed-width.has-border,
        .ie .stack.has-gutter.has-border {
            max-width: 602px !important;
            width: 602px !important;
        }

        .ie .stack.two-col.has-gutter .column {
            max-width: 290px !important;
            width: 290px !important;
        }

        .ie .stack.three-col.has-gutter .column,
        .ie .stack.has-gutter .narrow {
            max-width: 188px !important;
            width: 188px !important;
        }

        .ie .stack.has-gutter .wide {
            max-width: 394px !important;
            width: 394px !important;
        }

        .ie .stack.two-col.has-gutter.has-border .column {
            max-width: 292px !important;
            width: 292px !important;
        }

        .ie .stack.three-col.has-gutter.has-border .column,
        .ie .stack.has-gutter.has-border .narrow {
            max-width: 190px !important;
            width: 190px !important;
        }

        .ie .stack.has-gutter.has-border .wide {
            max-width: 396px !important;
            width: 396px !important;
        }

        .ie .fixed-width .layout__inner {
            border-left: 0 none white !important;
            border-right: 0 none white !important;
        }

        .ie .layout__edges {
            display: none;
        }

        .mso .layout__edges {
            font-size: 0;
        }

        .layout-fixed-width,
        .mso .layout-full-width {
            background-color: #ffffff;
        }

        @media only screen and (min-width: 620px) {
            .column,
            .gutter {
                display: table-cell;
                Float: none !important;
                vertical-align: top;
            }

            div.preheader,
            .email-footer {
                max-width: 560px !important;
                width: 560px !important;
            }

            div.header,
            .layout,
            .one-col .column {
                max-width: 600px !important;
                width: 600px !important;
            }

            .two-col .column {
                max-width: 300px !important;
                width: 300px !important;
            }

            .three-col .column,
            .column.narrow,
            .column.x_narrow {
                max-width: 200px !important;
                width: 200px !important;
            }

            .column.wide,
            .column.x_wide {
                width: 400px !important;
            }

            .two-col.has-gutter .column,
            .two-col.x_has-gutter .column {
                max-width: 290px !important;
                width: 290px !important;
            }

            .three-col.has-gutter .column,
            .three-col.x_has-gutter .column,
            .has-gutter .narrow {
                max-width: 188px !important;
                width: 188px !important;
            }

            .has-gutter .wide {
                max-width: 394px !important;
                width: 394px !important;
            }

            .two-col.has-gutter.has-border .column,
            .two-col.x_has-gutter.x_has-border .column {
                max-width: 292px !important;
                width: 292px !important;
            }

            .three-col.has-gutter.has-border .column,
            .three-col.x_has-gutter.x_has-border .column,
            .has-gutter.has-border .narrow,
            .has-gutter.x_has-border .narrow {
                max-width: 190px !important;
                width: 190px !important;
            }

            .has-gutter.has-border .wide,
            .has-gutter.x_has-border .wide {
                max-width: 396px !important;
                width: 396px !important;
            }
        }

        @supports (display: flex) {
            @media only screen and (min-width: 620px) {
                .fixed-width.has-border .layout__inner {
                    display: flex !important;
                }
            }
        }

        @media (max-width: 321px) {
            .fixed-width.has-border .layout__inner {
                border-width: 1px 0 !important;
            }

            .layout,
            .stack .column {
                min-width: 320px !important;
                width: 320px !important;
            }


            .has-gutter .border {
                display: table-cell;
            }
        }

        .mso div {
            border: 0 none white !important;
        }

        .mso .w560 .divider {
            Margin-left: 260px !important;
            Margin-right: 260px !important;
        }

        .mso .w360 .divider {
            Margin-left: 160px !important;
            Margin-right: 160px !important;
        }

        .mso .w260 .divider {
            Margin-left: 110px !important;
            Margin-right: 110px !important;
        }

        .mso .w160 .divider {
            Margin-left: 60px !important;
            Margin-right: 60px !important;
        }

        .mso .w354 .divider {
            Margin-left: 157px !important;
            Margin-right: 157px !important;
        }

        .mso .w250 .divider {
            Margin-left: 105px !important;
            Margin-right: 105px !important;
        }

        .mso .w148 .divider {
            Margin-left: 54px !important;
            Margin-right: 54px !important;
        }

        .mso .size-8,
        .ie .size-8 {
            font-size: 8px !important;
            line-height: 14px !important;
        }

        .mso .size-9,
        .ie .size-9 {
            font-size: 9px !important;
            line-height: 16px !important;
        }

        .mso .size-10,
        .ie .size-10 {
            font-size: 10px !important;
            line-height: 18px !important;
        }

        .mso .size-11,
        .ie .size-11 {
            font-size: 11px !important;
            line-height: 19px !important;
        }

        .mso .size-12,
        .ie .size-12 {
            font-size: 12px !important;
            line-height: 19px !important;
        }

        .mso .size-13,
        .ie .size-13 {
            font-size: 13px !important;
            line-height: 21px !important;
        }

        .mso .size-14,
        .ie .size-14 {
            font-size: 14px !important;
            line-height: 21px !important;
        }

        .mso .size-15,
        .ie .size-15 {
            font-size: 15px !important;
            line-height: 23px !important;
        }

        .mso .size-16,
        .ie .size-16 {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        .mso .size-17,
        .ie .size-17 {
            font-size: 17px !important;
            line-height: 26px !important;
        }

        .mso .size-18,
        .ie .size-18 {
            font-size: 18px !important;
            line-height: 26px !important;
        }

        .mso .size-20,
        .ie .size-20 {
            font-size: 20px !important;
            line-height: 28px !important;
        }

        .mso .size-22,
        .ie .size-22 {
            font-size: 22px !important;
            line-height: 31px !important;
        }

        .mso .size-24,
        .ie .size-24 {
            font-size: 24px !important;
            line-height: 32px !important;
        }

        .mso .size-26,
        .ie .size-26 {
            font-size: 26px !important;
            line-height: 34px !important;
        }

        .mso .size-28,
        .ie .size-28 {
            font-size: 28px !important;
            line-height: 36px !important;
        }

        .mso .size-30,
        .ie .size-30 {
            font-size: 30px !important;
            line-height: 38px !important;
        }

        .mso .size-32,
        .ie .size-32 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .mso .size-34,
        .ie .size-34 {
            font-size: 34px !important;
            line-height: 43px !important;
        }

        .mso .size-36,
        .ie .size-36 {
            font-size: 36px !important;
            line-height: 43px !important;
        }

        .mso .size-40,
        .ie .size-40 {
            font-size: 40px !important;
            line-height: 47px !important;
        }

        .mso .size-44,
        .ie .size-44 {
            font-size: 44px !important;
            line-height: 50px !important;
        }

        .mso .size-48,
        .ie .size-48 {
            font-size: 48px !important;
            line-height: 54px !important;
        }

        .mso .size-56,
        .ie .size-56 {
            font-size: 56px !important;
            line-height: 60px !important;
        }

        .mso .size-64,
        .ie .size-64 {
            font-size: 64px !important;
            line-height: 63px !important;
        }
    </style>

    <!--[if !mso]><!-->

    <style type="text/css">
        body {
            background-color: #fcfcfc
        }

        .logo a:hover, .logo a:focus {
            color: #859bb1 !important
        }

        .mso .layout-has-border {
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd
        }

        .mso .layout-has-bottom-border {
            border-bottom: 1px solid #bdbdbd
        }

        .mso .border, .ie .border {
            background-color: #bdbdbd
        }

        .mso h1, .ie h1 {
        }

        .mso h1, .ie h1 {
            font-size: 36px !important;
            line-height: 43px !important
        }

        .mso h2, .ie h2 {
        }

        .mso h2, .ie h2 {
            font-size: 22px !important;
            line-height: 31px !important
        }

        .mso h3, .ie h3 {
        }

        .mso h3, .ie h3 {
            font-size: 18px !important;
            line-height: 26px !important
        }

        .mso .layout__inner, .ie .layout__inner {
        }

        .mso .footer__share-button p {
        }

        .mso .footer__share-button p {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="Withdrawal request"/>
</head>
<body>
<table class="wrapper" style="border-collapse: collapse; table-layout: fixed; min-width: 320px; background-color: #fcfcfc;" role="presentation" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
<div role="banner">
<div id="emb-email-header-container" class="header" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px);"><!-- [if (mso)|(IE)]>
                    <table align="center" class="header" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                            <td style="width: 600px"><![endif]-->
<div class="logo emb-logo-margin-box" style="font-size: 26px; line-height: 32px; color: #c3ced9; font-family: Arial, Helvetica, sans-serif; border-bottom: 1px solid #dcdcdc; margin: 6px 20px 6px 20px;" align="center">
<div id="emb-email-header" class="logo-center" align="center"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #c3ced9;" href="https://afranga.com/"><img style="display: block; height: auto; width: 100%; border: 0; max-width: 225px;" src="{siteLogo}" alt="" width="225" /></a></div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div>
<div class="layout one-col fixed-width stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; padding: 1rem;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; background-color: #fcfcfc;"><!-- [if (mso)|(IE)]>
                        <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-fixed-width" style="background-color: #fcfcfc;">
                                <td style="width: 600px" class="w560"><![endif]-->
<div class="column" style="text-align: center; color: #787778; font-size: 16px; line-height: 24px; font-family: Arial, Helvetica, sans-serif;">
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>Withdrawal cancelled insufficient funds</strong></h1>
<p style="margin-top: 20px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; text-align: justify;">&nbsp;<br />{Investor.first_name},<br /><br />We''ve received your withdrawal request for {Transaction.amount} EUR on {timestamp} and we cancelled withdrawal insufficient funds.&nbsp;<br /><br /><span style="font-size: 14px; vertical-align: top;">●</span> Withdrawal amount: <strong>&euro; {Transaction.amount}</strong><br /><span style="font-size: 14px; vertical-align: top;">●</span> Date: <strong>{timestamp} </strong><br /><br /></p>
</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div class="btn btn--flat btn--large" style="margin-bottom: 20px; text-align: center;"><a style="-webkit-box-direction: normal; box-sizing: inherit; font-family: ''Arial'' !important; font-weight: 400 !important; border-radius: .38571429rem !important; letter-spacing: 0.5px; cursor: pointer; display: inline-block; min-height: 1em; outline: 0; border: none; vertical-align: baseline; margin: 0 .25em 0 0; padding: .78571429em 1.5em .78571429em; text-transform: none; line-height: 1em; font-style: normal; text-align: center; text-decoration: none; user-select: none; transition: opacity .1s ease,background-color .1s ease,color .1s ease,box-shadow .1s ease,background .1s ease,-webkit-box-shadow .1s ease; -webkit-tap-highlight-color: transparent; font-size: 1.1rem; color: #fff; text-shadow: none; box-shadow: 0 0 0 0 rgba(34,36,38,.15) inset; background-color: #009193;" href="{siteUrl}profile/invest">Manage my account</a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;"><a style="text-decoration: none; transition: opacity 0.1s ease-in; color: #ff7e79;" href="{siteUrl}refer-a-friend/"><img style="border: 0; margin-right: 3px;" src="{siteImgUrl}images/refer-a-friend-orange.png" alt="Facebook" width="20" height="20" /><span style="margin-top: 0; margin-bottom: 0; font-family: ''Arial''; text-align: justify; vertical-align: top; line-height: 1.5rem;"> Share with a friend and earn up to &euro;500.<br /></span></a></div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; line-height: 10px; font-size: 1px;">&nbsp;</div>
</div>
<div style="margin-left: 20px; margin-right: 20px;">
<div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle;">
<p style="margin-top: 0; margin-bottom: 0; text-align: left; font-size: 12px;">&nbsp;</p>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td></tr></table><![endif]--></div>
</div>
<div role="contentinfo">
<div class="layout email-footer stack" style="margin: 0 auto; max-width: 600px; min-width: 320px; width: calc(28000% - 167400px); overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
<div class="layout__inner" style="border-collapse: collapse; display: table; width: 100%; border-top: 1px solid #dcdcdc;"><!-- [if (mso)|(IE)]>
                            <table align="center" cellpadding="0" cellspacing="0" role="presentation">
                            <tr class="layout-email-footer">
                                <td style="width: 400px;" valign="top" class="w360"><![endif]-->
<div class="column wide" style="text-align: left; font-size: 12px; line-height: 19px; color: #bdbdbd; font-family: Arial, Helvetica, sans-serif; float: left; max-width: 400px; min-width: 320px; width: calc(8000% - 47600px);">
<div style="margin-left: 20px; margin-right: 20px; margin-top: 10px; padding-top: 10px;">
<div style="display: inline-block; float: right; width: 65%;">
<p style="margin-top: 0; margin-bottom: 0; text-align: justify; font-size: 12px;">Afranga is a brand name of "Stik Credit" JSC ("Stikcredit"). Stikcredit is a joined stock company registered in the Commercial register of the Republic of Bulgaria under company No. 202557159 with legal address at 13B Oborishte sq., 9700 Shumen, Bulgaria. Stikcredit is a regulated non-bank financial services company registered with and supervised by the Bulgarian National Bank under registration number BGR00370. Afranga is a marketplace for investing into loans originated by Stikcredit. Investing in loan receivables is subject to risks and we advise you to carefully evaluate all risks involved.</p>
</div>
<div style="display: inline-block; float: left; width: 30%;">
<div style="display: flex; justify-content: space-between; width: 70%;"><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_facebook_link}"><img style="border: 0;" src="{siteImgUrl}images/facebook.png" alt="Facebook" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="{layout_twitter_link}"><img style="border: 0;" src="{siteImgUrl}images/twitter.png" alt="Twitter" width="26" height="26" /></a><a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://www.linkedin.com/company/afranga/"><img style="border: 0;" src="{siteImgUrl}images/linkedin.png" alt="LinkedIn" width="26" height="26" /></a> <a style="text-decoration: underline; transition: opacity 0.1s ease-in; color: #bdbdbd;" href="https://afranga.com/"><img style="border: 0;" src="{siteImgUrl}images/website.png" alt="Website" width="26" height="26" /></a></div>
<div style="font-size: 12px; line-height: 19px; margin-top: 10px; text-align: left;"><a style="display: block; color: #bdbdbd;" href="mailto:support@afranga.com"> support@afranga.com</a>
<p class="company-info" style="margin-bottom: 0;">Afranga<br />Reg. No. 202557159<br />13B Oborishte sq.,</p>
<p class="company-info" style="margin-bottom: 0; margin-top:2px;">9700 Shumen,</p>
<p class="company-info" style="margin-bottom: 0;margin-top:2px;">Bulgaria</p>
</div>
</div>
</div>
</div>
</div>
<!-- [if (mso)|(IE)]></td>
                        </tr></table><![endif]--></div>
</div>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-15 16:53:29',2,NULL,NULL,NULL,NULL,NULL,NULL);
