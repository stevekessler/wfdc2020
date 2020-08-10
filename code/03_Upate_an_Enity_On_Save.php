<?php
use Drupal\webform\Entity\WebformSubmission;

//Use hook webform_submission_update to be able to run processes after a Webform is submitted
function MY_MODULE_webform_submission_update(WebformSubmissionInterface $webform_submission){
    //If the submission is no longer a draft we know it is final.
    if ($webform_submission->isDraft() == FALSE) {
        //We can test form value and the form type and other data
        $some_field = $webform_submission->getElementData('some_field');
        $webform_type = $webform_submission->getWebform()->id();

        if ($some_field ==  'some_value'){
            //Do something
        }
    }
}