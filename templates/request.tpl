<link rel="stylesheet" href="{$baseUrl}/plugins/generic/prereviewPlugin/css/prereview.css" type="text/css" />
{* Form to display radiobuttons of choice of display at the end of a shipment. *}
{fbvFormSection class="section-author pkpFormField--options" list="true"  translate=false}
	<p class="pre-icon">
	<img src="{$baseUrl}/plugins/generic/prereviewPlugin/images/prereview-logo.svg">
	</p>
	<p>{translate key='plugins.generic.prereview.description.p1'}</p>
	<p>{translate key='plugins.generic.prereview.description.p2'}</p>
	<p>{translate key='plugins.generic.prereview.description.p3'}</p>
	<h3 class="label">Select option<span class="req">*</span></h3>


	{fbvElement type="radio" label="plugins.generic.prereview.description.buttoncheck" value="request" id="prereviewRequest" name="prereview:authorization" class="required" required="true"}
	{fbvElement type="radio" label="plugins.generic.prereview.description.notdisplay" value="notdisplay" id="prereviewNotdisplay" name="prereview:authorization" }
	{fbvElement type="radio" label="plugins.generic.prereview.description.notsolicit" value="no" id="prereviewNo" name="prereview:authorization" }
	
{/fbvFormSection}
