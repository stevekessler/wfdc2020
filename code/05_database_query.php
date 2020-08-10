<?php
$database = \Drupal::database();
$select = $database->select('webform_submission', 'ws')
    ->fields('ws', ['webform_id', 'sid', 'uid'])
->fields('wsd', ['value'])
    ->join('webform_submission_data', 'wsd', 'ws.sid = wsd.sid')
    ->condition('ws.in_draft', '0' )
    ->condition('wsd.value', 0, '>')
    ->condition('wsd.name', 'hga1c_value');


$executed = $select->execute();
$completed_assessment_result = $executed->fetchAssoc();

ksm($completed_assessment_result);
