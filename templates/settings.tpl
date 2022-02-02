{**
 * templates/settings.tpl
 * The basic setting tab for the PREreview plugin.
 *}

<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#PrereviewSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>
<form class="pkp_form" id="PrereviewSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" tab="basic" save="true"}">
	<div id="prereviewSettings">
	{csrf}
	{fbvFormArea id="prereviewApiKeys" title="plugins.generic.prereview.apisettings.title" class="border"}
		
		{fbvFormSection for="prereviewApiKeys"}
		<p class="pkp_help">{translate key="plugins.generic.prereview.apisettings"}</p>
			{fbvElement type="text" label="plugins.generic.prereview.apisettings.name" id="prereviewApp" required="true" value=$prereviewApp size=$fbvStyles.size.MEDIUM}
			{fbvElement type="text" label="plugins.generic.prereview.apisettings.key" id="prereviewkey" required="true" value=$prereviewkey size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}	
	{/fbvFormArea}
	{fbvFormArea id="prereviewOption" title="plugins.generic.prereview.apisettings.option"}
		{fbvFormSection list="true"}
			<p class="pkp_help">{translate key="plugins.generic.prereview.apisettings.optionDescription"}</p>
			<p class="pkp_help">{translate key="plugins.generic.prereview.apisettings.optionDescription.ask"}</p>
			{fbvElement type="radio" id="showRevisions" name="showRevisions" value="rapid" label="plugins.generic.prereview.apisettings.showrevisions"  checked=$showRevisions|compare:"rapid"}
			{fbvElement type="radio" id="showRevisionsFull" name="showRevisions" value="full" label="plugins.generic.prereview.apisettings.showrevisionsLong" checked=$showRevisions|compare:"full"}
			{fbvElement type="radio" id="showRevisionsBoth" name="showRevisions" value="both" label="plugins.generic.prereview.apisettings.showrevisionsBoth" checked=$showRevisions|compare:"both"}
		{/fbvFormSection}

	{/fbvFormArea}
	{fbvFormButtons}

	</div>
</form>
