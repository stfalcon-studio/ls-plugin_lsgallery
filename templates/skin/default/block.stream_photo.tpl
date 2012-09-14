<ul id="stream-images" class="gallery-photos">
    {foreach from=$aImages item=oImage}
        {assign var="oUser" value=$oImage->getUser()}
        {assign var="oAlbum" value=$oImage->getAlbum()}
        {assign var="oVote" value=$oImage->getVote()}
        <li>
            <a class="gallery-item tooltiped" href="{$oImage->getUrlFull()}">
                <img class="image-100" src="{$oImage->getWebPath('100crop')}" alt="Image" />
            </a>
            
            <div class="gallery-bubble tooltip">
                <div class="gallery-bubble-content">
                    <h3>{$oImage->getDescription()|strip_tags|truncate:60}</h3>
                    <a class="gallery-album-name" href="{$oAlbum->getUrlFull()}">{$oAlbum->getTitle()|escape:'html'}</a>
                    <div class="gallery-album-about">
                        <a class="author" href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
                        <span class="date">{date_format date=$oImage->getDateAdd()}</span>
                        <span class="rating">
                            {$aLang.plugin.lsgallery.lsgallery_image_vote}:
                            <span class="{if $oVote || ($oUserCurrent && $oImage->getUserId()==$oUserCurrent->getId()) || 
                    strtotime($oImage->getDateAdd())<$smarty.now-$oConfig->GetValue('acl.vote.topic.limit_time')}{if $oImage->getRating()>0}
                    positive {elseif $oImage->getRating()<0}negative{/if}{/if}">{if $oVote || ($oUserCurrent && $oImage->getUserId()==$oUserCurrent->getId()) || 
            strtotime($oImage->getDateAdd())<$smarty.now-$oConfig->GetValue('acl.vote.topic.limit_time')} 
            {$oImage->getRating()} {else} ? {/if}</span>
                        </span>
                    </div>
                </div>
            </div>
            <a class="user" href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
        </li>
    {/foreach}
</ul>
<footer>
    {if $sType == 'new'}
        <a href="{router page='gallery'}photo/new">{$aLang.plugin.lsgallery.lsgallery_photo_all_new}</a>
    {else}
        <a href="{router page='gallery'}photo/best">{$aLang.plugin.lsgallery.lsgallery_photo_all_best}</a>
    {/if}
</footer>
<script>
jQuery('document').ready(function(){
    jQuery('#stream-images a.tooltiped').tooltip({
            position: "bottom center",
            offset: [-40, 0]
        });
})
</script>
