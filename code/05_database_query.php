<?php
//Example of collecting data from both the webform_submission and webform_submission_data tables
$database = \Drupal::database();
$select = $database->select('webform_submission', 'ws')
    ->fields('ws', ['webform_id', 'sid', 'uid']);
$alias = $select->leftJoin('webform_submission_data', 'wsd', 'ws.sid = %alias.sid');
$select->fields($alias, ['value'])
    ->condition('ws.in_draft', '0' )
    ->condition($alias . '.value', 0, '>')
    ->condition($alias . '.name', 'hga1c_value');

$executed = $select->execute();

while ($record = $executed->fetchAssoc()) {
    //Do something with the values we have collected. For example we can print the SID.
    print $record['sid'] ."\n";
}

/*
 * Similar query using SQL
 * SELECT wsd.sid
 * FROM webform_submission_data wsd
 * LEFT JOIN webform_submission ws ON ws.sid = wsd.sid
 * WHERE wsd.name = 'hga1c_value' and wsd.value > 0 and ws.in_draft = 0;
 */