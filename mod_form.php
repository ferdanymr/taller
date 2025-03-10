<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     mod_pairs
 * @copyright   2021 Fernando Munoz <fernando_munoz@cuaieed.unam.mx>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->libdir . '/filelib.php');

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     mod_pairs
 * @copyright   2021 Fernando Munoz <fernando_munoz@cuaieed.unam.mx>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class mod_pairs_mod_form extends moodleform_mod {

    protected $course = null;

    /**
     * Constructor
     */
    public function __construct($current, $section, $cm, $course) {
        $this->course = $course;
        parent::__construct($current, $section, $cm, $course);
    }

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('pairsname', 'mod_pairs'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        //introduccion o descripcion
        $this->standard_intro_elements('Descripcion');
        
        //añadimos cabecera de Ajustes Calificación
        $mform->addElement('header', 'ajustescalif', get_string('ajustescalif', 'mod_pairs'));
        
        //Añadimos etiqueta de estatedia y etiqueta de rubrica
        $mform->addElement('static', 'label1', get_string('estrategia','mod_pairs'), get_string('rub', 'mod_pairs'));
        $mform->addHelpButton('label1', 'estrategia', 'mod_pairs');
        
        $categorias = grade_get_categories_menu($this->course->id);
        $mform->addElement('select', 'categoria', get_string('categoria','mod_pairs'), $categorias);

        //listado del 100 al 0
        $opciones = array();
        for($i = 100; $i >= 0; $i--){
            $opciones[$i] = $i;
        }

        $select = $mform->addElement('select', 'attachment', get_string('calif_env', 'mod_pairs'), $opciones);
        $select->setSelected('80');
        $mform->addHelpButton('attachment', 'calif_env', 'mod_pairs');

        $mform->addElement('float', 'calif_aprobatoria', get_string('calif_aprob', 'mod_pairs'));
        $mform->addHelpButton('calif_aprobatoria', 'calif_aprob', 'mod_pairs');
        $mform->addRule('calif_aprobatoria', 'la rating aprobatoria solo es númerica', 'numeric', null, 'client');

        $select1 = $mform->addElement('select', 'assessment', get_string('calif_val', 'mod_pairs'), $opciones);
        $select1->setSelected('20');
        $mform->addHelpButton('assessment', 'calif_val', 'mod_pairs');

        $mform->addElement('float', 'calif_aprob_valoracion', get_string('calif_aprob_val', 'mod_pairs'));
        $mform->addHelpButton('calif_aprob_valoracion', 'calif_aprob_val', 'mod_pairs');
        $mform->addRule('calif_aprob_valoracion', 'la rating aprobatoria de valoración es solo númerica', 'numeric', null, 'client');
        
        $opciones = array();
        for($i = 5; $i >= 0; $i--){
            $opciones[$i] = $i;
        }

        $select2 = $mform->addElement('select', 'no_decimals', get_string('no_decimals', 'mod_pairs'), $opciones);
        $select2->setSelected('0');
        
        //parametros de envio ---------------------
        $mform->addElement('header', 'param_env', get_string('param_env', 'mod_pairs'));
        //editor de instrucciones de envio
        $mform->addElement('editor', 'instruction_attachment', get_string('param_inst', 'mod_pairs'));

        //Configuracion para admitir mas de un solo archivo
        $options = array();
        for ($i = 1; $i >= 1; $i--) {
            $options[$i] = $i;
        }

        $mform->addElement('hidden', 'no_attachments','1');
        //$select2 = $mform->addElement('select', 'no_attachments', get_string('param_max', 'mod_pairs'), $options);
        //$select2->setSelected('1');

        $mform->addElement('filetypes', 'type_attachments', get_string('param_type_arch', 'mod_pairs'));
        $mform->addHelpButton('type_attachments', 'param_type_arch', 'mod_pairs');

        $options = get_max_upload_sizes($CFG->maxbytes, $this->course->maxbytes);
        $mform->addElement('select', 'max_size', get_string('param_max_size', 'mod_pairs'), $options);
        
        //Configuración de la valoración---------------------------------------
        $mform->addElement('header', 'conf_val', get_string('conf_val', 'mod_pairs'));
        
        $mform->addElement('editor', 'instruction_assessment', get_string('conf_val_inst', 'mod_pairs'));


        $options = array();
        for ($i = 10; $i >= 1; $i--) {
            $options[$i] = $i;
        }

        $select2 = $mform->addElement('select', 'no_revisions', get_string('no_revisions', 'mod_pairs'), $options);
        $select2->setSelected('3');
        $mform->addHelpButton('no_revisions', 'no_revisions', 'mod_pairs');

        //Retroalimentación---------------------------------------
        $mform->addElement('header', 'retro', get_string('retro', 'mod_pairs'));
        
        $mform->addElement('editor', 'retro_conclusion', get_string('retro_con', 'mod_pairs'));
        
        $coursecontext = context_course::instance($this->course->id);

        // To be removed (deprecated) with MDL-67526.
        plagiarism_get_form_elements_module($mform, $coursecontext, 'mod_pairs');

        // Common module settings, Restrict availability, Activity completion etc. ----
        $features = array('groups' => true, 'groupings' => true,
                'outcomes' => true, 'gradecat' => false, 'idnumber' => true);

        $this->standard_coursemodule_elements();

        // Standard buttons, common to all modules ------------------------------------
        $this->add_action_buttons();
    }

    /**
     * Prepares the form before data are set
     *
     * Additional wysiwyg editor are prepared here, the introeditor is prepared automatically by core.
     * Grade items are set here because the core modedit supports single grade item only.
     *
     * @param array $data to be set
     * @return void
     */
    public function data_preprocessing(&$data) {
        if ($this->current->instance) {
            $editor = array('text'=>$data['instruction_attachment'], 'format'=>$data['instruction_attachmentformat']);
            $data['instruction_attachment'] = $editor;
            
            $editor = array('text'=>$data['instruction_assessment'], 'format'=>$data['instruction_assessmentformat']);
            $data['instruction_assessment'] = $editor;

            $editor = array('text'=>$data['retro_conclusion'], 'format'=>$data['retro_conclusionformat']);
            $data['retro_conclusion'] = $editor;
        }
    }

    /**
     * Set the grade item categories when editing an instance
     */
    public function definition_after_data() {

        $mform =& $this->_form;

        if ($id = $mform->getElementValue('update')) {
            $instance   = $mform->getElementValue('instance');

            $gradeitems = grade_item::fetch_all(array(
                'itemtype'      => 'mod',
                'itemmodule'    => 'pairs',
                'iteminstance'  => $instance,
                'courseid'      => $this->course->id));
            
            $gradeitem = current($gradeitems);
            if (!empty($gradeitems)) {

                    $element = $mform->getElement('categoria');
                    $element->setValue($gradeitem->categoryid);

            }
        }

        parent::definition_after_data();
    }
}
