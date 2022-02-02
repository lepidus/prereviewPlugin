<tab id="prereviewTab" label="{translate key="plugins.generic.prereview.title"}">
	<link rel="stylesheet" href="{$baseUrl}/plugins/generic/prereviewPlugin/css/prereview.css" type="text/css" />
{capture assign=actionUrl}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.prereviewPlugin.controllers.grid.PrereviewGridHandler" op="updateRequest" submissionId=$submissionId escape=false}{/capture}

<form class="pkp_form pkpForm " id="editRequest" method="post" action="{$actionUrl}">


{fbvFormSection class="section-author pkpFormField--options" list="true"  translate=false}
	<p class="pre-icon">
	<img src="{$baseUrl}/plugins/generic/prereviewPlugin/images/prereview-logo.svg">
	</p>
	<p>{translate key='plugins.generic.prereview.description.p1'}</p>
	<p>{translate key='plugins.generic.prereview.description.p2'}</p>
	<p>{translate key='plugins.generic.prereview.description.p3'}</p>
	<h3 class="label">Select option<span class="req">*</span></h3>

	{fbvElement type="radio" label="plugins.generic.prereview.description.buttoncheck" value="request" id="prereviewRequest" name="prereview:authorization" required="true" checked=$publication|compare:"request"}
	{fbvElement type="radio" label="plugins.generic.prereview.description.notdisplay" value="notdisplay" id="prereviewNotdisplay" name="prereview:authorization" checked=$publication|compare:"notdisplay"}
	{fbvElement type="radio" label="plugins.generic.prereview.description.notsolicit" value="no" id="prereviewNo" name="prereview:authorization" checked=$publication|compare:"no"}

{/fbvFormSection}

	{fbvFormSection class="formButtons pkpFormPage__footer"}
		{assign var=buttonId value="submitFormButton"|concat:"-"|uniqid}
		{fbvElement type="submit" class="submitFormButton" id=$buttonId label="common.save"}
	{/fbvFormSection}


</form>

</tab>

<script>
setTimeout("update()",1000);

function update(){
	var button1=$('#titleAbstract').find('.pkpButton');	
	var button2 = $('#prereviewTab').find('.submitFormButton');
	if($(button1).is(':disabled')){
	$(button2).attr("disabled", true);
	}else{
		$(button2).attr("disabled", false);
	}
	setTimeout("update()",1000);
}




</script>