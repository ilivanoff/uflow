<div class="about">
    <h1 class="info-text">Поделитесь своими эмоциями со всем миром!</h1>

    <div class="figure fig1 left">
        <img src="/i/wall-example.png" class="f"/>
        <div class="info">
            <h2>Как это работает</h2>
            <p>
                Как часто нас обуревают эмоции, которыми хочется с кем-то поделиться...
                Иногда эмоции так ярки, что хочется просто кричать об этом на весь Мир!
                Иногда же настроение такое, что не хочется никого видеть и ни с кем общаться, но при этом 
                поделиться своей печалью с кем-то.
            </p>

            <p>
                Допустим в Вашей жизни произошло что-то настолько яркое, что хочется зафиксировать
                этот момет не только в памяти, но и оставить по этому поводу надпись на небе:)
                Отлично! Заходите на наш сайт, нажимаете кнопку добавить и делитесь своими эмоциями 
                со всем Миром!:)
            </p>
            <p class="ps">
                Оставить запись на стене можно <span class="green">совершенно бесплатно</span>.<br/>
                Записи останутся здесь <span class="red">навсегда</span>!
            </p>
        </div>
    </div>

    <div class="figure fig2">
        <div class="info">
            <h2>Как добавить запись</h2>
            <p>
                Добавить новую запись можно в <a href="/add.php">соответствующем разделе</a>.
            </p>

            <p>
                Просьба соблюдать цензуру при написании сообщений (даже если эмоции Вас просто переполняют:)
                и загружать приличные изображения.
            </p>

            <p>
                <i>Рекламные сообщения не допускаются.</i> Для размещения рекламы на сайте свяжитесь, пожалуйста, с администрацией.
            </p>

            <p>
                Давайте уважать друг друга. Записи, не соответствующие правилам, будут удаляться.
            </p>

            <p class="ps">
                Хулиганы будут добавлены в <span class="red">бан</span>.
            </p>

        </div>
        <img src="/i/addcell.png" class="f2"/>
        <div class="clearall"></div>
    </div>

    <div class="figure fig3 left">
        <img src="/i/feedback.png" class="f"/>
        <div class="info">
            <h2>Обратная связь</h2>
            <p>
                По вопросам изменения Вашей ячейки просьба писать на адрес технической поддержки
                <a href="mailto:{$smarty.const.CROP_SUPPORT_MAIL}"><span class="fa fa-envelope"></span> {$smarty.const.CROP_SUPPORT_MAIL}</a>
            </p>

            <p>
                В письме просьба указать оставленный Вами email и номер ячейки.
                Просьба указывать тему сообщения: <b>Модификация ячейки</b>.
            </p>

            <p>
                Вы дизайнер или просто хотите помочь сделать проект лучше? Очень будем рады!
                Пишите свои вопросы/пожелания/предложения на тот-же адрес.
                При этом просьба указывать соответствующую тему письма, например:
                <b>Предложения по дизайну</b>.
            </p>
        </div>
    </div>


    {*
    <img src="/i/emotions/Joy.png" class="joy"/>
    <img src="/i/emotions/Sadness.png" class="sadness"/>
    <img src="/i/emotions/Anger.png" class="anger"/>
    <img src="/i/emotions/Fear.png" class="fear"/>
    <img src="/i/emotions/Disgust.png" class="disgust"/>
    <img src="/i/emotions/BingBong.png" class="bingbong"/>
    *}

    <div id="em-chart">
        <img class="em-progress" alt="loading" src="/ps-content/images/icons/page_loading.gif">
    </div>

    {*if $smarty.const.CROP_USE_EMOTIONS}
    <div class="figure fig-bottom"></div>
    {/if*}

</div>