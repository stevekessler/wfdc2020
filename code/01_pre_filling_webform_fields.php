<?php
use Drupal\webform\Entity\WebformSubmission;

/*
 * In this example we pre-fill a Webform and attach it to a newly created node.
 */

function MY_MODULE_create_webform_and_new_node(\Drupal\node\NodeInterface $node){

    //Get values for function by calling another function
    $variables_we_need = MY_MODULE_get_variables_we_need();

    //Give a value to our variable
    $some_variable = 'Something';

    //Create webform submission.
    $values = [
        'webform_id' => 'webform_machine_name',
        'in_draft' => TRUE,
    ];

    //Use the data array to add values to fields. The key is the name of the field.
    $data = [
        'webform_field_name' => 'some value',
        'webform_field_name2' => $some_variable,
    ];

    //Add the values above to the submission
    $webform_submission=  WebformSubmission::create($values);
    // Set submission data.
    $webform_submission->setData($data);

    //Create the node
    $node = \Drupal\node\Entity\Node::create([
        'type' => 'webform_node',
        'title' => 'New Webform'
    ]);

    //Link to Webform
    $node->field_reference_to_webform = ['target_id' => $webform_submission->webform_id->target_id];

    $node->save();
    $nid = $node->id();

    //Append values to Webform and Save
    $webform_submission->entity_type ='node';
    $webform_submission->entity_id = $nid;
    $webform_submission->save();

    return $nid;
}
