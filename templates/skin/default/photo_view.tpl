<div>
    {assign var="oUser" value=$oImage->getUser()}
    {assign var="oVote" value=$oImage->getVote()}
    <div class="image-wrap">
        {hookb run="image_status_list" oImage=$oImage oAlbum=$oAlbum bSliderImage=$bSliderImage}
            <div id="image" class="content">
                {if $bSliderImage}
                    <img class="gallery-big-photo" id="{$oImage->getId()}" src="{$oImage->getWebPath('638')}" alt="image" />
                {else}
                    <a href="{$oImage->getUrlFull()}" >
                        <img class="gallery-big-photo" id="{$oImage->getId()}" src="{$oImage->getWebPath('638')}" alt="image" />
                    </a>
                {/if}
            </div>
        {/hookb}
    </div>
    {if $bSliderImage}
        <div class="gallery-navigation">
            {if $oPrevImage}
                <a class="gal-right ajaxy" href="{$oPrevImage->getUrlFull()}/{$sOrder}">
                    <img src="{$oPrevImage->getWebPath('40crop')}" alt="Image" />
                    →
                </a>
            {/if}

            <a class="gal-expend" href="{$oImage->getWebPath()}">{$aLang.plugin.lsgallery.lsgallery_image_zoom}</a>

            {if $oNextImage}
                <a class="gal-left ajaxy" href="{$oNextImage->getUrlFull()}/{$sOrder}">
                    ←
                    <img src="{$oNextImage->getWebPath('40crop')}" alt="image">
                </a>
            {/if}
        </div>
    {/if}

    {hook run='image_content_after' oImage=$oImage oAlbum=$oAlbum bSliderImage=$bSliderImage }

    <div class="topic-content">
		{$oImage->getDescription()|strip_tags}
	</div>

    <footer class="topic-footer">
        <ul class="topic-tags">
            <li>
                <i class="icon-synio-tags"></i>
            </li>
            {foreach from=$oImage->getTagsArray() item=sTag name=tags_list}
                <li><a href="{router page='gallery'}tag/{$sTag|escape:'url'}/">{$sTag|escape:'html'}</a>{if !$smarty.foreach.tags_list.last}, {/if}</li>
            {/foreach}
        </ul>

        <ul class="topic-info">
            <li class="topic-info-author">
                <a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(24)}" alt="avatar" class="avatar" /></a>
                <a rel="author" href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
            </li>
            <li class="topic-info-date">
                <time datetime="{date_format date=$oImage->getDateAdd() format='c'}" title="{date_format date=$oImage->getDateAdd() format='j F Y, H:i'}">
                {date_format date=$oImage->getDateAdd() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}
                </time>
            </li>
            <li class="topic-info-favourite" onclick="return ls.favourite.toggle({$oImage->getId()},$('#fav_image_{$oImage->getId()}'),'image');">
                <i id="fav_image_{$oImage->getId()}" class="favourite {if $oUserCurrent && $oImage->getIsFavourite()}active{/if}"></i>
                <span class="favourite-count" id="fav_count_image_{$oImage->getId()}">{if $oImage->getCountFavourite()>0}{$oImage->getCountFavourite()}{/if}</span>
            </li>
            {if $oVote || ($oUserCurrent && $oImage->getUserId() == $oUserCurrent->getId()) || strtotime($oImage->getDateAdd()) < $smarty.now-$oConfig->GetValue('acl.vote.image.limit_time')}
                {assign var="bVoteInfoShow" value=true}
            {/if}
            <li class="topic-info-vote">
                <div id="vote_area_image_{$oImage->getId()}" class="vote-topic
                {if $oVote || ($oUserCurrent && $oImage->getUserId() == $oUserCurrent->getId()) || strtotime($oImage->getDateAdd()) < $smarty.now-$oConfig->GetValue('acl.vote.image.limit_time')}
                    {if $oImage->getRating() > 0}
                        vote-count-positive
                    {elseif $oImage->getRating() < 0}
                        vote-count-negative
                    {elseif $oImage->getRating() == 0}
                        vote-count-zero
                    {/if}
                {/if}

                {if !$oUserCurrent or ($oUserCurrent && $oImage->getUserId() != $oUserCurrent->getId())}
                    vote-not-self
                {/if}

                {if $oVote}
                    voted

                    {if $oVote->getDirection() > 0}
                        voted-up
                    {elseif $oVote->getDirection() < 0}
                        voted-down
                    {elseif $oVote->getDirection() == 0}
                        voted-zero
                    {/if}
                {else}
                    not-voted
                {/if}

                {if (strtotime($oImage->getDateAdd()) < $smarty.now-$oConfig->GetValue('acl.vote.image.limit_time') && !$oVote) || ($oUserCurrent && $oImage->getUserId() == $oUserCurrent->getId())}
                    vote-nobuttons
                {/if}

                {if strtotime($oImage->getDateAdd()) > $smarty.now-$oConfig->GetValue('acl.vote.iamge.limit_time')}
                    vote-not-expired
                {/if}

                {if $bVoteInfoShow}js-infobox-vote-image{/if}">
                    <div class="vote-item vote-down" onclick="return ls.vote.vote({$oImage->getId()},this,-1,'image');"><span><i></i></span></div>
                    <div class="vote-item vote-count" title="{$aLang.topic_vote_count}: {$oImage->getCountVote()}">
						<span id="vote_total_image_{$oImage->getId()}">
                        {if $bVoteInfoShow}
                            {if $oImage->getRating() > 0}+{/if}{$oImage->getRating()}
                        {else}
                            <i onclick="return ls.vote.vote({$oImage->getId()},this,0,'image');"></i>
                        {/if}
                        </span>
                    </div>
                    <div class="vote-item vote-up" onclick="return ls.vote.vote({$oImage->getId()},this,1,'image');"><span><i></i></span></div>
                {if $bVoteInfoShow}
                    <div id="vote-info-topic-{$oImage->getId()}" style="display: none;">
                        <ul class="vote-image-info">
                            <li><i class="icon-synio-vote-info-up"></i> {$oImage->getCountVoteUp()}</li>
                            <li><i class="icon-synio-vote-info-down"></i> {$oImage->getCountVoteDown()}</li>
                            <li><i class="icon-synio-vote-info-zero"></i> {$oImage->getCountVoteAbstain()}</li>
                        </ul>
                    </div>
                {/if}
                </div>
            </li>
        </ul>
    </footer>
</div>