<?php

namespace Modules\Common\Services;

use Modules\Core\Services\BaseService;

class VariablesService extends BaseService
{

    public function __construct() {
        parent::__construct();
    }

    /**
     * @param string $documentTemplateContent
     * @param array $variables
     *
     * @return string
     */
    public function replaceVariables(string $documentTemplateContent, array $variables)
    {
        $data = array();

        foreach ($variables as $key => $variable) {
            if (is_array($variable)) {
                foreach ($variable as $keySub => $variablesub) {
                    if (is_array($variablesub)) {
                        foreach ($variablesub as $keySubInner => $variableSubInner) {
                            $data['{' . $key . '.' . $keySub . '.' . $keySubInner . '}'] = $variableSubInner;
                        }
                    }
                    $data['{' . $key . '.' . $keySub . '}'] = $variablesub;
                }
            }
            $data['{' . $key . '}'] = $variable;
        }

        return strtr($documentTemplateContent, $data);
    }

}
