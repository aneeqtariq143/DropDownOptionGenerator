<?php

namespace App\Utilities;

/**
 * Description of DropDownOptionsGenerator
 * This class Help To Generate DropDown Options
 * @author Aneeq
 */
class DropDownOptionsGenerator {
    protected $config = [
        'optgroup' => [
            'label' => '', // Required
            'data_attributes' => [],
            'condition' => [
                'AND' => [],
                'OR' => [],
            ],
        ],
        'option' => [
            'default' => [
                'label' => 'Select',
                'value' => ''
            ],
            'label' => 'column_name', // Required
            'value' => 'column_name', // Required
            'selected' => '',
            'exclude' => [
                'AND' => [],
                'OR' => [],
            ],
            'data_attributes' => []
        ]
    ];
    protected $optgroup_format = '<optgroup label="%s">%s</optgroup>';
    protected $option_format = '<option value="%s" %s %s>%s</option>';
    protected $condition_wrapper = '%s(%s)';
    protected $html_output;

    public function __construct(array $config) {
        if (!is_array($config) && !empty($config)) {
            throw new \InvalidArgumentException('Configurations Must Be Array');
        }

        foreach ($this->config as $key => $value) {
            if (key_exists($key, $config)) {
                $method_name = 'setConfig' . ucwords($key);
                $this->$method_name($config[$key]);
            }
        }
    }

    public function setConfigOptgroup($config) {
        if (!key_exists('label', $config)) {
            throw new \InvalidArgumentException('"Label" Option is Missing under Config optgroup Key. Set Column Name used in Label ');
        }

        if (!key_exists('condition', $config)) {
            $config['condition'] = $this->config['optgroup']['condition'];
        }


        $this->config['optgroup'] = $config;
    }

    public function setConfigOption($config) {
        if (!key_exists('label', $config)) {
            throw new \InvalidArgumentException('"Label" Option is Missing under Config option Key. Set Column Name used in Label ');
        }

        if (!key_exists('value', $config)) {
            throw new \InvalidArgumentException('"Value" Option is Missing under Config option Key. Set Column Name used in Value');
        }

        if (!key_exists('exclude', $config)) {
            $config['exclude'] = $this->config['option']['exclude'];
        }

        if (!key_exists('data_attributes', $config)) {
            $config['data_attributes'] = $this->config['option']['data_attributes'];
        }

        if (!key_exists('default', $config)) {
            $config['default'] = $this->config['option']['default'];
        }

        if (!key_exists('selected', $config)) {
            $config['selected'] = $this->config['option']['selected'];
        }

        $this->config['option'] = $config;
    }

