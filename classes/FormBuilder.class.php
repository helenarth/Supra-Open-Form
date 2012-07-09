<?php 
require_once(dirname(__FILE__).'/FormInput.class.php');

class FormBuilder {

    private $form_input;

    function __construct() {
        $this->form_input = new FormInput();
    }

    function inputBuilder($params,$edit=false) {

        $input_type = $params['input_type'];

        switch($input_type) {

            case "combobox":
            break;
            case "radiogroup":
            break;
            case "select":
                $form_input = $this->renderSelectBuilder();
            break;
            case "attribute":
                $form_input = $this->renderAttributeAdder();
            break;
            default:
                $form_input = $this->renderRegInputBuilder($input_type);
        }

        //echo "<pre>";
        //print_r($form_input);
        //print_r($params);
        //echo "</pre>";

        if($edit)
            $form_input = $this->custom_merge($form_input,$params);

        //echo "<pre>";
        //print_r($_SESSION);
        //print_r($form_input);
        //echo "</pre>";
        
        if($input_type == "attribute"){
            $form_input = $this->form_input->render($form_input);
            echo $this->form_input->wrapInput($form_input,'attribute');
        } 
        else {
            $this->form_input->renderAndDisplay($form_input);
        }
    }

    function custom_merge($form_inputs,$defaults) {

        foreach($form_inputs as $k=>$v) {
            if(!count($defaults[$k]))
                continue; 
            $array[$k] = array_merge($v,$defaults[$k]);
        }

        return array_merge($form_inputs,$array);
    }

    function renderSelectBuilder() {


        $form_input['name'] = array(
                                    'type'=>'text',
                                    'label'=>'Name'
                                   );

        $form_input['label'] = array(
                                     'type'=>'text',
                                     'label'=>'Label'
                                    );

        $form_input['add_empty'] = array(
                                         'type'=>'checkbox',
                                         'label'=>'Add Empty?',
                                         'value'=>true
                                        );

        $form_input['choices'] = array(
                                     'type'=>'text',
                                     'label'=>'Choices',
                                     'help'=>'provide comma seperated values'
                                    );

        return $form_input;
    }

    function renderRegInputBuilder($input_type) {

        
        $form_input['name'] = array(
                                    'type'=>'text',
                                    'label'=>'Name'
                                   );

        $form_input['value'] = array(
                                     'type'=>'text',
                                     'label'=>'Value'
                                    );


        if(!in_array($input_type,array('submit','hidden')))
            $form_input['label'] = array(
                                         'type'=>'text',
                                         'label'=>'Label'
                                        );

        return $form_input;
    }

    function renderAttributeAdder() {

        $form_input['input_attr_key'] = array(
                                          'type'=>'text',
                                          'label'=>'Attribute Key',
                                          'arrayable'=>true
                                         );

        $form_input['input_attr_val'] = array(
                                          'type'=>'text',
                                          'label'=>'Attribute Value',
                                          'arrayable'=>true
                                         );

        return $form_input;
    }
}
