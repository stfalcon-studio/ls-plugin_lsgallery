{include file='header.tpl' menu="album"}
<script src="{$sHistoryJsPath}"></script>
{if $oAlbum->getType() == 'personal'}
    {assign var="bSelectFriends" value=0}
{else}
    {assign var="bSelectFriends" value=1}
{/if}
<div id="view-image" class="topic gallery-topic">
    {include file="`$sTemplatePathLsgallery`photo_view.tpl" oImage=$oImage bSelectFriends=$bSelectFriends bSliderImage=true}
</div>

<div id="image-comments">
    {include
        file='comment_tree.tpl'
        iTargetId=$oImage->getId()
        sTargetType='image'
        iCountComment=$oImage->getCountComment()
        sDateReadLast=$oImage->getDateRead()
        bAllowNewComment=false
        sNoticeNotAllow=$aLang.plugin.lsgallery.plugin.lsgallery.lsgallery_image_comment_notallow
        sNoticeCommentAdd=$aLang.topic_comment_add
	aPagingCmt=$aPagingCmt}
</div>

{include file='footer.tpl'}