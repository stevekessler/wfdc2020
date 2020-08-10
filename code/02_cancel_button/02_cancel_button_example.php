<?php
/*
 * Form alter for targeting a SPECIFIC form
 * for example if our form is called health_survey than this is our form alter hook
 */

function MY_MODULE_webform_form_webform_submission_health_survey_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id){
    //Get the Webform Object so we can load all the data from the entity
    if ($webform_submission_object = $form_state->getFormObject()->getEntity()) {
        //Determine if we are viewing a draft or not
        if ($webform_submission_object->isDraft() == TRUE) {
           //Check if the current user has a custom permission to cancel the webform
            if (\Drupal::currentUser()->hasPermission('access cancel webform')) {
                //Get the ID of the current node so that it can be passed as an argument to the modal
                $node = \Drupal::routeMatch()->getParameter('node');
                $node_id = $node->id();
                //Add a cancel button to the array of buttons at the bottom of the Webform
                $form['actions']['cancel'] = [
                    '#type' => 'link',
                    '#url' => \Drupal\Core\Url::fromRoute('name_of_route_from_routing_file', ['node' => $node_id]),
                    '#title' => 'Cancel',
                    '#attributes' => [
                        'class' => ['btn', 'btn-default', 'btn-rounded', 'use-ajax',],
                        'data-dialog-type' => 'modal',
                        'data-dialog-options' => json_encode([
                            'width' => '70%',
                            'minHeight' => '70%'
                        ]),
                    ],
                ];
            }
        }
    }
}

function _YOUR_MODULE_custom_webform_cancel_function($nid, $sid){
    if($webform_node = \Drupal::entityTypeManager()->getStorage('node')->load($nid)){
        $webform_node->delete();
        if($webform_to_delete = \Drupal::entityTypeManager()->getStorage('webform_submission')->load($sid)){
            //Get more field information
            $some_field = $webform_to_delete->getElementData('summary_note_nid');
            $webform_to_delete->delete();
        }
    }
}