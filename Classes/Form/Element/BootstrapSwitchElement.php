<?php
namespace SimpleTYPO3\DocumentNodeType\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper;
use TYPO3\CMS\Lang\Service\TranslationService;

/**
 * Generation of TCEform elements of the type "check"
 */
class BootstrapSwitchElement extends AbstractFormElement
{

    /**
     * Add an own language object with needed labels
     *
     * @param array $resultArray
     * @return array
     */
    protected function createJavaScriptLanguageLabels(array $resultArray)
    {
        /** @var $languageFactory LocalizationFactory */
        $languageFactory = GeneralUtility::makeInstance(LocalizationFactory::class);
        $language = $GLOBALS['LANG']->lang;
        $localizationArray = $languageFactory->getParsedData(
            'EXT:document_node_type/Resources/Private/Language/locallang_form.xlf',
            $language,
            'utf-8',
            1
        );
        if (is_array($localizationArray) && !empty($localizationArray)) {
            if (!empty($localizationArray[$language])) {
                $xlfLabelArray = $localizationArray['default'];
                ArrayUtility::mergeRecursiveWithOverrule($xlfLabelArray, $localizationArray[$language], true, false);
            } else {
                $xlfLabelArray = $localizationArray['default'];
            }
        } else {
            $xlfLabelArray = [];
        }
        $labelArray = [];
        foreach ($xlfLabelArray as $key => $value) {
            if (isset($value[0]['target'])) {
                $labelArray[$key] = $value[0]['target'];
            } else {
                $labelArray[$key] = '';
            }
        }
        $javaScriptString = 'var BootstrapSwitchElement = BootstrapSwitchElement || {};' . LF;
        $javaScriptString .= 'BootstrapSwitchElement.lang = ' . json_encode($labelArray) . LF;
        $resultArray['additionalJavaScriptPost'][] = $javaScriptString;
        return $resultArray;
    }

