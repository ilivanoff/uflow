<link rel="shortcut icon" href="/favicon.ico" />
{*<link rel="shortcut icon" href="/fav.png" type="image/png">*}

<link rel="stylesheet" href="/ps-content/css/core.css" type="text/css" media="all" />
<!--<link rel="stylesheet" href="/ps-content/css/common.css" type="text/css" media="all" />-->
<link rel="stylesheet" href="/ps-content/css/common.widgets.css" type="text/css" media="all" />

{*hint Css [https://github.com/chinchang/hint.css]*}

<link rel="stylesheet" href="/ps-content/css/hint/hint.min.css" />
<link rel="stylesheet" href="/ps-addon/css-lib/font-awesome-4.5.0/css/font-awesome.min.css" />

<script type="text/javascript" src="/ps-content/js-lib/jquery-1.8.2.js"></script>

<script type="text/javascript" src="/ps-content/js-lib/jquery.livequery.js"></script>

<script type="text/javascript" src="/ps-content/js-lib/jquery.scrollTo.1.4.7/jquery.scrollTo.min.js"></script>

<script type="text/javascript" src="/ps-content/js-lib/shortcut.js"></script>

<script type="text/javascript" src="/ps-content/js-lib/store/source/store.js"></script>

<script type="text/javascript" src="/ps-content/js-lib/md5.js"></script>

<script type="text/javascript" src="/ps-content/js-lib/date.format.js"></script>

<script type="text/javascript" src="/ps-content/js-lib/cropper/dist/cropper.js"></script>
<link rel="stylesheet" href="/ps-content/js-lib/cropper/dist/cropper.css" />

{*jQuery UI*}
{*
<link rel="stylesheet" href="/ps-content/js-lib/jquery-ui-1.9.1.custom/css/smoothness/jquery-ui-1.9.1.custom.min.css" type="text/css" media="all"/>
<script type="text/javascript" src="/ps-content/js-lib/jquery-ui-1.9.1.custom/js/jquery-ui-1.9.1.custom.min.js"></script>
*}
<link rel="stylesheet" href="/ps-addon/js-lib/jquery-ui-1.11.4.custom.Cupertino/jquery-ui.css" type="text/css" media="all"/>
<script type="text/javascript" src="/ps-addon/js-lib/jquery-ui-1.11.4.custom.Cupertino/jquery-ui.js"></script>


<link rel="stylesheet" href="/ps-content/js-lib/color-picker/colorPicker.css" type="text/css" media="all" />
<script type="text/javascript" src="/ps-content/js-lib/color-picker/jquery.colorPicker.min.js"></script>

{if isset($ATOOL_ENABLE) && $ATOOL_ENABLE}
    {*Скрипт для получения выделения на странице*}
    <script type="text/javascript" src="/ps-content/js-lib/jquery.a-tools-1.5.2.min.js"></script>
{/if}

{if !isset($MATHJAX_DISABLE) || !$MATHJAX_DISABLE}
    {linkup_js dir='/ps-content/js-lib' name="MathJax/MathJax.js"}
{/if}

{if $PAGE=='add'}
    {literal}
        <script>window.FileAPI = {
                debug: true,
                media: true,
                staticPath: '/ps-content/js-lib/FileAPI/FileAPI 2.0.18/dist/'};
            window.gRecaptchaLoaded = false;
            window.ongRecaptchaLoaded = function () {
                window.gRecaptchaLoaded = true;
            };
        </script>
    {/literal}
    <script src="/ps-content/js-lib/FileAPI/FileAPI 2.0.18/dist/FileAPI.js"></script>
    <script src="/ps-content/js-lib/FileAPI/FileAPI 2.0.18/plugins/caman.full.js"></script>

    <script src='https://www.google.com/recaptcha/api.js?onload=ongRecaptchaLoaded&render=explicit' async defer></script>
{/if}

{if $PAGE=='cell'}
    <script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script>
{/if}

{*Кнопки соц сетей Ya share*}
{if $smarty.const.CROP_YA_SHARE_ENABED}
    <script type="text/javascript" src="https://yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://yastatic.net/share2/share.js" charset="utf-8"></script>
{/if}

{if isset($GOOGLE_CHARTS) && $GOOGLE_CHARTS}
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
{/if}

{*
========================
Базовые ресурсы сайта
========================
*}

<script type="text/javascript" src="/ps-content/js/core.js"></script>
<script type="text/javascript" src="/ps-content/js/core.math.js"></script>
{$JS_DEFS}
<script type="text/javascript" src="/ps-content/js/common.js"></script>
<script type="text/javascript" src="/ps-content/js/common.math.js"></script>
<script type="text/javascript" src="/ps-content/js/common.ajax.js"></script>
{*<script type="text/javascript" src="/ps-content/js/common.forms.js"></script>*}
{*<script type="text/javascript" src="/ps-content/js/common.localbus.js"></script>*}
<script type="text/javascript" src="/ps-content/js/common.dialog.js"></script>
<script type="text/javascript" src="/ps-content/js/common.managers.js"></script>
{*<script type="text/javascript" src="/ps-content/js/common.bubbles.js"></script>*}
{*<script type="text/javascript" src="/ps-content/js/common.widgets.js"></script>*}

{devmodeOrAdmin}
<script type="text/javascript" src="/ps-content/js/common.dev.or.admin.js"></script>
<link rel="stylesheet" href="/ps-content/css/common.dev.or.admin.css" type="text/css" media="all" />
{/devmodeOrAdmin}
