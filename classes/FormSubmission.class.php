<?php
require_once(dirname(__FILE__).'/UsefulDb/ORM.php');
require_once(dirname(__FILE__).'/Form.class.php');
require_once(dirname(__FILE__).'/FormInput.class.php');

class FormSubmission {

    private $db, $form_input, $form, $form_id, $last_submission_id, $table;

    public function __construct($form_id = null) {
        $this->db = new ORM(DB_NAME,DB_HOST,DB_USER,DB_PASSWORD);

        $this->form       = new Form();
        $this->form_input = new FormInput();

        $plugin_bridge = new PluginBridge();
        $this->table = $plugin_bridge->getTablePrefix('open_form_submissions');

        if(!empty($form_id))
            $this->form_id = $form_id;
    }

    public function setFormId($form_id) {
        $this->form_id = $form_id;
    }

    public function submitForm($request) {

        $form_input = $request['form_input'];

        if(!is_array($form_input))
            parse_str($form_input,$submission);

        //form id must be the last element
        $form_id = array_pop($submission);
 
        $this->insertSubmission($form_id,$submission);

        $this->form_id = $form_id;

        $this->wp_submission_response();

        //$this->getSubmission($this->last_submission_id);
    }

    private function insertSubmission($form_id,$submission) {

        $submission = $this->mergeAssocLabels($form_id,$submission);

        $submission = base64_encode(serialize($submission));

        $this->db->execute("
                            INSERT INTO ".$this->table."(`id`,`form_id`,`submission`,`datetime`) 
                            VALUES (NULL,".$form_id.",'".$submission."','".time()."')
                          ");

        $this->last_submission_id = $this->db->lastInsertedId();
    }

    private function wp_submission_response() {
        $this->form->setForm($this->form_id);

        $wp_post_id = $this->form->getForm('wp_post_id');

        if(!empty($wp_post_id)) {
            echo json_encode(array('type'=>'redirect','value'=>get_page_link($wp_post_id)));
        }
        else {
            $db_succ_msg = $this->form->getForm('success_msg');
            $def_succ_msg = "Form submission successful";
            $succ_msg = (empty($db_succ_msg)) ? $def_succ_msg : $db_succ_msg;
            $succ_msg = $this->form_input->wrapInput($succ_msg,'success');   
            echo json_encode(array('type'=>'flash','value'=>$succ_msg));
        }
    }

    private function getSubmission($id) {
        $submission = $this->db->findOneBy($this->table,"*",'id ='.$id);

        $submission['submission'] = unserialize(base64_decode($submission['submission']));

        return $submission;
    }

    private function mergeAssocLabels($form_id,$submission) {
        $form = $this->form->getFormById($form_id);

        //get the labels
        foreach($form['inputs'] as $input) {
     
            foreach($input as $name=>$attrs) {
                $inputs[$name]['label'] = $attrs['label'];
            }

            foreach($submission as $k=>$v) {
                $new_submission[$k]['label'] = $inputs[$k]['label'];
                    
                $new_submission[$k]['value'] = $v;
            }
        }

        return $new_submission;
    }

    private function getSubmissionsByFormId($id) {
        return $this->db->findBy($this->table,'*','form_id ='.$id);
    }
   
    public function countSubmissionsByFormId($id) {
        return $this->db->findOneBy($this->table,'COUNT(*)','form_id ='.$id);
    }
 
    private function getSubmissions() {
        return $this->db->find($this->table);
    }

    public function displaySubmissions($form_id) {

        $submissions = $this->getSubmissionsByFormId($form_id);

        foreach((array)$submissions as $submission) {

            $fs_row = '<div class="view_submission" data-submission-id="'.$submission['id'].'">'.$this->toString($submission).'</div>';

            echo $this->form_input->wrapInput($fs_row,'fs_row');
        }

    }
    
    public function displaySubmission($id) {

        $submission = $this->getSubmission($id);

        $this->form->setForm($form_id);

        $form_name = $this->form->getForm('name');

        echo $this->form_input->wrapInput($form_name,'form_name');

        foreach((array)$submission['submission'] as $input) {
            $input_row = null;
            $input_row .= $this->form_input->wrapInput($input['label'],'input_label');
            $input_row .= $this->form_input->wrapInput($input['value'],'input_value');
            echo $this->form_input->wrapInput($input_row,'input_row');
        }
    }

    private function toString($submission,$field = 'datetime') {
        switch($field) {
            case "datetime":
                $tostring = date('M d, Y h:i a',$submission[$field]);
            break;
        }

        return $tostring;
    }
}
