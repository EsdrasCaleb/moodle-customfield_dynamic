<?php
namespace mod_coursecertificate\form;

defined('MOODLE_INTERNAL') || die;


use customfield_dynamic\field_controller;
use external_function_parameters;
use external_value;
use external_multiple_structure;
use external_single_structure;

require_once($CFG->dirroot . '/lib/externallib.php');

class form_template_options extends \external_api {


    /**
     * Define os parâmetros esperados pela função externa.
     *
     * @return external_function_parameters
     */
    public static function get_options_parameters() {
        return new external_function_parameters([
            'instance' => new external_value(PARAM_INT, 'Instancia'),
            'search' => new external_value(PARAM_RAW, 'Termo de busca', VALUE_DEFAULT,null),
        ]);
    }


    /**
     * Função que será chamada externamente para buscar as opções de templates.
     *
     * @param string $search O termo de busca.
     * @param array $options Parâmetros adicionais.
     * @return array
     */
    public static function get_options($instance,$search) {
        global $COURSE;

        $handler = \core_customfield\handler::get_handler('core_course', 'course');
        $field = \core_customfield\field_controller::create($instance, $handler);
        // Obter o contexto do curso
        $context = $field->get_handler()->get_configuration_context();
        $options = field_controller::get_options_array($field,$field->get_configdata_property('multiselect'));
        // Filtrar os templates com base no termo de busca
        $count = 0;
        $result = [];
        foreach ($options as $key => $option) {
            // Multilang formatting with filters.
            $name = format_string($option, true, ['context' => $context]);
            if (stripos($name, $search) !== false) {
                $result[] = [
                    'value' => $key,
                    'label' => $name,
                ];
                $count++;
                if ($count >= 10) {
                    break;
                }
            }
        }


        return $result;
    }

    /**
     * Define o tipo de retorno da função externa.
     *
     * @return external_multiple_structure
     */
    public static function get_options_returns() {
        return new external_multiple_structure(new external_single_structure([
            'value' => new external_value(PARAM_INT, 'Id of template'),
            'label' => new external_value(PARAM_RAW, 'The name of template'),
        ]));
    }

}
