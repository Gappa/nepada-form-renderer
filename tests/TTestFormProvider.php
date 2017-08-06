<?php
/**
 * This file is part of the nepada/form-renderer.
 * Copyright (c) 2017 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace NepadaTests;

use NepadaTests\FormRenderer\FooControl;
use NepadaTests\FormRenderer\FooPresenter;
use Nette;
use Nette\Application\UI\Presenter;


trait TTestFormProvider
{

    /**
     * @return Nette\Application\UI\Form
     */
    protected function createTestForm(): Nette\Application\UI\Form
    {
        $presenter = $this->mockPresenter();
        $form = new Nette\Application\UI\Form($presenter, 'form');
        $form->getElementPrototype()->addClass('form-class1');
        $form->getElementPrototype()->addClass('form-class2');

        $form->addHidden('hidden');

        $group = $form->addGroup('Group 1');
        $group->setOption('description', 'Group 1 description.');
        $group->setOption('embedNext', true);
        $group->setOption('id', 'custom-group-id');
        $form->addText('text', 'Text');
        $form->addTextArea('textarea', 'TextArea');

        $group = $form->addGroup('Group 2');
        $group->setOption('label', Nette\Utils\Html::el('span', 'Group 2 label'));
        $group->setOption('description', Nette\Utils\Html::el('span', 'Group 2 description.'));
        $group->setOption('embedNext', true);
        $container = $form->addContainer('container');
        $container->addCheckbox('checkbox', 'Checkbox');
        $container->addCheckboxList('checkboxlist', 'CheckBoxList', [1 => 'one', 2 => 'two']);
        $container->addRadioList('radiolist', 'RadioList', [3 => 'three', 4 => 'four']);

        $innerContainer = $container->addContainer('innerContainer');
        $innerContainer->addSelect('selectbox', 'Selectbox', [5 => 'five', 6 => 'six']);
        $innerContainer->addUpload('upload', 'Upload');

        $form->setCurrentGroup(null);
        $form->addComponent(new FooControl('Foo'), 'foo');
        $form->addSubmit('send', 'SubmitButton');
        $form->addButton('button', 'Button');

        return $form;
    }

    /**
     * @return Presenter
     */
    private function mockPresenter(): Presenter
    {
        $presenter = new FooPresenter();

        $url = new Nette\Http\UrlScript('https://example.com/');
        $httpRequest = new Nette\Http\Request($url);
        $httpResponse = new Nette\Http\Response();
        $router = new Nette\Application\Routers\Route('/<presenter>/<action>');
        $presenter->injectPrimary(null, null, $router, $httpRequest, $httpResponse);

        $request = new Nette\Application\Request('Foo', 'GET');
        $requestReflection = new \ReflectionProperty(Presenter::class, 'request');
        $requestReflection->setAccessible(true);
        $requestReflection->setValue($presenter, $request);

        $initGlobalParametersReflection = new \ReflectionMethod(Presenter::class, 'initGlobalParameters');
        $initGlobalParametersReflection->setAccessible(true);
        $initGlobalParametersReflection->invoke($presenter);

        return $presenter;
    }

}