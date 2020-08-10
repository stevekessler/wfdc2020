<?php
namespace Drupal\YourModulesName\Form;

use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use \Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * ModalForm class.
 */
class WebformCancelButton extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
      //The ID for the form
    return 'webform_cancel_button';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {
    $nid = $node->id();
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    //Query to get the SID (Webform ID) for the Node
    $database = \Drupal::database();
    $select = $database->select('webform_submission', 'ws')
      ->fields('ws', ['in_draft', 'sid'])
      ->condition('ws.entity_id', $nid, '=');

    $executed = $select->execute();
    $webform_to_delete = $executed->fetchAssoc();

    $sid = $webform_to_delete['sid'];
    $draft = $webform_to_delete['in_draft'];

    $webform_submission = \Drupal::entityTypeManager()->getStorage('webform_submission')->load($sid);

    $form['sid'] = [
      '#type' => 'hidden',
      '#value' => $sid,
    ];

    //You can bring in a field from the Webform.
    $some_field = $webform_submission->getElementData('some_field');

    //In this example we are just pulling the value into the hidden field
    $form['some_filed'] = [
      '#type' => 'hidden',
      '#value' => $some_field,
    ];

    $webform_type =  $webform_submission->getWebform()->label();

      $form['#prefix'] = '<div id="cancel_modal_form">';
      $form['#suffix'] = '</div>';

      // The status messages that will contain any form errors.

      $form['status_messages'] = [
        '#type' => 'status_messages',
        '#weight' => -10,
      ];

      if ($draft == 1){
        $form['content'] = [
          '#type' => 'markup',
          '#markup' => 'Clicking <em>Cancel</em> will <strong>permanently</strong> remove your ' . $webform_type . ' from the website. This cannot be undone.',
          '#weight' => '10',
        ];

        $form['actions'] = array('#type' => 'actions');
        $form['actions']['delete'] = [
          '#type' => 'submit',
          '#value' => $this->t('Delete Webform'),
          '#attributes' => [
            'class' => [
              'use-ajax-submit',
            ],
          ],
          '#name' => 'cancel',
          '#weight' => '20',
        ];
        $form['actions']['close'] = [
          '#type' => 'submit',
          '#value' => $this->t('Return to Webform'),
          '#attributes' => [
            'class' => [
              'use-ajax-submit',
            ],
          ],

        '#name' => 'close',
        '#weight' => '21',
        ];

       if (\Drupal::request()->isXmlHttpRequest()) {
        $form['actions']['cancel']['#attributes']['class'][] = 'use-ajax-submit';
        $form['actions']['close']['#attributes']['class'][] = 'use-ajax-submit';
      }
    }else{
        $form['content'] = [
          '#type' => 'markup',
          '#markup' => 'Your ' . $webform_type . ' is not a draft and cannot be canceled. Please contact support if you believe
           you are receiving this message in error. ',
          '#weight' => '10',
        ];
        \Drupal::logger('YOUR_MODULE')->error('An attempt was made to delete ' . $nid . ' and '
          . $sid . ' however this '. $webform_type.' is not a draft.');
        $form['actions'] = array('#type' => 'actions');
        $form['actions']['close'] = [
          '#type' => 'submit',
          '#value' => $this->t('Return to ' . $webform_type),
          '#attributes' => [
            'class' => [
              'use-ajax-submit',
            ],
          ],

          '#name' => 'close',
          '#weight' => '21',
        ];

    }

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    if ($triggering_element['#name'] == 'cancel') {
      $nid = $form_state->getValue('nid');
      $sid = $form_state->getValue('sid');
      $member_id = $form_state->getValue('member_id');
      _YOUR_MODULE_custom_webform_cancel_function($nid, $sid);
      if (\Drupal::request()->isXmlHttpRequest()) {
        $response = new AjaxResponse();
        $response->addCommand(new CloseModalDialogCommand());

        //Because we are deleting the node we are on we need to redirect to the person node
        $redirect_url = '/node/'.$member_id;
        $response->addCommand(new RedirectCommand($redirect_url));
        $form_state->setResponse($response);
        return $response;
      }
      else {
        //return RedirectResponse
      }
    }
    if ($triggering_element['#name'] == 'close') {
      $response = new AjaxResponse();
      $response->addCommand(new CloseModalDialogCommand());
      $form_state->setResponse($response);
      return $form;
    }
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   * *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['config.webform_cancel_model_form'];
  }

}
