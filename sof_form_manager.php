<?php
    require_once(dirname(__FILE__).'/classes/Form.class.php');
    $form = new Form();
    wp_enqueue_script( 'jquery', plugins_url('/js/jquery.js', __FILE__) );
    wp_enqueue_script( 'global', plugins_url('/js/global.js', __FILE__) );
    wp_enqueue_script( 'form_submission', plugins_url('/js/form_submission.js', __FILE__) );
    wp_enqueue_style( 'form_buildercss', plugins_url('/css/form_builder.css', __FILE__) );
?>
    <div id="notify"></div>
    <div id="open_form">
      <div id=left_of">
        <div id="forms">
        <? $form->displayForms(); ?>     
        </div>
      </div>
      <div id="right_of">
        <div id="form"></div>
        <div id="form_submissions_header">Submissions</div>
        <div id="form_submissions"></div>
        <div id="form_submission_header">Submission</div>
        <div id="form_submission"></div>
      </div>
      <div id="cleaner"></div>
    </div>
