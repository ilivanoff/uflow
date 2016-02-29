<div id="carrier">
    {* ЛЕВАЯ ПАНЕЛЬ *}
    <div class="container">

        <div class="top-buttons">
            <label for="choose-file" class="choose-file-label">
                Загрузить фото
            </label>
            <input type="file" id="choose-file" accept="image/*" />
        </div>

        <div class="progress">
            <img src="/ps-content/images/icons/page_loading.gif" alt="loading">
        </div>

        <div class="info_box warn">
            Error message
        </div>

        <div class="crop-editor">
            {*<div class="crop-holder"></div>*}
        </div>
        {*
        <div class="emotions noselect">
        <img src="/i/emotions/Joy-nc.png" class="joy"/>
        <img src="/i/emotions/Sadness.png" class="sadness"/>
        <img src="/i/emotions/Anger-nc.png" class="anger"/>
        <img src="/i/emotions/Fear-nc.png" class="fear"/>
        <img src="/i/emotions/Disgust-nc.png" class="disgust"/>
        <img src="/i/emotions/BingBong-nc.png" class="bingbong"/>
        </div>
        *}

        <div class="emotions noselect">
            <span class="joy hint--top hint--rounded hint--joy active" data-hint="Радость" data-code="{CropConst::EMOTION_JOY}"></span>
            <span class="sadness hint--top hint--rounded hint--sadness" data-hint="Печаль" data-code="{CropConst::EMOTION_SADNESS}"></span>
            <span class="anger hint--top hint--rounded hint--anger" data-hint="Злость" data-code="{CropConst::EMOTION_ANGER}"></span>
            <span class="fear hint--top hint--rounded hint--fear" data-hint="Страх" data-code="{CropConst::EMOTION_FEAR}"></span>
            <span class="disgust hint--top hint--rounded hint--success" data-hint="Брезгливость" data-code="{CropConst::EMOTION_DISGUST}"></span>
            <span class="bingbong hint--top hint--rounded" data-hint="Бинго Бонг" data-code="{CropConst::EMOTION_BINGABONG}"></span>
        </div>

        {*
        <div class="emotions noselect">
        <div class="joy">
        <span class="joy"></span>
        Радость
        </div>
        <div class="sadness">
        <span class="sadness"></span>
        Печаль
        </div>
        <div class="anger">
        <span class="anger"></span>
        Злость
        </div>
        <div class="disgust">
        <span class="disgust"></span>
        Брезгливость
        </div>
        <div class="fear">
        <span class="fear"></span>
        Страх
        </div>
        <div class="bingbong">
        <span class="bingbong"></span>
        Бинго Бонг
        </div>
        </div>
        *}

        <div class="crop-text">
            <div class="crop-textarea-holder">
                <textarea ml={$smarty.const.CROP_MSG_MAX_LEN}></textarea>
            </div>
        </div>

        {*PsHtmlForm::capture()*}

        <div class="bottom-buttons">
            <button>Опубликовать</button>
        </div>

    </div>

    {* ПРАВАЯ ПАНЕЛЬ *}
    <div class="crop-menu noselect">
        <div class="btn-box btn-group zoom">
            <a href="#zoomPlus"><i class="fa fa-search-plus"></i></a>
            <a href="#zoomMinus"><i class="fa fa-search-minus"></i></a>
        </div>

        <div class="btn-box btn-group rotate">
            <a href="#rotateLeft"><i class="fa fa-rotate-left"></i></a>
            <a href="#rotateRight"><i class="fa fa-rotate-right"></i></a>
        </div>

        <div id="PresetFilters" class="btn-box">
            <a href="#vintage">Vintage</a>
            <a href="#lomo">Lomo</a>
            <a href="#clarity">Clarity</a>
            <a href="#sinCity">Sin City</a>
            <a href="#sunrise">Sunrise</a>
            <a href="#crossProcess">Cross Process</a>
            <a href="#orangePeel">Orange Peel</a>
            <a href="#love">Love</a>
            <a href="#grungy">Grungy</a>
            <a href="#jarques">Jarques</a>
            <a href="#pinhole">Pinhole</a>
            <a href="#oldBoot">Old Boot</a>
            <a href="#glowingSun">Glowing Sun</a>
            <a href="#hazyDays">Hazy Days</a>
            <a href="#herMajesty">Her Majesty</a>
            <a href="#nostalgia">Nostalgia</a>
            <a href="#hemingway">Hemingway</a>
            <a href="#concentrate">Concentrate</a>
        </div>

        <div class="clearall"></div>

        <div class="crop-preview"></div>

        <div class="crop-grid">
            <div class="crop-preview"></div>
        </div>

    </div>

    <div class="clearall"></div>
</div>