    /**
     * This will render a checkbox or an array of checkboxes
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {


        $resultArray = $this->initializeResultArray();
        $resultArray['requireJsModules'] = ['TYPO3/CMS/DocumentNodeType/BootstrapSwitchElement'];

        $resultArray['stylesheetFiles'] = ['../typo3conf/ext/document_node_type/bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css'];
        $resultArray = $this->createJavaScriptLanguageLabels($resultArray);

        $html = '';
        $disabled = false;
        if ($this->data['parameterArray']['fieldConf']['config']['readOnly']) {
            $disabled = true;
        }
        // Traversing the array of items
        $items = $this->data['parameterArray']['fieldConf']['config']['items'];

        $numberOfItems = count($items);
        if ($numberOfItems === 0) {
            $items[] = array('', '');
            $numberOfItems = 1;
        }
        $formElementValue = (int)$this->data['parameterArray']['itemFormElValue'];
        $cols = (int)$this->data['parameterArray']['fieldConf']['config']['cols'];
        if ($cols > 1) {
            $colWidth = (int)floor(12 / $cols);
            $colClass = "col-md-12";
            $colClear = array();
            if ($colWidth == 6) {
                $colClass = "col-sm-6";
                $colClear = array(
                    2 => 'visible-sm-block visible-md-block visible-lg-block',
                );
            } elseif ($colWidth === 4) {
                $colClass = "col-sm-4";
                $colClear = array(
                    3 => 'visible-sm-block visible-md-block visible-lg-block',
                );
            } elseif ($colWidth === 3) {
                $colClass = "col-sm-6 col-md-3";
                $colClear = array(
                    2 => 'visible-sm-block',
                    4 => 'visible-md-block visible-lg-block',
                );
            } elseif ($colWidth <= 2) {
                $colClass = "checkbox-column col-sm-6 col-md-3 col-lg-2";
                $colClear = array(
                    2 => 'visible-sm-block',
                    4 => 'visible-md-block',
                    6 => 'visible-lg-block'
                );
            }
            $html .= '<div class="checkbox-row row">';
            $counter = 0;
            // @todo: figure out in which cases checkbox items to not begin at 0 and why and when this would be useful
            foreach ($items as $itemKey => $itemDefinition) {
                $html .=
                    '<div class="checkbox-column ' . $colClass . '">'
                    . $this->renderSingleCheckboxElement($resultArray, $itemKey,  $formElementValue, $numberOfItems, $this->data['parameterArray'], $disabled) .
                    '</div>';
                $counter = $counter + 1;
                if ($counter < $numberOfItems && !empty($colClear)) {
                    foreach ($colClear as $rowBreakAfter => $clearClass) {
                        if ($counter % $rowBreakAfter === 0) {
                            $html .= '<div class="clearfix ' . $clearClass . '"></div>';
                        }
                    }
                }
            }
            $html .= '</div>';
        } else {
            $counter = 0;
            foreach ($items as $itemKey => $itemDefinition) {
                $html .=  $this->renderSingleCheckboxElement($resultArray,  $counter, $formElementValue, $numberOfItems, $this->data['parameterArray'], $disabled);
                $counter = $counter + 1;
            }
        }
        if (!$disabled) {
            $html .= '<input type="hidden" name="' . $this->data['parameterArray']['itemFormElName'] . '" value="' . htmlspecialchars($formElementValue) . '" />';
        }
        $resultArray['html'] = $html;
        return $resultArray;
    }

    /**
     * This functions builds the HTML output for the checkbox
     *
     * @param string $label Label of this item
     * @param int $itemCounter Number of this element in the list of all elements
     * @param int $formElementValue Value of this element
     * @param int $numberOfItems Full number of items
     * @param array $additionalInformation Information with additional configuration options.
     * @param bool $disabled TRUE if form element is disabled
     * @return string Single element HTML
     */
    protected function renderSingleCheckboxElement(&$resultArray, $itemCounter, $formElementValue, $numberOfItems, $additionalInformation, $disabled)
    {
        $resultArray['requireJsModules'] = ['TYPO3/CMS/DocumentNodeType/BootstrapSwitchElement'];

        $label = $GLOBALS['LANG']->sL($this->data['parameterArray']['fieldConf']['label']);
        $config = $additionalInformation['fieldConf']['config'];
        $inline = !empty($config['cols']) && $config['cols'] === "inline";
        $checkboxParameters = $this->checkBoxParams(
            $additionalInformation['itemFormElName'],
            $formElementValue,
            $itemCounter,
            $numberOfItems,
            implode('', $additionalInformation['fieldChangeFunc'])
        );
        $checkboxId = $additionalInformation['itemFormElID'] . '_' . $itemCounter;
        $javaScriptString = 'BootstrapSwitchElement = BootstrapSwitchElement || {};' . LF;
        $javaScriptString .= 'BootstrapSwitchElement.id = BootstrapSwitchElement.id || [];'. LF;
        $javaScriptString .= 'BootstrapSwitchElement.id.push("'.$checkboxId.'");'. LF;
        $resultArray['additionalJavaScriptPost'][] = $javaScriptString;

        return '
			<div class="checkbox' . ($inline ? ' checkbox-inline' : '') . (!$disabled ? '' : ' disabled') . '">
				<label style="padding-left: 0; width:100%;">
					<input 	 data-size="mini"  class="switch" type="checkbox"
						value="1"
						data-formengine-input-name="' . htmlspecialchars($additionalInformation['itemFormElName']) . '"
						' . $checkboxParameters . '
						' . $additionalInformation['onFocus'] . '
						' . (!$disabled ?: ' disabled="disabled"') . '
						id="' . $checkboxId . '" />
				</label>

			</div>';
    }

    /**
     * Creates checkbox parameters
     *
     * @param string $itemName Form element name
     * @param int $formElementValue The value of the checkbox (representing checkboxes with the bits)
     * @param int $checkbox Checkbox # (0-9?)
     * @param int $checkboxesCount Total number of checkboxes in the array.
     * @param string $additionalJavaScript Additional JavaScript for the onclick handler.
     * @return string The onclick attribute + possibly the checked-option set.
     */
    protected function checkBoxParams($itemName, $formElementValue, $checkbox, $checkboxesCount, $additionalJavaScript = '')
    {
        $elementName = 'document.editform[' . Generalutility::quoteJSvalue($itemName) . ']';
        $checkboxPow = pow(2, $checkbox);
        $onClick = $elementName . '.value=this.checked?(' . $elementName . '.value|' . $checkboxPow . '):('
            . $elementName . '.value&' . (pow(2, $checkboxesCount) - 1 - $checkboxPow) . ');' . $additionalJavaScript;
        return ' onclick="' . htmlspecialchars($onClick) . '"' . ($formElementValue & $checkboxPow ? ' checked="checked"' : '');
    }
}