    private function isAssociativeArray(array $array) {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    public function generateMarkup($data) {
        if (gettype($data) == 'array') {
            $this->generateMarkupFromArray($data);
        }

        if (gettype($data) == 'object') {
            $this->generateMarkupFromObject($data);
        }

        $default = $this->config['option']['default'];
        $default_option_html = sprintf($this->option_format, $default['value'], '', '', $default['label']);
        return $default_option_html . $this->html_output;
    }

    public function generateMarkupFromObject($data) {
        if (!gettype($data) == 'object') {
            throw new \InvalidArgumentException('Data Must be In Object of Array Format');
        }

        if (isset($this->config['optgroup']) && !empty($this->config['optgroup']['label'])) {
            $this->html_output = $this->generateOptionGroup($data);
        } else {
            $this->html_output = $this->generateOptions($data);
        }
    }

    public function generateMarkupFromArray($data) {
        if (!gettype($data) == 'array') {
            throw new \InvalidArgumentException('Data Must be In Array Format');
        }
    }

    protected function generateOptionGroup($data) {
        $output = '';
        $data_attribute_string = '';
        $label = $this->config['optgroup']['label'];
        $condition_config = $this->config['optgroup']['condition'];
        $condition = $this->conditionBuilder($condition_config);

        foreach ($data as $row) {

            if (empty($condition)) {
                if (!empty($row->childrens)) {
                    $options = $this->generateOptions($row->childrens);
                }

                $output .= sprintf($this->optgroup_format, $row->$label, $options);
            } else {
                if (eval($condition)) {
                    if (!empty($row->childrens)) {
                        $options = $this->generateOptions($row->childrens);
                    }

                    $output .= sprintf($this->optgroup_format, $row->$label, $options);
                }
            }
        }

        return $output;
    }

    protected function generateOptions($data) {
        $output = '';
        $label = $this->config['option']['label'];
        $value = $this->config['option']['value'];
        $selected = $this->config['option']['selected'];
        $selected_string = '';
        $data_attributes = $this->config['option']['data_attributes'];
        $exclude_config = $this->config['option']['exclude'];
        $exclude_condition = $this->conditionBuilder($exclude_config);

        foreach ($data as $key => $row) {
            $selected_string = '';
            $data_attribute_string = '';

            if (!empty($data_attributes)) {
                foreach ($data_attributes as $data_attribute) {
                    $temp_data_attribute_value = $row->$data_attribute;
                    $temp_data_attribute = str_replace('_', '-', $data_attribute);
                    $data_attribute_string .= "data-$temp_data_attribute='$temp_data_attribute_value' ";
                }
            }

            if (!empty($selected) && $row->$value == $selected) {
                $selected_string = "selected='selected'";
            }
            
            if (empty($exclude_condition)) {
                $output .= sprintf($this->option_format, $row->$value, $selected_string, $data_attribute_string, $row->$label);
            } else {
                if (eval($exclude_condition)) {
                    $output .= sprintf($this->option_format, $row->$value, $selected_string, $data_attribute_string, $row->$label);
                }
            }

            if (!empty($row->childrens)) {
                $output .= $this->generateOptions($row->childrens);
            }
        }
        return $output;
    }

    protected function conditionBuilder($config = '', $raw_condition = FALSE) {
        $exclude_condition = '';
        if (!empty($config) && (!empty($config['AND'] || !empty($config['OR'])))) {
            foreach ($config as $type => $type_config) {
                switch ($type) {
                    case 'AND': {
                            $this->formatConditionString($type_config, $exclude_condition, '&&');
                            break;
                        }
                    case 'OR': {
                            $this->formatConditionString($type_config, $exclude_condition, '||');
                            break;
                        }
                }
            }

            if ($raw_condition) {
                $exclude_condition = $exclude_condition;
            } else {
                $exclude_condition = "return $exclude_condition;";
            }
        }
        return $exclude_condition;
    }

    protected function formatConditionString($type_config, &$exclude_condition, $type) {
        if (isset($type_config['composite'])) {
            if (isset($type_config['composite']['prefix'])) {
                $condition_prefix = $type_config['composite']['prefix'];
                unset($type_config['composite']['prefix']);
            } else {
                $condition_prefix = '';
            }

            foreach ($type_config['composite'] as $composite_config) {
                if (isset($composite_config['prefix'])) {
                    $composite_condition_prefix = $composite_config['prefix'];
                    unset($composite_config['prefix']);
                } else {
                    $composite_condition_prefix = '';
                }
                $temp_condition = '';
                foreach ($composite_config as $single_config) {
                    $temp_condition .= (empty($temp_condition)) ? '$row->' . $single_config['column_name'] . " $single_config[operator] " . ' "' . $single_config['value'] . '"' : ' ' . $type . ' $row->' . $single_config['column_name'] . " $single_config[operator] " . ' "' . $single_config['value'] . '"';
                }

//                if (count($single_config) > 1) {
                $temp_condition = sprintf($this->condition_wrapper, $composite_condition_prefix, $temp_condition);
//                }

                $exclude_condition .= (empty($exclude_condition)) ? $temp_condition : ' ' . $type . ' ' . $temp_condition;
            }

//            if (count($type_config['composite']) > 1) {
            $exclude_condition = sprintf($this->condition_wrapper, $condition_prefix, $exclude_condition);
//            }
        } else {
            if (isset($type_config['prefix'])) {
                $condition_prefix = $type_config['prefix'];
                unset($type_config['prefix']);
            } else {
                $condition_prefix = '';
            }

            foreach ($type_config as $single_config) {
                $exclude_condition .= (empty($exclude_condition)) ? '$row->' . $single_config['column_name'] . " $single_config[operator] " . ' "' . $single_config['value'] . '"' : ' ' . $type . ' $row->' . $single_config['column_name'] . " $single_config[operator] " . ' "' . $single_config['value'] . '"';
            }

//            if (count($type_config) > 1) {
            $exclude_condition = sprintf($this->condition_wrapper, $condition_prefix, $exclude_condition);
//            }
        }
    }

    public function conditionDump($config_option = 'both') {
        $output = [];
        switch ($config_option) {
            case 'both': {
                    $condition_config = $this->config['optgroup']['condition'];
                    $output['condition'] = $this->conditionBuilder($condition_config, TRUE);

                    $exclude_config = $this->config['option']['exclude'];
                    $output['exclude'] = $this->conditionBuilder($exclude_config, TRUE);
                    break;
                }
            case 'optgroup': {
                    $condition_config = $this->config['optgroup']['condition'];
                    $output['condition'] = $this->conditionBuilder($condition_config, TRUE);
                    break;
                }
            case 'option': {
                    $exclude_config = $this->config['option']['exclude'];
                    $output['exclude'] = $this->conditionBuilder($exclude_config, TRUE);
                    break;
                }
        }
        return $output;
    }

}
