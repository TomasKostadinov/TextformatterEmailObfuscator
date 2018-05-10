<?php

/**
 * ProcessWire Mixcloud Embedding Textformatter
 * Copyright (c) 2017 by Conclurer GmbH / Tomas Kostadinov
 *
 * Looks for Emails and automatically obfuscates them.
 *
 *
 * ProcessWire 3.x
 * Copyright (C) 2018 by Tomas Kostadinov / Conclurer GmbH
 * Licensed under MIT
 *
 * http://conclurer.com
 * http://tomaskostadinov.com
 *
 *
 *
 */

class TextformatterEmailObfuscator extends Textformatter implements ConfigurableModule {

	public static function getModuleInfo() {
		return array('title' => __('Email Obfuscator', __FILE__), 'version' => 100, 'summary' => __('Automatically obfuscate emails. Add this text formatter to the fields you want your emails to be obfuscated', __FILE__), 'author' => 'Tomas Kostadinov / Conclurer GmbH', 'href' => 'https://tomaskostadinov.com');
	}

	/**
	 * Text formatting function as used by the Textformatter interface
	 */
	public function format(&$str) {
		$this->searchMail($str, $this->get("code"));
	}

	/**
	 *
	 * Search and replace emails with obfuscated strings
	 *
	 */
	protected function searchMail(&$str) {
		if (strpos($str, '@') === false) return;
		preg_match_all('/(mailto:)?([a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b)/', $str, $matches);
		foreach ($matches[0] as $match) {
			if (strpos($match, '@') === false) return;
			if (strpos($match, 'mailto') === false) {
				$str = str_replace($match, "<script>document.write(decryptAndReturn('" . $this->encryptEmail($match) . "'))</script>", $str);
			} else {
				$str = str_replace($match, "javascript:decryptAndOpen('" . $this->encryptEmail($match) . "')", $str);
			}
		}
	}

	/**
	 * Module configuration screen
	 *
	 */
	public static function getModuleConfigInputfields(array $data) {
		$inputfields = new InputfieldWrapper();
		$f = wire('modules')->get('InputfieldTextarea');
		$f->attr('value', '<?php $module = $modules->getModule("TextformatterEmailObfuscator"); echo $module->renderJS();?>');
		$f->label = "Code for obfuscation";
		$f->description = "Add this code to your layout file to automatically add the needed js files.";
		$inputfields->add($f);

		return $inputfields;
	}

	/**
	 * @param $string
	 * @return string obfuscated email
	 */
	function encryptEmail($string) {
		$string = str_replace("@", "@@", $string);
		return strrev($string);
	}

	public function renderJS() {
		$file = new ProcessWire\TemplateFile(dirname(__FILE__) . '/templates/encrypt-js.inc.php');
		return $file->render();
	}

}
