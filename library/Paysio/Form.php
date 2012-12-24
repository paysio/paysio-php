<?php

namespace Paysio;

class Form
{
    /**
     * form element id
     * @var string
     */
    protected $_formId;
    /**
     * from init params
     * @var
     */
    protected $_params;
    /**
     * from values
     * @var
     */
    protected $_values;
    /**
     * form params error
     * @var
     */
    protected $_errors;
    /**
     * @var Api
     */
    protected $_api;

    /**
     * @param $formId
     * @param Api $api
     */
    public function __construct($formId, Api $api = null)
    {
        $this->_formId = $formId;
        $this->_api = $api;
    }

    /**
     * @param array $attributes
     * @param bool $withJQuery
     * @param bool $return
     * @return string
     */
    public function render(array $attributes = array(), $withJQuery = true, $return = false)
    {
        $script = $this->renderHead($withJQuery, true);
        $script .= $this->renderFrom($attributes, true);
        $script .= $this->renderJs(true);

        if ($return) {
            return $script;
        } else {
            echo $script;
        }
    }

    /**
     * @param array $attributes
     * @param bool $return
     * @return string
     */
    public function renderFrom(array $attributes = array(), $return = false)
    {
        $defaultAttributes = array(
            'action' => '',
            'method' => 'POST',
        );
        $attributes = $defaultAttributes + $attributes;
        $attributes['id'] = $this->_formId;

        $attributesString = '';
        foreach ($attributes as $key => $val) {
            $attributesString .=  " $key=\"$val\"";
        }
        $script = '<form' . $attributesString . '></form>' . "\n";

        if ($return) {
            return $script;
        } else {
            echo $script;
        }
    }

    /**
     * @param bool $withJQuery
     * @param bool $return
     * @return string
     */
    public function renderHead($withJQuery = true, $return = false)
    {
        $script = '<link href="' . $this->_getApi()->getStaticUrl() . '/paysio.css" type="text/css" rel="styleSheet" />' . "\n";
        if ($withJQuery) {
            $script .= '<script src="https://yandex.st/jquery/1.8.1/jquery.min.js"></script>' . "\n";
        }
        $script .= '<script src="' . $this->_getApi()->getStaticUrl() . '/paysio.js"></script>' . "\n";

        if ($return) {
            return $script;
        } else {
            echo $script;
        }
    }

    /**
     * @param bool $return
     * @return string
     */
    public function renderJs($return = false)
    {
        $script = "<script type=\"text/javascript\">\n";
        $script .= "Paysio.setEndpoint('" . $this->_getApi()->getEndpoint() . "');\n";
        $script .= "Paysio.setPublishableKey('" . $this->_getApi()->getPublishableKey() . "');\n";
        $script .= "Paysio.form.build($('#{$this->_formId}'), " . json_encode($this->_params ?: new \stdClass());
        $script .= ', ' . json_encode($this->_values ?: new \stdClass());
        if ($this->_errors) {
            $script .= ', ' . json_encode($this->_errors);
        }
        $script .= ");\n";
        $script .= '</script>';

        if ($return) {
            return $script;
        } else {
            echo $script;
        }
    }

    /**
     * @param array|\stdClass $params
     * @return Form
     */
    public function setParams($params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * @param array|\stdClass $params
     * @return Form
     */
    public function addParams($params)
    {
        $this->_params += $params;
        return $this;
    }

    /**
     * @param array|\stdClass $values
     * @return Form
     */
    public function setValues($values)
    {
        $this->_values = $values;
        return $this;
    }

    /**
     * @param array $errors
     * @return Form
     */
    public function setErrors(array $errors)
    {
        $this->_errors = $errors;
        return $this;
    }

    /**
     * @return Api
     */
    protected function _getApi()
    {
        if ($this->_api === null) {
            $this->_api = Api::getInstance();
        }
        return $this->_api;
    }
}