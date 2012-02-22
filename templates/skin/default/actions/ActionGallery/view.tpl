{include file='header.tpl' menu="album"}
<div class="topic gallery-topic">
    {include file='photo_view.tpl' oImage=$oImage bSelectFriends=true bSliderImage=true}
</div>
    
{include 
	file='comment_tree.tpl' 	
	iTargetId=$oImage->getId()
	sTargetType='image'
	iCountComment=$oImage->getCountComment()
	sDateReadLast=$oImage->getDateRead()
	bAllowNewComment=false
	sNoticeNotAllow=$aLang.topic_comment_notallow
	sNoticeCommentAdd=$aLang.topic_comment_add
	aPagingCmt=$aPagingCmt}	    

{include file='footer.tpl'}