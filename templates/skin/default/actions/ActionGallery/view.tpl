{include file='header.tpl' menu="album"}
<script src="{$sHistoryJsPath}"></script>
<div id="view-image" class="topic gallery-topic">
    {include file="`$sTemplatePathLsgallery`photo_view.tpl" oImage=$oImage bSelectFriends=true bSliderImage=true}
</div>

<div id="image-comments">
    {include
        file='comment_tree.tpl'
        iTargetId=$oImage->getId()
        sTargetType='image'
        iCountComment=$oImage->getCountComment()
        sDateReadLast=$oImage->getDateRead()
        bAllowNewComment=false
        sNoticeNotAllow=$aLang.image_comment_notallow
        sNoticeCommentAdd=$aLang.topic_comment_add
	aPagingCmt=$aPagingCmt}
</div>

{include file='footer.tpl'}