<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Legacy style form renderer
 *
 * @category  XoopsForm
 * @package   XoopsFormRendererBootstrap3
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2017 XOOPS Project (http://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class FormRendererBootstrap3 implements XoopsFormRendererInterface
{

    /**
     * Render support for XoopsFormButton
     *
     * @param XoopsFormButton $element form element
     *
     * @return string rendered form element
     */
    public function renderFormButton(XoopsFormButton $element)
    {
        $class = 'btn btn-default';
        $class .= ($element->getClass()) ? ' ' . $element->getClass() : '';
        $ret
            = '<input '
              . "type='{$element->getType()}' "
              . "class='{$class}' "
              . "name='{$element->getName()}' "
              . "id='{$element->getName()}' "
              . "value='{$element->getValue()}' "
              . "title='{$element->getValue()}'"
              . "{$element->getExtra()} />";
        return $ret;
    }

    /**
     * Render support for XoopsFormButtonTray
     *
     * @param XoopsFormButtonTray $element form element
     *
     * @return string rendered form element
     */
    public function renderFormButtonTray(XoopsFormButtonTray $element)
    {
        $ret = '';
        if ($element->_showDelete) {
            $ret
                .= '<input '
                . 'type="submit" '
                . 'class="btn btn-danger" '
                . 'name="delete" '
                . 'id="delete" '
                . 'value="' . _DELETE . '" '
                . 'onclick="this.form.elements.op.value=\'delete\'">';
            $ret .= '&nbsp;';
        }
        $ret
            .= '<input '
            . 'type="button" '
            . 'class="btn btn-danger" '
            . 'value="' . _CANCEL . '" '
            . 'onClick="history.go(-1);return true;" />';
        $ret .= '&nbsp;';
        $ret
            .= '<input '
            . 'type="reset" '
            . 'class="btn btn-warning" '
            . 'name="reset" '
            . 'id="reset" '
            . 'value="' . _RESET . '" />';
        $ret .= '&nbsp;';
        $ret
            .= '<input '
            . 'type="' . $element->getType() . '" '
            . 'class="btn btn-success"  '
            . 'name="' . $element->getName() . '"  '
            . 'id="' . $element->getName() . '" '
            . 'value="' . $element->getValue() . '"'
            . $element->getExtra()
            . '  />';

        return $ret;
    }

    /**
     * Render support for XoopsFormCheckBox
     *
     * @param XoopsFormCheckBox $element form element
     *
     * @return string rendered form element
     */
    public function renderFormCheckBox(XoopsFormCheckBox $element)
    {
        $elementName = $element->getName();
        $elementId = $elementName;
        $elementOptions = $element->getOptions();
        if (count($elementOptions) > 1 && substr($elementName, -2, 2) !== '[]') {
            $elementName .= '[]';
            $element->setName($elementName);
        }

        switch ((int) ($element->columns)) {
            case 0:
                return $this->renderCheckedInline($element, 'checkbox', $elementId, $elementName);
            case 1:
                return $this->renderCheckedOneColumn($element, 'checkbox', $elementId, $elementName);
            default:
                return $this->renderCheckedColumnar($element, 'checkbox', $elementId, $elementName);
        }
    }

    /**
     * Render a inline checkbox or radio element
     *
     * @param XoopsFormCheckBox|XoopsFormRadio $element element being rendered
     * @param string                           $type    'checkbox' or 'radio;
     * @param string                           $elementId   input 'id' attribute of element
     * @param string                           $elementName input 'name' attribute of element
     * @return string
     */
    protected function renderCheckedInline($element, $type, $elementId, $elementName)
    {
        $class = $type . '-inline';
        $ret = '';

        $idSuffix = 0;
        $elementValue = $element->getValue();
        $elementOptions = $element->getOptions();
        foreach ($elementOptions as $value => $name) {
            ++$idSuffix;
            $ret .= '<label class="' . $class . '">';
            $ret .= "<input type='" . $type . "' name='{$elementName}' id='{$elementId}{$idSuffix}' title='"
                . htmlspecialchars(strip_tags($name), ENT_QUOTES) . "' value='"
                . htmlspecialchars($value, ENT_QUOTES) . "'";

            if (is_array($elementValue) ? in_array($value, $elementValue): $value == $elementValue) {
                $ret .= ' checked';
            }
            $ret .= $element->getExtra() . ' />' . $name . $element->getDelimeter();
            $ret .= '</label>';
        }

        return $ret;
    }

    /**
     * Render a single column checkbox or radio element
     *
     * @param XoopsFormCheckBox|XoopsFormRadio $element element being rendered
     * @param string                           $type    'checkbox' or 'radio;
     * @param string                           $elementId   input 'id' attribute of element
     * @param string                           $elementName input 'name' attribute of element
     * @return string
     */
    protected function renderCheckedOneColumn($element, $type, $elementId, $elementName)
    {
        $class = $type;
        $ret = '';

        $idSuffix = 0;
        $elementValue = $element->getValue();
        $elementOptions = $element->getOptions();
        foreach ($elementOptions as $value => $name) {
            ++$idSuffix;
            $ret .= '<div class="' . $class . '">';
            $ret .= '<label>';
            $ret .= "<input type='" . $type . "' name='{$elementName}' id='{$elementId}{$idSuffix}' title='"
                . htmlspecialchars(strip_tags($name), ENT_QUOTES) . "' value='"
                . htmlspecialchars($value, ENT_QUOTES) . "'";

            if (is_array($elementValue) ? in_array($value, $elementValue): $value == $elementValue) {
                $ret .= ' checked';
            }
            $ret .= $element->getExtra() . ' />' . $name . $element->getDelimeter();
            $ret .= '</label>';
            $ret .= '</div>';
        }

        return $ret;
    }

    /**
     * Render a multicolumn checkbox or radio element
     *
     * @param XoopsFormCheckBox|XoopsFormRadio $element element being rendered
     * @param string                           $type    'checkbox' or 'radio;
     * @param string                           $elementId   input 'id' attribute of element
     * @param string                           $elementName input 'name' attribute of element
     * @return string
     */
    protected function renderCheckedColumnar($element, $type, $elementId, $elementName)
    {
        $class = $type;
        $ret = '';

        $idSuffix = 0;
        $elementValue = $element->getValue();
        $elementOptions = $element->getOptions();
        foreach ($elementOptions as $value => $name) {
            ++$idSuffix;
            $ret .= '<div class="' . $class . ' col-md-2">';
            $ret .= '<label>';
            $ret .= "<input type='" . $type . "' name='{$elementName}' id='{$elementId}{$idSuffix}' title='"
                . htmlspecialchars(strip_tags($name), ENT_QUOTES) . "' value='"
                . htmlspecialchars($value, ENT_QUOTES) . "'";

            if (is_array($elementValue) ? in_array($value, $elementValue): $value == $elementValue) {
                $ret .= ' checked';
            }
            $ret .= $element->getExtra() . ' />' . $name . $element->getDelimeter();
            $ret .= '</label>';
            $ret .= '</div>';
        }

        return $ret;
    }
    /**
     * Render support for XoopsFormColorPicker
     *
     * @param XoopsFormColorPicker $element form element
     *
     * @return string rendered form element
     */
    public function renderFormColorPicker(XoopsFormColorPicker $element)
    {
        if (isset($GLOBALS['xoTheme'])) {
            $GLOBALS['xoTheme']->addScript('include/spectrum.js');
            $GLOBALS['xoTheme']->addStylesheet('include/spectrum.css');
        } else {
            echo '<script type="text/javascript" src="' . XOOPS_URL . '/include/spectrum.js"></script>';
            echo '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/include/spectrum.css">';
        }
        return '<input class="form-control" style="width: 25%;" type="color" name="' . $element->getName()
            . "' title='" . $element->getTitle() . "' id='" . $element->getName()
            . '" size="7" maxlength="7" value="' . $element->getValue() . '"' . $element->getExtra() . ' />';
    }

    /**
     * Render support for XoopsFormDhtmlTextArea
     *
     * @param XoopsFormDhtmlTextArea $element form element
     *
     * @return string rendered form element
     */
    public function renderFormDhtmlTextArea(XoopsFormDhtmlTextArea $element)
    {
        static $js_loaded;

        xoops_loadLanguage('formdhtmltextarea');
        $ret = '';
        // actions
        $ret .= $this->renderFormDhtmlTAXoopsCode($element) . "<br>\n";
        // fonts
        $ret .= $this->renderFormDhtmlTATypography($element);
        // length checker

        $ret .= "<br>\n";
        // the textarea box
        $ret
            .= '<textarea '
               . "class='form-control' "
               . "id='" . $element->getName() . "' "
               . "name='" . $element->getName() . "' "
               . "title='" . $element->getTitle() . "' "
               . "onselect=\"xoopsSavePosition('" . $element->getName() . "');\" "
               . "onclick=\"xoopsSavePosition('" . $element->getName() . "');\" "
               . "onkeyup=\"xoopsSavePosition('" . $element->getName() . "');\" "
               . "cols='" . $element->getCols() . "' "
               . "rows='" . $element->getRows() . "'"
               . $element->getExtra()
               . '>' . $element->getValue() . "</textarea>\n";

        if (empty($element->skipPreview)) {
            if (empty($GLOBALS['xoTheme'])) {
                $element->js .= implode('', file(XOOPS_ROOT_PATH . '/class/textsanitizer/image/image.js'));
            } else {
                $GLOBALS['xoTheme']->addScript(
                    '/class/textsanitizer/image/image.js',
                    array('type' => 'text/javascript')
                );
            }
            $button = "<button type='button' class='btn btn-primary' onclick=\"form_instantPreview('" . XOOPS_URL
                . "', '" . $element->getName() . "','" . XOOPS_URL . "/images', " . (int)$element->doHtml . ", '"
                . $GLOBALS['xoopsSecurity']->createToken() . "')\" title='" . _PREVIEW . "'>" . _PREVIEW . '</button>';

            $ret .= '<br>' . "<div id='" . $element->getName() . "_hidden' style='display: block;'> "
                . '   <fieldset>' . '       <legend>' . $button . '</legend>'
                . "       <div id='" . $element->getName() . "_hidden_data'>" . _XOOPS_FORM_PREVIEW_CONTENT
                . '</div>' . '   </fieldset>' . '</div>';
        }
        // Load javascript
        if (empty($js_loaded)) {
            $javascript = ($element->js ? '<script type="text/javascript">' . $element->js . '</script>' : '')
                . '<script type="text/javascript" src="' . XOOPS_URL . '/include/formdhtmltextarea.js"></script>';
            $ret        = $javascript . $ret;
            $js_loaded  = true;
        }

        return $ret;
    }

    /**
     * Render xoopscode buttons for editor, include calling text sanitizer extensions
     *
     * @param XoopsFormDhtmlTextArea $element form element
     *
     * @return string rendered buttons for xoopscode assistance
     */
    protected function renderFormDhtmlTAXoopsCode(XoopsFormDhtmlTextArea $element)
    {
        $textarea_id = $element->getName();
        $code = '';
        $code .= "<div class='row'><div class='col-md-12'>";
        $code .= "<button type='button' class='btn btn-default btn-sm' onclick='xoopsCodeUrl(\"{$textarea_id}\", \"" . htmlspecialchars(_ENTERURL, ENT_QUOTES) . '", "' . htmlspecialchars(_ENTERWEBTITLE, ENT_QUOTES) . "\");' onmouseover='style.cursor=\"hand\"' title='" . _XOOPS_FORM_ALT_URL . "'><span class='fa fa-fw fa-link' aria-hidden='true'></span></button>";
        $code .= "<button type='button' class='btn btn-default btn-sm' onclick='xoopsCodeEmail(\"{$textarea_id}\", \"" . htmlspecialchars(_ENTEREMAIL, ENT_QUOTES) . '", "' . htmlspecialchars(_ENTERWEBTITLE, ENT_QUOTES) . "\");' onmouseover='style.cursor=\"hand\"' title='" . _XOOPS_FORM_ALT_EMAIL . "'><span class='fa fa-fw fa-envelope-o' aria-hidden='true'></span></button>";
        $code .= "<button type='button' class='btn btn-default btn-sm' onclick='xoopsCodeImg(\"{$textarea_id}\", \"" . htmlspecialchars(_ENTERIMGURL, ENT_QUOTES) . '", "' . htmlspecialchars(_ENTERIMGPOS, ENT_QUOTES) . '", "' . htmlspecialchars(_IMGPOSRORL, ENT_QUOTES) . '", "' . htmlspecialchars(_ERRORIMGPOS, ENT_QUOTES) . '", "' . htmlspecialchars(_XOOPS_FORM_ALT_ENTERWIDTH, ENT_QUOTES) . "\");' onmouseover='style.cursor=\"hand\"' title='" . _XOOPS_FORM_ALT_IMG . "'><span class='fa fa-fw fa-file-image-o' aria-hidden='true'></span></button>";
        $code .= "<button type='button' class='btn btn-default btn-sm' onclick='openWithSelfMain(\"" . XOOPS_URL . "/imagemanager.php?target={$textarea_id}\",\"imgmanager\",400,430);' onmouseover='style.cursor=\"hand\"' title='" . _XOOPS_FORM_ALT_IMAGE . "'><span class='fa fa-file-image-o' aria-hidden='true'></span><small> Manager</small></button>";
        $code .= "<button type='button' class='btn btn-default btn-sm' onclick='openWithSelfMain(\"" . XOOPS_URL . "/misc.php?action=showpopups&amp;type=smilies&amp;target={$textarea_id}\",\"smilies\",300,475);' onmouseover='style.cursor=\"hand\"' title='" . _XOOPS_FORM_ALT_SMILEY . "'><span class='fa fa-fw fa-smile-o' aria-hidden='true'></span></button>";

        $myts = MyTextSanitizer::getInstance();

        $extensions = array_filter($myts->config['extensions']);
        foreach (array_keys($extensions) as $key) {
            $extension = $myts->loadExtension($key);
            @list($encode, $js) = $extension->encode($textarea_id);
            if (empty($encode)) {
                continue;
            }
            $code .= $encode;
            if (!empty($js)) {
                $element->js .= $js;
            }
        }
        $code .= "<button type='button' class='btn btn-default btn-sm' onclick='xoopsCodeCode(\"{$textarea_id}\", \"" . htmlspecialchars(_ENTERCODE, ENT_QUOTES) . "\");' onmouseover='style.cursor=\"hand\"' title='" . _XOOPS_FORM_ALT_CODE . "'><span class='fa fa-fw fa-code' aria-hidden='true'></span></button>";
        $code .= "<button type='button' class='btn btn-default btn-sm' onclick='xoopsCodeQuote(\"{$textarea_id}\", \"" . htmlspecialchars(_ENTERQUOTE, ENT_QUOTES) . "\");' onmouseover='style.cursor=\"hand\"' title='" . _XOOPS_FORM_ALT_QUOTE . "'><span class='fa fa-fw fa-quote-right' aria-hidden='true'></span></button>";
        $code .= '</div></div>';

        $xoopsPreload = XoopsPreload::getInstance();
        $xoopsPreload->triggerEvent('core.class.xoopsform.formdhtmltextarea.codeicon', array(&$code));

        return $code;
    }

    /**
     * Render typography controls for editor (font, size, color)
     *
     * @param XoopsFormDhtmlTextArea $element form element
     *
     * @return string rendered typography controls
     */
    protected function renderFormDhtmlTATypography(XoopsFormDhtmlTextArea $element)
    {
        $textarea_id = $element->getName();
        $hiddentext  = $element->_hiddenText;

        $fontarray = !empty($GLOBALS['formtextdhtml_fonts']) ? $GLOBALS['formtextdhtml_fonts'] : array(
            'Arial',
            'Courier',
            'Georgia',
            'Helvetica',
            'Impact',
            'Verdana',
            'Haettenschweiler');

        $colorArray = array(
            'Black'  => '000000',
            'Blue'   => '38AAFF',
            'Brown'  => '987857',
            'Green'  => '79D271',
            'Grey'   => '888888',
            'Orange' => 'FFA700',
            'Paper'  => 'E0E0E0',
            'Purple' => '363E98',
            'Red'    => 'FF211E',
            'White'  => 'FEFEFE',
            'Yellow' => 'FFD628',
        );

        $fontStr = '<div class="row"><div class="col-md-12"><div class="btn-group" role="toolbar">';
        $fontStr .= '<div class="btn-group">'
            . '<button type="button" class="btn btn-default btn-sm dropdown-toggle" title="'. _SIZE .'"'
            . ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
            . '<span class = "glyphicon glyphicon-text-height"></span><span class="caret"></span></button>'
            . '<ul class="dropdown-menu">';
            //. _SIZE . '&nbsp;&nbsp;<span class="caret"></span></button><ul class="dropdown-menu">';
        foreach ($GLOBALS['formtextdhtml_sizes'] as $value => $name) {
            $fontStr .= '<li><a href="javascript:xoopsSetElementAttribute(\'size\', \'' . $value . '\', \''
                . $textarea_id . '\', \'' . $hiddentext . '\');">' . $name . '</a></li>';
        }
        $fontStr .= '</ul></div>';

        $fontStr .= '<div class="btn-group">'
            . '<button type="button" class="btn btn-default btn-sm dropdown-toggle" title="'. _FONT .'"'
            . ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
            . '<span class = "glyphicon glyphicon-font"></span><span class="caret"></span></button>'
            . '<ul class="dropdown-menu">';
            //. _FONT . '&nbsp;&nbsp;<span class="caret"></span></button><ul class="dropdown-menu">';
        foreach ($fontarray as $font) {
            $fontStr .= '<li><a href="javascript:xoopsSetElementAttribute(\'font\', \'' . $font . '\', \''
                . $textarea_id . '\', \'' . $hiddentext . '\');">' . $font . '</a></li>';
        }
        $fontStr .= '</ul></div>';

        $fontStr .= '<div class="btn-group">'
            . '<button type="button" class="btn btn-default btn-sm dropdown-toggle" title="'. _COLOR .'"'
            . ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
            . '<span class = "glyphicon glyphicon-text-color"></span><span class="caret"></span></button>'
            . '<ul class="dropdown-menu">';
            //. _COLOR . '&nbsp;&nbsp;<span class="caret"></span></button><ul class="dropdown-menu">';
        foreach ($colorArray as $color => $hex) {
            $fontStr .= '<li><a href="javascript:xoopsSetElementAttribute(\'color\', \'' . $hex . '\', \''
                . $textarea_id . '\', \'' . $hiddentext . '\');">'
                . '<span style="color:#' . $hex . ';">' . $color .'</span></a></li>';
        }
        $fontStr .= '</ul></div>';
        $fontStr .= '</div>';

        //$styleStr = "<div class='row'><div class='col-md-12'>";
        $styleStr  = "<div class='btn-group' role='group'>";
        $styleStr .= "<button type='button' class='btn btn-default btn-sm' onclick='xoopsMakeBold(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_BOLD . "' aria-label='Left Align'><span class='fa fa-bold' aria-hidden='true'></span></button>";
        $styleStr .= "<button type='button' class='btn btn-default btn-sm' onclick='xoopsMakeItalic(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_ITALIC . "' aria-label='Left Align'><span class='fa fa-italic' aria-hidden='true'></span></button>";
        $styleStr .= "<button type='button' class='btn btn-default btn-sm' onclick='xoopsMakeUnderline(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_UNDERLINE . "' aria-label='Left Align'>" . '<span class="fa fa-underline"></span></button>';
        $styleStr .= "<button type='button' class='btn btn-default btn-sm' onclick='xoopsMakeLineThrough(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_LINETHROUGH . "' aria-label='Left Align'>" . '<span class="fa fa-strikethrough"></span></button>';
        $styleStr .= '</div>';

        $alignStr = "<div class='btn-group' role='group'>";
        $alignStr .= "<button type='button' class='btn btn-default btn-sm' onclick='xoopsMakeLeft(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_LEFT . "' aria-label='Left Align'><span class='fa fa-align-left' aria-hidden='true'></span></button>";
        $alignStr .= "<button type='button' class='btn btn-default btn-sm' onclick='xoopsMakeCenter(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_CENTER . "' aria-label='Left Align'><span class='fa fa-align-center' aria-hidden='true'></span></button>";
        $alignStr .= "<button type='button' class='btn btn-default btn-sm' onclick='xoopsMakeRight(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_RIGHT . "' aria-label='Left Align'><span class='fa fa-align-right' aria-hidden='true'></span></button>";
        $alignStr .= '</div>';

        $fontStr .= "&nbsp;{$styleStr}&nbsp;{$alignStr}&nbsp;\n";

        $fontStr .= "<button type='button' class='btn btn-default btn-sm' onclick=\"XoopsCheckLength('"
            . $element->getName() . "', '" . @$element->configs['maxlength'] . "', '"
            . _XOOPS_FORM_ALT_LENGTH . "', '" . _XOOPS_FORM_ALT_LENGTH_MAX . "');\" title='"
            . _XOOPS_FORM_ALT_CHECKLENGTH . "'><span class='fa fa-check-square-o' aria-hidden='true'></span></button>";
        $fontStr .= '</div></div>';

        return $fontStr;
    }

    /**
     * Render support for XoopsFormElementTray
     *
     * @param XoopsFormElementTray $element form element
     *
     * @return string rendered form element
     */
    public function renderFormElementTray(XoopsFormElementTray $element)
    {
        $count = 0;
        $ret = '<span class="form-inline ' . $element->getClass() . '">';
        foreach ($element->getElements() as $ele) {
            if ($count > 0) {
                $ret .= $element->getDelimeter();
            }
            if ($ele->getCaption() != '') {
                $ret .= $ele->getCaption() . '&nbsp;';
            }
            $ret .= $ele->render() . NWLINE;
            if (!$ele->isHidden()) {
                ++$count;
            }
        }
        /*
        if (substr_count($ret, '<div class="form-group form-inline">') > 0) {
            $ret = str_replace('<div class="form-group form-inline">', '', $ret);
            $ret = str_replace('</div>', '', $ret);
        }
        if (substr_count($ret, '<div class="checkbox-inline">') > 0) {
            $ret = str_replace('<div class="checkbox-inline">', '', $ret);
        }
        */
        $ret .= '</span>';
        return $ret;
    }

    /**
     * Render support for XoopsFormFile
     *
     * @param XoopsFormFile $element form element
     *
     * @return string rendered form element
     */
    public function renderFormFile(XoopsFormFile $element)
    {
        $ret = '';
        $ret .= '<input '
            . 'class="form-control-static" '
            . 'type="file" '
            . 'name="' . $element->getName() . '" '
            . 'id="' . $element->getName() . '" '
            . 'title="' . $element->getTitle() . '" ' . $element->getExtra() . ' />';
        $ret .= '<input '
            . 'type="hidden" '
            . 'name="MAX_FILE_SIZE" '
            . 'value="' . $element->getMaxFileSize() . '" />';
        $ret .= '<input '
            . 'type="hidden" '
            . 'name="xoops_upload_file[]" '
            . 'id="xoops_upload_file[]" '
            . 'value="' . $element->getName() . '" />';
        return $ret;
    }

    /**
     * Render support for XoopsFormLabel
     *
     * @param XoopsFormLabel $element form element
     *
     * @return string rendered form element
     */
    public function renderFormLabel(XoopsFormLabel $element)
    {
        return '<div class="form-control-static">' . $element->getValue() . '</div>';
    }

    /**
     * Render support for XoopsFormPassword
     *
     * @param XoopsFormPassword $element form element
     *
     * @return string rendered form element
     */
    public function renderFormPassword(XoopsFormPassword $element)
    {
        $ret = '<input '
               . "class='form-control' "
               . "type='password' name='{$element->getName()}' "
               . "id='{$element->getName()}' "
               . "size='{$element->getSize()}' "
               . "maxlength='{$element->getMaxlength()}' "
               . "value='{$element->getValue()}'"
               . "{$element->getExtra()} " . ($element->autoComplete ? '' : "autocomplete='off' ") . '/>';
        if ($element->confirm) {
            $ret .= '&nbsp;';
            $ret .= '<input '
                    . "class='form-control' "
                    . "type='password' name='{$element->getName()}_confirm' "
                    . "id='{$element->getName()}_confirm' "
                    . "size='{$element->getSize()}' "
                    . "maxlength='{$element->getMaxlength()}' "
                    . "value='{$element->getValue()}' "
                    . "onfocusout='";
            $ret .= 'var password = document.getElementById("{$element->getName()}"), password_confirm = document.getElementById("{$element->getName()}_confirm");';
            $ret .= 'if(password.value != password_confirm.value) {';
            $ret .= 'alert("' . _XOOPS_FORMPASSWORD_NOTMATCH . '");';
            $ret .= '}';
            $ret .= "'";
            $ret .= "{$element->getExtra()} " . ($element->autoComplete ? '' : "autocomplete='off' ") . '/>';

        }
        return $ret;
    }

    /**
     * Render support for XoopsFormRadio
     *
     * @param XoopsFormRadio $element form element
     *
     * @return string rendered form element
     */
    public function renderFormRadio(XoopsFormRadio $element)
    {

        $elementName = $element->getName();
        $elementId = $elementName;

        switch ((int) ($element->columns)) {
            case 0:
                return $this->renderCheckedInline($element, 'radio', $elementId, $elementName);
            case 1:
                return $this->renderCheckedOneColumn($element, 'radio', $elementId, $elementName);
            default:
                return $this->renderCheckedColumnar($element, 'radio', $elementId, $elementName);
        }
    }

    /**
     * Render support for XoopsFormSelect
     *
     * @param XoopsFormSelect $element form element
     *
     * @return string rendered form element
     */
    public function renderFormSelect(XoopsFormSelect $element)
    {
        $class = 'form-control';
        $class .= ($element->getClass()) ? ' ' . $element->getClass() : '';
        $elementName    = $element->getName();
        $elementTitle   = $element->getTitle();
        $elementValue   = $element->getValue();
        $elementOptions = $element->getOptions();
        $ret = '<select '
               . "class='{$class}' "
               . "size='{$element->getSize()}' "
               . (string)($element->getExtra());
        if ($element->isMultiple() != false) {
            $ret .= ' '
                    . "name='{$elementName}[]' "
                    . "id='{$elementName}' "
                    . "title='{$elementTitle}' "
                    . "multiple='multiple'>";
        } else {
            $ret .= ' '
                    . "name='{$elementName}' "
                    . "id='{$elementName}' "
                    . "title='{$elementTitle}'>";
        }
        foreach ($elementOptions as $value => $name) {
            $ret .= "<option value='" . htmlspecialchars($value, ENT_QUOTES) . "'";
            if (count($elementValue) > 0 && in_array($value, $elementValue)) {
                $ret .= ' selected';
            }
            $ret .= ">{$name}</option>";
        }
        $ret .= '</select>';

        return $ret;
    }
    /**
     * Render support for XoopsFormText
     *
     * @param XoopsFormText $element form element
     *
     * @return string rendered form element
     */
    public function renderFormText(XoopsFormText $element)
    {
        $class = 'form-control';
        $class .= ($element->getClass()) ? ' ' . $element->getClass() : '';
        $ret = '<input '
               . "class='{$class}' "
               . "type='text' "
               . "name='{$element->getName()}' "
               . "title='{$element->getTitle()}' "
               . "id='{$element->getName()}' "
               . "size='{$element->getSize()}' "
               . "maxlength='{$element->getMaxlength()}' "
               . "value='{$element->getValue()}'"
               . "{$element->getExtra()} />";
        return $ret;
    }

    /**
     * Render support for XoopsFormTextArea
     *
     * @param XoopsFormTextArea $element form element
     *
     * @return string rendered form element
     */
    public function renderFormTextArea(XoopsFormTextArea $element)
    {
        $class = 'form-control';
        $class .= ($element->getClass()) ? ' ' . $element->getClass() : '';
        $ret = '<textarea '
               . "class='{$class}' "
               . "name='{$element->getName()}' "
               . "id='{$element->getName()}' "
               . "title='{$element->getTitle()}' "
               . "rows='{$element->getRows()}' "
               . "cols='{$element->getCols()}'"
               . "{$element->getExtra()}>{$element->getValue()}</textarea>";
        return $ret;
    }

    /**
     * Render support for XoopsFormTextDateSelect
     *
     * https://github.com/uxsolutions/bootstrap-datepicker
     *
     * @param XoopsFormTextDateSelect $element form element
     *
     * @return string rendered form element
     */
    public function renderFormTextDateSelect(XoopsFormTextDateSelect $element)
    {
        static $included = false;
        if (file_exists(XOOPS_ROOT_PATH . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/calendar.php')) {
            include_once XOOPS_ROOT_PATH . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/calendar.php';
        } else {
            include_once XOOPS_ROOT_PATH . '/language/english/calendar.php';
        }

        $elementName  = $element->getName();
        $elementValue = $element->getValue(false);
        if (is_string($elementValue)) {
            $display_value = $elementValue;
            $elementValue     = time();
        } else {
            $display_value = date(_SHORTDATESTRING, $elementValue);
        }

        $jstime = formatTimestamp($elementValue, _SHORTDATESTRING);
        if (isset($GLOBALS['xoTheme']) && is_object($GLOBALS['xoTheme'])) {
            $GLOBALS['xoTheme']->addScript('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js');
            $GLOBALS['xoTheme']->addScript('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/locales/bootstrap-datepicker.' . 'it' . '.min.js');
            $GLOBALS['xoTheme']->addStylesheet('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css');
            $GLOBALS['xoTheme']->addStylesheet('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css');
            if (!$included) {
                $included = true;
                $GLOBALS['xoTheme']->addScript('', '', '');
            }
        }
        $ret = <<<EOE
            <div class='input-group date'>
                <input
                    type='text'
                    class='form-control'
                    name='{$elementName}'
                    id='{$elementName}'
                    size='{$element->getSize()}'
                    maxlength='{$element->getMaxlength()}'
                    value='{$display_value}'
                    {$element->getExtra()}
                    >
                <div class='input-group-addon'>
                    <span class='glyphicon glyphicon-th'></span>
                </div>
            </div>
            <script>
                $('#' + $.escapeSelector('{$elementName}')).datepicker({
                    format: 'dd/mm/yyyy',
                    language: 'it',
                    autoclose: true
                });
            </script>
EOE;
        return $ret;
    }

    /**
     * Render support for XoopsThemeForm
     *
     * @param XoopsThemeForm $form form to render
     *
     * @return string rendered form
     */
    public function renderThemeForm(XoopsThemeForm $form)
    {
        $formName = $form->getName();

        $ret = '<div>';
        $ret .= '<form '
                . "class='{$form->getClass()}' "
                . "name='{$formName}' "
                . "id='{$formName}' "
                . "action='{$form->getAction()}' "
                . "method='{$form->getMethod()}' "
                . "onsubmit='return xoopsFormValidate_{$formName}();' "
                . "{$form->getExtra()}>";
        $ret .= "<h3>{$form->getTitle()}</h3>";
        $hidden   = '';
        foreach ($form->getElements() as $element) {
            if (!is_object($element)) { // see $form->addBreak()
                $ret .= $element;
                continue;
            }
            if ($element->isHidden()) {
                $hidden .= $element->render();
                continue;
            }
            $ret .= "<div class='form-group'>";
            if (($caption = $element->getCaption()) != '') {
                $ret .= "<label for='{$element->getName()}' class='col-md-2 control-label'>"
                    . $element->getCaption()
                    . ($element->isRequired() ? "<span class='caption-required'>*</span>" : '')
                        . '</label>';
            } else {
                $ret .= "<div class='col-md-2'> </div>";
            }
            $ret .= "<div class='col-md-10'>";
            $ret .= $element->render();
            if (($desc = $element->getDescription()) != '') {
                $ret .= "<p class='help-block'>{$desc}</p>";
            }
            $ret .= '</div>';
            $ret .= '</div>';
        }
        $ret .= $hidden;
        $ret .= '</form></div>';
        $ret .= $form->renderValidationJS(true);

        return $ret;
    }

    /**
     * Support for themed addBreak
     *
     * @param XoopsThemeForm $form
     * @param string         $extra pre-rendered content for break row
     * @param string         $class class for row
     *
     * @return void
     */
    public function addThemeFormBreak(XoopsThemeForm $form, $extra, $class)
    {
        $class = ($class != '') ? preg_replace('/[^A-Za-z0-9\s\s_-]/i', '', $class) : '';
        $form->addElement('<div class="col-sm-12 ' . $class .'">'. $extra . '</div>');
    }
}
