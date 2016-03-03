<div class="cell">
    <div class="cell-view">
        {if $cell->existsImgBig()}
            <img src="/c/{$cell->getCellId()}/big.png"/>
        {else}
            <img src="/i/blank.png"/>
        {/if}
        <div class="content">
            <div class="date">
                {$cell->getDtEvent()}
            </div>
            <div>
                {html_4show($cell->getText())}
            </div>
        </div>
        <div class="clearall"></div>
    </div>
</div>

<div class="vk-like-holder">
    <div id="vk_like"></div>
</div>

<div class="vk-comments-holder">
    <div id="vk_comments">
    </div>
</div>