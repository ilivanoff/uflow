<div class="cell">
    <div class="cell-view">
        {if $cell->existsImgBig()}
            <img src="/c/{$cell->getCellId()}/big.png"/>
        {else}
            <img src="/i/blank.png"/>
        {/if}
        <div class="cell-content">
            <div class="date">{$cell->getDtEvent()}</div>
            <div class="text">{$cell->getText4Show()}</div>
            <div class="auth">{$cell->getAuthor4Show()}</div>
        </div>
        <div class="clearall"></div>
    </div>
</div>

<div class="vk-like-holder">
    {*VK Like button*}
    <div class="c-like" id="vk_like_{$cell->getCellId()}">
    </div>
    {FB like button}
    <div class="fb-like" 
         data-href="http://thflow.com/cell.php?id={$cell->getCellId()}" 
         data-layout="button_count" 
         data-action="like" 
         data-show-faces="true">
    </div>
</div>
<div class="vk-comments-holder">
    <div id="vk_comments">
    </div>
</div>