
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="charset=utf-8"/>
    <style>
        table:first-child tr td:nth-child(2), table:nth-child(2) tr td:nth-child(2) {
            text-align: center;
        }

        td {
            border: 1px solid #000;
            width: 50%;
            padding: 7px;
            font-size: 13px;
            vertical-align: top;
        }

        * {
            font-family: "DejaVu Sans", sans-serif;
        }

        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            width: 100%;
        }

        h1 {
            font-size: 16px;
        }

        p {
            font-size: 13px;
            margin-bottom: 0;
            padding-bottom: 0;
            padding-top: 20px;
            margin-top: 30px;
            line-height: 0.8;
        }

        h3 {
            text-align: center;
            font-style: italic;
            text-decoration: underline;
            line-height: 1;
            font-size: 15px;
            padding-top: 30px;
        }

        h4 {
            font-size: 15px;
        }

        h5 {
            font-style: italic;
        }

        span {
            font-size: 13px;
            line-height: 3;
        }

        .ordered-list {
            list-style-type: upper-roman;
            font-size: 12px;
        }

        .ordered-list li {
            margin-bottom: 5px;
        }

        .ordered-list ol {
            margin-top: 10px;
        }

        /* Utils */
        .mt-30 {
            margin-top: 30px;
        }

        .pb-50 {
            padding-bottom: 50px
        }

        .pb-70 {
            padding-bottom: 70px;
        }

        .pb-200 {
            padding-bottom: 200px;
        }

        .main-point {
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }

        ol {
            counter-reset: item;
            list-style-position: inside;
        }

        ol.roman-list-upper > li {
            list-style-type: upper-roman;
        }

        ol.roman-list-lower > li {
            list-style-type: lower-roman;
        }

        /* Numbered list (default, but with a pseudo-element) */
        ol.numbered-list {
            padding: 0 0 0 1.3rem;
            counter-reset: numberedList;
        }

        ol.numbered-list > li {
            list-style: none;
            text-indent: -1.3em;
        }

        ol.numbered-list > li:before {
            content: counter(numberedList) ". ";
            counter-increment: numberedList;
        }

        /* Numbered list with bracket: 1), 2), 3) */
        ol.numbered-list-bracket {
            counter-reset: list;
        }

        ol.numbered-list-bracket > li {
            text-indent: -1.3em;
            list-style: none;
        }

        ol.numbered-list-bracket > li:before {
            content: counter(list) ") ";
            counter-increment: list;
        }

        /*ol.numbered-list-bracket.start {
            counter-reset: list;
        }*/

        /* Numbered list with article's prefix */
        ol.article-list {
            list-style-type: none;
        }

        ol.article-list > li {
            list-style: none;
        }

        ol.article-list > li:before {
            content: "чл." counter(articleList) ". ";
            counter-increment: articleList;
        }

        ol.article-list.start {
            counter-reset: articleList;
        }

        /* Lists that start with a dash */
        ul.dashed-list > li {
            text-indent: -1.3em;
            list-style: none;
        }

        ul.dashed-list > li:before {
            content: '-';
            list-style-position: outside;
        }

        li ol {
            margin-top: 5px;
            margin-bottom: 5px;
        }

        li li {
            display: block;
            font-weight: normal;
            padding: 3px 0;
        }

        /*li LI:before {
            content: counters(item, ".") " ";
        }*/
        td {
            /*font-weight: bold;*/
            font-size: 12px;
        }

        tr td:first-child {
            text-align: left;
        }

        tr td:last-child {
            text-align: right;
        }

        table {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .list-style-none {
            list-style: none;
        }

        .p-0 {
            padding: 0;
        }

        .MailHtml p span {
            line-height: 1 !important;
        }

        .MailHtml p {
            margin-top: 3px !important;
            padding-top: 3px !important;
            line-height: 1.8 !important;
        }

    </style>
</head>

<body>
@php
    echo $content;
@endphp
</body>
</html>
