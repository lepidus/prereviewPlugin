<link rel="stylesheet" href="{$baseUrl}/plugins/generic/prereviewPlugin/css/prereview.css" type="text/css" />
{* Form to display radiobuttons of choice of display at the end of a shipment. *}
{fbvFormSection class="section-author pkpFormField--options" list="true"  translate=false}
	<p class="pre-icon">
	<img src="{$baseUrl}/plugins/generic/prereviewPlugin/images/prereview-logo.svg">
	</p>
	<p>{translate key='plugins.generic.prereview.option.description'}</p>
	<h3 class="label">
		{translate key="plugins.generic.prereview.option.select"}<span class="req">*</span>
	</h3>


	{fbvElement type="radio" label="plugins.generic.prereview.option.display" value="display" id="prereviewDisplay" name="prereview:authorization" class="required" required="true"}
	{fbvElement type="radio" label="plugins.generic.prereview.option.notdisplay" value="notdisplay" id="prereviewNotdisplay" name="prereview:authorization" }
	
{/fbvFormSection}
