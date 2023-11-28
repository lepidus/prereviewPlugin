<tab id="prereviewTab" label="{translate key="plugins.generic.prereview.title"}">
	<link rel="stylesheet" href="{$baseUrl}/plugins/generic/prereviewPlugin/css/prereview.css" type="text/css" />
	{capture assign=updateRequestUrl}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.prereviewPlugin.controllers.grid.PrereviewGridHandler" op="updateRequest" submissionId=$submissionId escape=false}{/capture}

	<div class="pkp_form pkpForm" id="editRequest">
		{fbvFormSection class="section-author pkpFormField--options" list="true" translate=false}
			<p class="pre-icon">
			<img src="{$baseUrl}/plugins/generic/prereviewPlugin/images/prereview-logo.svg">
			</p>
			<p>{translate key='plugins.generic.prereview.option.description'}</p>
			<h3 class="label">
				{translate key="plugins.generic.prereview.option.select"}<span class="req">*</span>
			</h3>

			{fbvElement type="radio" label="plugins.generic.prereview.option.display" value="display" id="prereviewDisplay" name="prereviewAuthorization" required="true" checked=$selected|compare:"display"}
			{fbvElement type="radio" label="plugins.generic.prereview.option.notdisplay" value="notdisplay" id="prereviewNotdisplay" name="prereviewAuthorization" checked=$selected|compare:"notdisplay"}
		{/fbvFormSection}

		{fbvFormSection class="formButtons pkpFormPage__footer"}
			<button id="editRequestSubmit" type="button" class="pkp_button submitFormButton">{translate key="common.save"}</button>
		{/fbvFormSection}

	</div>
	
	<script>
		function updatedRequestSuccess(){ldelim}
			alert("{translate key="form.saved"}");
		{rdelim}

		async function updateRequestPrereview(e){ldelim}
			$.post(
				"{$updateRequestUrl}",
				{ldelim}
					prereviewAuthorization: $('input[name=prereviewAuthorization]:checked').val()
				{rdelim},
				updatedRequestSuccess()
			);
		{rdelim}

		$(function(){ldelim}
			$('#editRequestSubmit').click(updateRequestPrereview);
		{rdelim});
	</script>
</tab>