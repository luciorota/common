<?php

class br extends DOMElement {
    public function __construct() {
        parent::__construct('br');
    }
}

//class XoopsDOMDocument extends DOMDocument
//{
//    public function createElement() {
//        return new XoopsDOMElement();
//        
//        
//    }
//    
//}
//
//
//
//class XoopsDOMElement extends DOMElement
//{
//    public function setAttributes($attributes = array()) {
//        foreach ($attributes as $attribute => $values) {
//            if (is_array($values)) {
//                foreach ($values as $value) {
//                    $this->setAttribute($attribute, $value);
//                }
//            } else {
//                $this->setAttribute($attribute, $values);
//            }
//        }
//    }
//}

$page = new DOMDocument();
$page->normalizeDocument();
$page->formatOutput = true;

$html = $page->createElement('html');
$head = $page->createElement('head');
$title = $page->createElement('title');
$body = $page->createElement('body');
$form = $page->createElement('form');
$fieldset = $page->createElement('fieldset');
$name = $page->createElement('input');
$email = $page->createElement('input');
$submit = $page->createElement('input');

$title_text = $page->createTextNode('Page Title Here');
$title->appendChild($title_text);

$head->appendChild($title);

$html->appendChild($head);

$name->setAttribute('type', 'text');
$name->setAttribute('name', 'name');

$email->setAttribute('type', 'text');
$email->setAttribute('name', 'email');

$submit->setAttribute('type','submit');
$submit->setAttribute('value','Submit');

$fieldset->appendChild($page->createTextNode('Name: '));
$fieldset->appendChild($name);
$fieldset->appendChild(new br);
$fieldset->appendChild($page->createTextNode('Email: '));
$fieldset->appendChild($email);
$fieldset->appendChild(new br);
$fieldset->appendChild($submit);

$form->appendChild($fieldset);

$body->appendChild($form);

$html->appendChild($body);

$page->appendChild($html);

echo "<!DOCTYPE html>\n" . html_entity_decode($page->saveHTML());
