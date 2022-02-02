{* PREreview content in the OPS detail view *}
<div class="item prereview">
    <div  class="value" >
    {if $authorization=='request'}
    {if $status=='ok'  }
        <div class="message_header"><a href="{$url}" target="_blank"> <img src="{$baseUrl}/plugins/generic/prereviewPlugin/images/prereview-logo.svg" style="width: 160px !important;"></a></div>    
          <div class="div-header"><div><span>{$numRapidReviews}</span> {translate key="plugins.generic.prereview.view.textRapidReview"}</div><div><span>{$numFullReviews}</span> {translate key="plugins.generic.prereview.view.textLongReview"}</div><div><span>{$numRequests} </span>{if $numRequests > 1}{translate key="plugins.generic.prereview.view.textRequests"}{else}{translate key="plugins.generic.prereview.view.textRequest"}{/if}</div></div>
          {if $numRapidReviews != 0 && (empty($showReviews) || $showReviews==="both" || $showReviews==="rapid")}
          <h2>Rapid PREreviews</h2>
              <div class="general-container"><div class="ask-container ">{translate key="plugins.generic.prereview.question.ynNovel"}</div><div class="option-container">{$rapidReviews.ynNovel}</div></div>
              <div class="general-container"><div class="ask-container ">{translate key="plugins.generic.prereview.question.ynFuture"}</div><div class="option-container">{$rapidReviews.ynFuture}</div></div>
              <div class="general-container"><div class="ask-container ">{translate key="plugins.generic.prereview.question.ynReproducibility"}</div><div class="option-container">{$rapidReviews.ynReproducibility}</div></div>
              <div class="general-container"><div class="ask-container ">{translate key="plugins.generic.prereview.question.ynMethods"}</div><div class="option-container">{$rapidReviews.ynMethods}</div></div>
              <div class="general-container"><div class="ask-container ">{translate key="plugins.generic.prereview.question.ynCoherent"}</div><div class="option-container">{$rapidReviews.ynCoherent}</div></div>
              <div class="general-container"><div class="ask-container ">{translate key="plugins.generic.prereview.question.ynLimitations"}</div><div class="option-container">{$rapidReviews.ynLimitations}</div></div>
              <div class="general-container"><div class="ask-container ">{translate key="plugins.generic.prereview.question.ynEthics"}</div><div class="option-container">{$rapidReviews.ynEthics}</div></div>
              <div class="general-container"><div class="ask-container ">{translate key="plugins.generic.prereview.question.ynNewData"}</div><div class="option-container">{$rapidReviews.ynNewData}</div></div>
              <div class="general-container"><div class="ask-container ">{translate key="plugins.generic.prereview.question.ynAvailableData"}</div><div class="option-container">{$rapidReviews.ynAvailableData}</div></div>
              <div class="general-container"><div class="ask-container ">{translate key="plugins.generic.prereview.question.ynAvailableCode"}</div><div class="option-container">{$rapidReviews.ynAvailableCode}</div></div>
              <div class="general-container"><div class="ask-container ">{translate key="plugins.generic.prereview.question.ynRecommend"}</div><div class="option-container">{$rapidReviews.ynRecommend}</div></div>
              <div class="general-container"><div class="ask-container ">{translate key="plugins.generic.prereview.question.ynPeerReview"}</div><div class="option-container">{$rapidReviews.ynPeerReview}</div></div>
          {/if}
          {if $numFullReviews != 0 && (empty($showReviews) || $showReviews==="both" || $showReviews==="full")}
          <div class='full-reviews'>
            <h2>Full PREreviews</h2>
            {foreach item=full from=$fullReviews}
              <div class='author-name'>
                <h3>{$full.name}</h3>
                <p id='btn-{$full.id}' class='more' onclick='more({$full.id})'><a>More</a></p>
                <div id='div-{$full.id}'class='contents'>{$full.content}</div>
                <p id='less-{$full.id}' class='less' onclick='less({$full.id})'><a>Less</a></p>
              </div>
            {/foreach} 
          </div>
          {/if}
          <p><a href="{$url}" target="_blank">{translate key="plugins.generic.prereview.view.textViewSite"} </a></p>
        {else}
          <div class="message_header"> 
            <img src="{$baseUrl}/plugins/generic/prereviewPlugin/images/prereview-logo.svg" style="width: 160px !important;">
          </div>
          <div class="message">
           {translate key="plugins.generic.prereview.view.message"}
          </div>
        {/if}
      {/if}
    </div>
</div>
