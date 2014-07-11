<?php
namespace Famelo\Bootstrap\ViewHelpers\Button;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Fluid".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper;
use TYPO3\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3\Fluid\ViewHelpers\FormViewHelper;
use TYPO3\Fluid\ViewHelpers\Form\AbstractFormViewHelper;

/**
 *
 * @api
 */
class ActionViewHelper extends FormViewHelper {
	/**
	 * Render the form.
	 *
	 * @param string $action target action
	 * @param array $arguments additional arguments
	 * @param string $controller name of target controller
	 * @param string $package name of target package
	 * @param string $subpackage name of target subpackage
	 * @param mixed $object object to use for the form. Use in conjunction with the "property" attribute on the sub tags
	 * @param string $section The anchor to be added to the action URI (only active if $actionUri is not set)
	 * @param string $format The requested format (e.g. ".html") of the target page (only active if $actionUri is not set)
	 * @param array $additionalParams additional action URI query parameters that won't be prefixed like $arguments (overrule $arguments) (only active if $actionUri is not set)
	 * @param boolean $absolute If set, an absolute action URI is rendered (only active if $actionUri is not set)
	 * @param boolean $addQueryString If set, the current query parameters will be kept in the action URI (only active if $actionUri is not set)
	 * @param array $argumentsToBeExcludedFromQueryString arguments to be removed from the action URI. Only active if $addQueryString = TRUE and $actionUri is not set
	 * @param string $fieldNamePrefix Prefix that will be added to all field names within this form
	 * @param string $actionUri can be used to overwrite the "action" attribute of the form tag
	 * @param string $objectName name of the object that is bound to this form. If this argument is not specified, the name attribute of this form is used to determine the FormObjectName
	 * @param boolean $useParentRequest If set, the parent Request will be used instead ob the current one
	 * @return string rendered form
	 * @api
	 * @throws ViewHelper\Exception
	 */
	public function render($action = NULL, array $arguments = array(), $controller = NULL, $package = NULL, $subpackage = NULL, $object = NULL, $section = '', $format = '', array $additionalParams = array(), $absolute = FALSE, $addQueryString = FALSE, array $argumentsToBeExcludedFromQueryString = array(), $fieldNamePrefix = NULL, $actionUri = NULL, $objectName = NULL, $useParentRequest = FALSE) {
		$this->formActionUri = NULL;
		if ($action === NULL && $actionUri === NULL) {
			throw new ViewHelper\Exception('FormViewHelper requires "actionUri" or "action" argument to be specified', 1355243748);
		}
		$this->tag->addAttribute('action', $this->getFormActionUri());

		if (strtolower($this->arguments['method']) === 'get') {
			$this->tag->addAttribute('method', 'get');
		} else {
			$this->tag->addAttribute('method', 'post');
		}

		$formContent = $this->renderChildren();

		// wrap hidden field in div container in order to create XHTML valid output
		$content = chr(10) . '<div style="display: none">';
		if (strtolower($this->arguments['method']) === 'get') {
			$content .= $this->renderHiddenActionUriQueryParameters();
		}
		$content .= $this->renderHiddenIdentityField($this->arguments['object'], $this->getFormObjectName());
		$content .= $this->renderAdditionalIdentityFields();
		$content .= $this->renderHiddenReferrerFields();
		$content .= $this->renderEmptyHiddenFields();
		// Render the trusted list of all properties after everything else has been rendered
		if (strtolower($this->arguments['method']) !== 'get') {
			$content .= $this->renderCsrfTokenField();
		}
		$content .= chr(10) . '</div>' . chr(10);

		$button = new TagBuilder('button', $formContent);
		$button->addAttribute('type', 'submit');
		$button->addAttribute('class', 'link');
		$content .= $button->render();

		$this->tag->addAttribute('class', 'link');
		$this->tag->setContent($content);
		return $this->tag->render();
	}
}
