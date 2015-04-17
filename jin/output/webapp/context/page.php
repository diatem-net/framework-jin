<?php

namespace jin\output\webapp\context;

use jin\output\webapp\WebApp;
use jin\lang\StringTools;
use jin\output\webapp\context\DefaultController;
use jin\output\webapp\context\View;
use jin\output\webapp\template\TemplateManager;

class Page {

    public $controller;
    public $view;
    private $method;
    private $code;

    public function __construct($code, $method) {
        if (StringTools::right($code, 1) == '/') {
            $code = StringTools::left($code, StringTools::len($code) - 1);
        }
        if (StringTools::left($code, 1) == '/') {
            $code = StringTools::right($code, StringTools::len($code) - 1);
        }

        $this->code = $code;
        $this->method = $method;
        $this->setController();
        $this->setView();
    }

    public function getCode() {
        return $this->code;
    }

    public function getNamespace() {
        return StringTools::replaceAll($this->getCode(), '/', '_');
    }

    public function getMethod() {
        return $this->method;
    }

    private function setController() {
        if (is_file($this->getRootPath() . 'controller/' . $this->getMethod() . '/' . $this->getNameSpace() . '_controller.php')) {
            //Controler method
            include $this->getRootPath() . 'controller/' . $this->getMethod() . '/' . $this->getNameSpace() . '_controller.php';
            $classPath = '\\' . $this->getNameSpace() . '_controller';
            $this->controller = new $classPath();
        } else if ($this->getMethod() != 'GET' && is_file($this->getRootPath() . 'controller/GET/' . $this->getNameSpace() . '_controller.php')) {
            //Default controller GET
            include $this->getRootPath() . 'controller/GET/' . $this->getNameSpace() . '_controller.php';
            $classPath = '\\' . $this->getNameSpace() . '_controller';
            $this->controller = new $classPath();
        } else if (is_file($this->getRootPath() . 'controller/' . $this->getNameSpace() . '_controller.php')) {
            include $this->getRootPath() . 'controller/' . $this->getNameSpace() . '_controller.php';
            $classPath = '\\' . $this->getNameSpace() . '_controller';
            $this->controller = new $classPath();
        } else if (is_file($this->getRootPath() . '' . $this->getNameSpace() . '_controller.php')) {
            //Root controller
            include $this->getRootPath() . '' . $this->getNameSpace() . '_controller.php';
            $classPath = '\\' . $this->getNameSpace() . '_controller';
            $this->controller = new $classPath();
        } else {
            $this->controller = new DefaultController();
        }
    }

    private function setView() {
        if (is_file($this->getRootPath() . 'view/' . $this->getMethod() . '/view.php')) {
            //View for method specific
            $this->view = new View($this->getRootPath() . 'view/' . $this->getMethod() . '/view.php');
        } else if ($this->getMethod() != 'GET' && is_file($this->getRootPath() . 'view/GET/view.php')) {
            //View for default GET
            $this->view = new View($this->getRootPath() . 'view/GET/view.php');
        } else if (is_file($this->getRootPath() . 'view/view.php')) {
            //view folder
            $this->view = new View($this->getRootPath() . 'view/view.php');
        } else if (is_file($this->getRootPath() . 'view.php')) {
            $this->view = new View($this->getRootPath() . 'view.php');
        } else if (is_file($this->getRootPath() . 'index.php')) {
            //index.php view
            $this->view = new View($this->getRootPath() . 'index.php');
        } else {
            throw new \Exception('Vue introuvable pour la page ' . $this->code);
        }
    }

    public function getRootPath() {
        $folder = WebApp::getPagesFolder() . $this->code;
        if (StringTools::right($folder, 1) != '/') {
            $folder .= '/';
        }

        return $folder;
    }
    
    public function onInit(){
        $this->controller->onInit();
    }

    public function beforeRender() {
        $this->controller->beforeRender();
    }

    public function render() {
        $baseContent = $this->view->executeAndReturnContent();
        $content = TemplateManager::render($baseContent);
        $content = $this->controller->render($content);

        return $content;
    }

    public function afterRender() {
        $this->controller->afterRender();
    }

}