//
//use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
//use TYPO3\CMS\Backend\Utility\BackendUtility;
//use TYPO3\CMS\Core\Localization\LocalizationFactory;
//use TYPO3\CMS\Core\Utility\ArrayUtility;
//use TYPO3\CMS\Core\Utility\GeneralUtility;
//use TYPO3\CMS\Core\Utility\MathUtility;
//use TYPO3\CMS\Core\Utility\StringUtility;
//
//class BootstrapSwitchElement extends AbstractFormElement
//{
//
//    /**
//     * This will render a single-line input password field
//     * and a button to toggle password visibility
//     *
//     * @return array As defined in initializeResultArray() of AbstractNode
//     */
//    public function render()
//    {
//        $resultArray = $this->initializeResultArray();
//        $resultArray['requireJsModules'] = ['TYPO3/CMS/DocumentNodeType/BootstrapSwitchElement'];
//        $resultArray = $this->createJavaScriptLanguageLabels($resultArray);
//
//        $parameterArray = $this->data['parameterArray'];
//        $config = $parameterArray['fieldConf']['config'];
//        $evalList = GeneralUtility::trimExplode(',', $config['eval'], true);
//        $evalList = array_filter($evalList, function ($item) {
//            return $item !== 'password';
//        });
//        $attributes = [
//            'type' => 'password',
//            'value' => '********',
//            'autocomplete' => 'off',
//        ];
//
//        // @todo: The whole eval handling is a mess and needs refactoring
//        foreach ($evalList as $func) {
//            switch ($func) {
//                case 'required':
//                    $attributes['data-formengine-validation-rules'] = $this->getValidationDataAsJsonString(['required' => true]);
//                    break;
//                default:
//                    // @todo: This is ugly: The code should find out on it's own whether a eval definition is a
//                    // @todo: keyword like "date", or a class reference. The global registration could be dropped then
//                    // Pair hook to the one in \TYPO3\CMS\Core\DataHandling\DataHandler::checkValue_input_Eval()
//                    if (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][$func])) {
//                        if (class_exists($func)) {
//                            $evalObj = GeneralUtility::makeInstance($func);
//                            if (method_exists($evalObj, 'deevaluateFieldValue')) {
//                                $_params = [
//                                    'value' => $parameterArray['itemFormElValue'],
//                                ];
//                                $parameterArray['itemFormElValue'] = $evalObj->deevaluateFieldValue($_params);
//                            }
//                        }
//                    }
//            }
//        }
//
//        // set classes
//        $classes = [];
//        $classes[] = 'toggle-password-field';
//        $classes[] = 'form-control';
//        $classes[] = 't3js-clearable';
//        $classes[] = 'hasDefaultValue';
//
//        // calculate attributes
//        $paramsList = [
//            'field' => $parameterArray['itemFormElName'],
//            'evalList' => implode(',', $evalList),
//            'is_in' => trim($config['is_in']),
//        ];
//        $attributes['data-formengine-validation-rules'] = $this->getValidationDataAsJsonString($config);
//        $attributes['data-formengine-input-params'] = json_encode($paramsList);
//        $attributes['data-formengine-input-name'] = htmlspecialchars($parameterArray['itemFormElName']);
//        $attributes['id'] = StringUtility::getUniqueId('formengine-input-');
//        if (isset($config['max']) && (int)$config['max'] > 0) {
//            $attributes['maxlength'] = (int)$config['max'];
//        }
//        if (!empty($classes)) {
//            $attributes['class'] = implode(' ', $classes);
//        }
//
//        // This is the EDITABLE form field.
//        if (!empty($config['placeholder'])) {
//            $attributes['placeholder'] = trim($config['placeholder']);
//        }
//
//        if (isset($config['autocomplete'])) {
//            $attributes['autocomplete'] = empty($config['autocomplete']) ? 'off' : 'on';
//        }
//
//        // Build the attribute string
//        $attributeString = '';
//        foreach ($attributes as $attributeName => $attributeValue) {
//            $attributeString .= ' ' . $attributeName . '="' . htmlspecialchars($attributeValue) . '"';
//        }
//
//        $html = '
//			<div class="input-group">
//				<input' . $attributeString . ' />
//			</div>';
//
//        // This is the ACTUAL form field - values from the EDITABLE field must be transferred to this field which is the one that is written to the database.
//        $html .= '<input type="hidden" name="' . $parameterArray['itemFormElName'] . '" value="' . htmlspecialchars($parameterArray['itemFormElValue']) . '" />';
//
//        // Going through all custom evaluations configured for this field
//        // @todo: Similar to above code!
//        foreach ($evalList as $evalData) {
//            if (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][$evalData])) {
//                if (class_exists($evalData)) {
//                    $evalObj = GeneralUtility::makeInstance($evalData);
//                    if (method_exists($evalObj, 'returnFieldJS')) {
//                        $resultArray['extJSCODE'] .= LF . 'TBE_EDITOR.customEvalFunctions[' . GeneralUtility::quoteJSvalue($evalData) . '] = function(value) {' . $evalObj->returnFieldJS() . '}';
//                    }
//                }
//            }
//        }
//
//        // Wrap a wizard around the item?
//        $html = $this->renderWizards(
//            [$html],
//            $config['wizards'],
//            $this->data['tableName'],
//            $this->data['databaseRow'],
//            $this->data['fieldName'],
//            $parameterArray,
//            $parameterArray['itemFormElName'],
//            BackendUtility::getSpecConfParts($parameterArray['fieldConf']['defaultExtras'])
//        );
//
//        // Add a wrapper to remain maximum width
//        $size = MathUtility::forceIntegerInRange($config['size'] ?: $this->defaultInputWidth, $this->minimumInputWidth,
//            $this->maxInputWidth);
//        $width = (int)$this->formMaxWidth($size);
//        $html = '<div class="form-control-wrap"' . ($width ? ' style="max-width: ' . $width . 'px"' : '') . '>' . $html . '</div>';
//        $resultArray['html'] = $html;
//
//        return $resultArray;
//    }
//
//    /**
//     * Add an own language object with needed labels
//     *
//     * @param array $resultArray
//     * @return array
//     */
//    protected function createJavaScriptLanguageLabels(array $resultArray)
//    {
//        /** @var $languageFactory LocalizationFactory */
//        $languageFactory = GeneralUtility::makeInstance(LocalizationFactory::class);
//        $language = $GLOBALS['LANG']->lang;
//        $localizationArray = $languageFactory->getParsedData(
//            'EXT:formengine_example/Resources/Private/Language/locallang_form.xlf',
//            $language,
//            'utf-8',
//            1
//        );
//        if (is_array($localizationArray) && !empty($localizationArray)) {
//            if (!empty($localizationArray[$language])) {
//                $xlfLabelArray = $localizationArray['default'];
//                ArrayUtility::mergeRecursiveWithOverrule($xlfLabelArray, $localizationArray[$language], true, false);
//            } else {
//                $xlfLabelArray = $localizationArray['default'];
//            }
//        } else {
//            $xlfLabelArray = [];
//        }
//        $labelArray = [];
//        foreach ($xlfLabelArray as $key => $value) {
//            if (isset($value[0]['target'])) {
//                $labelArray[$key] = $value[0]['target'];
//            } else {
//                $labelArray[$key] = '';
//            }
//        }
//
//        $javaScriptString = 'var FormengineExample = new Object();' . LF;
//        $javaScriptString .= 'FormengineExample.lang = ' . json_encode($labelArray) . LF;
//        $resultArray['additionalJavaScriptPost'][] = $javaScriptString;
//
//        return $resultArray;
//    }
//
//}