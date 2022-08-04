 INSERT INTO public.email_template (email_template_id,"key",description,variables,title,body,"text",gender,"type",active,deleted,created_at,created_by,updated_at,updated_by,deleted_at,deleted_by,enabled_at,enabled_by,disabled_at,disabled_by) VALUES
(15,'verification_rejected','Verification rejected','["logo","firmName","firmPhone","firmWebSite","Investor.first_name","Investor.middle_name","Investor.last_name","loan_id","signature_first","signature_last","timestamp","layout_contacts_link","layout_contacts_link_title","layout_about_us_link","layout_about_us_link_title","layout_facebook_link","layout_facebook_link_title","layout_twitter_link","layout_twitter_link_title","layout_home_link_title"]','Account verified rejected','Verification rejected','<!DOCTYPE html>
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
    <meta property="og:title" content="Verification rejected"/>
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
<h1 style="margin-top: 0; margin-bottom: 0; font-style: normal; font-weight: normal; color: #565656; font-size: 30px; line-height: 38px; text-align: center;"><strong>{Investor.first_name}, your Afranga account is rejected verified </strong></h1>
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
</html>','common','system',1,0,'2021-02-15 11:54:21',1,'2021-02-16 10:32:52',2,NULL,NULL,NULL,NULL,NULL,NULL);
