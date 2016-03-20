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

        {if $smarty.const.CROP_USE_EMOTIONS}
            <div class="emotions noselect">
                <span class="joy hint--top hint--rounded hint--joy active" data-hint="Радость" data-code="{CropConst::EMOTION_JOY}"></span>
                <span class="sadness hint--top hint--rounded hint--sadness" data-hint="Печаль" data-code="{CropConst::EMOTION_SADNESS}"></span>
                <span class="anger hint--top hint--rounded hint--anger" data-hint="Злость" data-code="{CropConst::EMOTION_ANGER}"></span>
                <span class="fear hint--top hint--rounded hint--fear" data-hint="Страх" data-code="{CropConst::EMOTION_FEAR}"></span>
                <span class="disgust hint--top hint--rounded hint--success" data-hint="Брезгливость" data-code="{CropConst::EMOTION_DISGUST}"></span>
                <span class="bingbong hint--top hint--rounded" data-hint="Пофигизм:)" data-code="{CropConst::EMOTION_BINGABONG}"></span>
            </div>
        {/if}

        <div class="crop-email">
            <input type="text" placeholder="Ваш email" />
            <span class="hint hint--right hint--rounded hint--error" data-hint="email нигде не публикуется и нужен для управления ячейкой"><span class="fa fa-at"></span></span>
        </div>

        <div class="crop-text">
            <div class="crop-textarea-holder">
                <textarea data-manual="1" data-ml="{$smarty.const.CROP_MSG_MAX_LEN}" placeholder="Ваши мысли..."></textarea>
            </div>
        </div>

        <div id="google-recaptcha">
            <img src="/ps-content/images/icons/page_loading.gif" alt="loading">
        </div>

        <div class="bottom-buttons">
            <button>Опубликовать</button>
        </div>

    </div>

    {* ПРАВАЯ ПАНЕЛЬ *}
    <div class="crop-menu noselect">
        <div class="btn-box transform">
            <div class="btn-group">
                <a href="#zoomIn"><i class="fa fa-search-plus"></i></a>
                <a href="#zoomOut"><i class="fa fa-search-minus"></i></a>
            </div>

            <div class="btn-group">
                <a href="#rotateL"><i class="fa fa-rotate-left"></i></a>
                <a href="#rotateR"><i class="fa fa-rotate-right"></i></a>
            </div>

            <div class="btn-group">
                <a href="#moveL"><i class="fa fa fa-arrow-left"></i></a>
                <a href="#moveR"><i class="fa fa fa-arrow-right"></i></a>
            </div>

            <div class="btn-group">
                <a href="#moveU"><i class="fa fa-arrow-up"></i></a>
                <a href="#moveD"><i class="fa fa-arrow-down"></i></a>
            </div>

            <div class="btn-group">
                <a href="#reflectX" data-o1="-1"><i class="fa fa-arrows-h"></i></a>
                <a href="#reflectY" data-o1="-1"><i class="fa fa-arrows-v"></i></a>
            </div>

            <div class="btn-group">
                <a href="#reset" data-o1="-1"><i class="fa fa-refresh"></i></a>
                <a href="#close" data-o1="-1"><i class="fa fa-remove"></i></a>
            </div>
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