<div>
{assign var="oUser" value=$oImage->getUser()}
{assign var="oVote" value=$oImage->getVote()}
{assign var="oPrevImage" value=$oImage->getPrevImage()}
{assign var="oNextImage" value=$oImage->getNextImage()}
<div class="image-wrap">
    {if $bSelectFriends}
    <div id="wrapper-notice">
        <div id="select-people-notice" class="hidden">
            <span>{$aLang.lsgallery_image_mark_notice}</span> <a href="#" id="image-mark-ready">{$aLang.lsgallery_ready}</a>
        </div>
    </div>    
    {/if}
    <div id="image" class="content">
        {if !$bSelectFriends}
        <a href="{$oImage->getUrlFull()}" >
            <img class="gallery-big-photo" id="{$oImage->getId()}" src="{$oImage->getWebPath('600')}" alt="image" />
        </a>
        {else}
            <img class="gallery-big-photo" id="{$oImage->getId()}" src="{$oImage->getWebPath('600')}" alt="image" />
        {/if}
        {foreach from=$aImageUser item=oImageUser}
                {assign var="oTargetUser" value=$oImageUser->getTargetUser()}
                {if $oImageUser->getStatus() == 'new' || $oImageUser->getStatus() == 'confirmed' || ($oImageUser->getStatus() == 'declined' && $oUserCurrent && ($oTargetUser->getId() == $oUserCurrent->getId() || $oImage->getUserId() == $oUserCurrent->getId() || $ouserCurrent->isAdministrator()))}
                    <div class="image-marker" id="marked-user-{$oImageUser->getTargetUserId()}" 
                         style="top: {$oImageUser->getLassoY()}px; left: {$oImageUser->getLassoX()}px; width: {$oImageUser->getLassoW()}px; height: {$oImageUser->getLassoH()}px;">
						<div class="marker-wrap" style="width: {$oImageUser->getLassoW()}px; height: {$oImageUser->getLassoH()}px;  display: none;"> 
							<div class="marker-inside" style="width: {$oImageUser->getLassoW()-2}px; height: {$oImageUser->getLassoH()-2}px"></div>
							<div class="user-href-wrap"><a class="user" href="{$oTargetUser->getUserWebPath()}">{$oTargetUser->getLogin()}</a></div>
						</div>
                    </div>
                {/if} 
        {/foreach}            
    </div>
