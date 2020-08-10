<?php
/*
 * Form alter for targeting a SPECIFIC form
 * for example if our form is called health_survey than this is our form alter hook
 */

function MY_MODULE_webform_form_webform_submission_health_survey_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id){
    //Get the Webform Object so we can load all the data from the entity
    if ($webform_submission_object = $form_state->getFormObject()->getEntity()) {
        //Determine if we are viewing a draft or not
        if ($webform_submission_object->isDraft() == FALSE) {
            //Load extra CSS
            $form['#attached']['library'][] = 'my_module/my_module_webform.css';

            $intro_message = 'This form is no longer editable';
            //Add Intro Message
            $form['intro_message'] = array(
                '#markup' => t('<div id="survey-view-intro"><strong>' . $intro_message . '</strong></div>'),
                '#weight' => 0,
            );
        }
    }
}