</div>
    {if $bSliderImage}
        <div class="gallery-navigation">
            {if $oPrevImage}
                <a class="gal-right" href="{$oPrevImage->getUrlFull()}">
                    <img src="{$oPrevImage->getWebPath('40crop')}" alt="Image" />
                    →
                </a>
            {/if}

            <a class="gal-expend" href="{$oImage->getWebPath()}">{$aLang.lsgallery_image_zoom}</a>

            {if $oNextImage}
                <a class="gal-left" href="{$oNextImage->getUrlFull()}">
                    ←
                    <img src="{$oNextImage->getWebPath('40crop')}" alt="image">
                </a>
            {/if}
        </div>    
    {/if}    
    {if $oCurrentImageUser && $oCurrentImageUser->getStatus() =='new'}
        {assign var="oTargetUser" value=$oCurrentImageUser->getTargetUser()}
        <div id="current-image-user">
			<div class="inside">
             {$aLang.lsgallery_uwere_marked} 
				<div class="current-image-options">
				 <a href="#" class="confirmed" onclick="ls.gallery.changeMark({$oImage->getId()}, {$oCurrentImageUser->getTargetUserId()}, 'confirmed', this); return false;"><span class="ico"></span><span class="txt">{$aLang.lsgallery_mark_confirm}</span></a>
				 <a href="#" class="declined" onclick="ls.gallery.changeMark({$oImage->getId()}, {$oCurrentImageUser->getTargetUserId()}, 'declined', this); return false;"><span class="ico"></span><span class="txt">{$aLang.lsgallery_mark_decline}</span></a>
				 <a href="#" class="remove-own" onclick="ls.gallery.removeMark({$oImage->getId()}, {$oCurrentImageUser->getTargetUserId()}, this); return false;"><span class="ico"></span><span class="txt">{$aLang.lsgallery_mark_remove}</span></a>
				</div>
			</div>
		</div>    
    {/if}
    {if $bSelectFriends}
        <ul id="selected-people">
            {foreach from=$aImageUser item=oImageUser}
                {assign var="oTargetUser" value=$oImageUser->getTargetUser()}
                {if $oImageUser->getStatus() == 'new'}
                    <li id="target-{$oImageUser->getTargetUserId()}" class="selected-new">
                        <a class="user" href="{$oTargetUser->getUserWebPath()}">{$oTargetUser->getLogin()}</a>
                        {if $oUserCurrent}
                            {if $oImage->getUserId() == $oUserCurrent->getId()}
                                <a href="#" class="remove" onclick="ls.gallery.removeMark({$oImage->getId()}, {$oImageUser->getTargetUserId()}, this); return false;"></a>
                            {/if}
                        {/if}    
                    </li>
                {else if $oImageUser->getStatus() == 'confirmed'}    
                    <li id="target-{$oImageUser->getTargetUserId()}" class="selected-confimed">
                        <a class="user" href="{$oTargetUser->getUserWebPath()}">{$oTargetUser->getLogin()}</a>
                        {if $oUserCurrent && ($oTargetUser->getId() == $oUserCurrent->getId() || $oUserCurrent->getId() == $oImage->getUserId())}
                            <a href="#" class="remove" onclick="ls.gallery.removeMark({$oImage->getId()}, {$oImageUser->getTargetUserId()}, this); return false;"></a>
                        {/if}    
                    </li>
                {else if $oImageUser->getStatus() == 'declined' && $oUserCurrent && 
                    ($oTargetUser->getId() == $oUserCurrent->getId() || $oImage->getUserId() == $oUserCurrent->getId() || $oUserCurrent->isAdministrator())}    
                    <li id="target-{$oImageUser->getTargetUserId()}" class="selected-declined">
                        <a class="user" href="{$oTargetUser->getUserWebPath()}">{$oTargetUser->getLogin()}</a>
                        <a href="#" class="remove" onclick="ls.gallery.removeMark({$oImage->getId()}, {$oImageUser->getTargetUserId()}, this); return false;"></a>
                    </li>
                {/if}
            {/foreach}
        </ul>    
        {if $oUserCurrent} 
            <div id="select-friends">
                <a id="mark" href="#">{$aLang.lsgallery_image_mark_friend}</a>
                <div class="mark-name" style="display:none;">
                    <input type="text" class="autocomplete-friend" value=""/>
                    <a href="#" class="submit-selected-friend">{$aLang.lsgallery_image_mark}</a>
                    <a href="#" class="cancel-selected-friend">{$aLang.lsgallery_cancel}</a>
                </div>
            </div>    
        {/if}    
    {/if}
    <div class="content">
		{$oImage->getDescription()|escape:'html'}
	</div>
    
    <ul class="tags">
		{foreach from=$oImage->getTagsArray() item=sTag name=tags_list}
			<li><a href="{router page='gallery'}tag/{$sTag|escape:'url'}/">{$sTag|escape:'html'}</a>{if !$smarty.foreach.tags_list.last}, {/if}</li>
		{/foreach}                                                             
	</ul>
    
    <ul class="info">
		<li id="vote_area_image_{$oImage->getId()}" class="voting 
            {if $oVote || ($oUserCurrent && $oImage->getUserId()==$oUserCurrent->getId()) || 
        strtotime($oImage->getDateAdd())<$smarty.now-$oConfig->GetValue('acl.vote.topic.limit_time')}{if $oImage->getRating()>0}
        positive {elseif $oImage->getRating()<0}negative{/if}{/if} {if !$oUserCurrent || $oImage->getUserId()==$oUserCurrent->getId() || strtotime($oImage->getDateAdd())<$smarty.now-$oConfig->GetValue('acl.vote.topic.limit_time')}guest{/if}{if $oVote} voted {if $oVote->getDirection()>0}plus{elseif $oVote->getDirection()<0}minus{/if}{/if}">
			<a href="#" class="plus" onclick="return ls.vote.vote({$oImage->getId()},this,1,'image');"></a>
			<span id="vote_total_image_{$oImage->getId()}" class="total" title="{$aLang.topic_vote_count}: {$oImage->getCountVote()}">
        {if $oVote || ($oUserCurrent && $oImage->getUserId()==$oUserCurrent->getId()) || 
strtotime($oImage->getDateAdd())<$smarty.now-$oConfig->GetValue('acl.vote.topic.limit_time')} 
{$oImage->getRating()} {else} <a href="#" onclick="return ls.vote.vote({$oImage->getId()},this,0,'image');">?</a> {/if}</span>
			<a href="#" class="minus" onclick="return ls.vote.vote({$oImage->getId()},this,-1,'image');"></a>
		</li>
		<li class="date">{date_format date=$oImage->getDateAdd()}</li>
		<li class="username"><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></li>
        <li>
            <a href="#" onclick="return ls.favourite.toggle({$oImage->getId()},this,'image');" class="favourite gallery-favourite {if $oUserCurrent && $oImage->getIsFavourite()}active{/if}">
                <span class="icon"></span><span class="favourite-count" id="fav_count_image_{$oImage->getId()}">{if $oImage->getCountFavourite()>0}{$oImage->getCountFavourite()}{else}&nbsp;{/if}</span>
            </a>
        </li>
	</ul>
</div>