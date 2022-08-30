<?php declare(strict_types=1);

namespace atkuiextendedtemplate\tests;

use Atk4\Core\AtkPhpunit\TestCase;
use Atk4\Ui\HtmlTemplate;
use atk4\ui\View;
use atkuiextendedtemplate\SubTemplateCloneDeleteTrait;
use atkuiextendedtemplate\tests\testclasses\ViewWithSubTemplateCloneTrait;

class SubTemplateCloneDeleteTraitTest extends TestCase
{

    public function testTemplateCloneAndDelete(): void
    {
        $view = new ViewWithSubTemplateCloneTrait();
        $view->template = new HtmlTemplate();
        $view->template->loadFromString('Hans{Lala}test1{/Lala}{Dada}test2{/Dada}');
        $view->templateCloneAndDelete(['Lala', 'Dada']);
        self::assertEquals('test1', $view->_tLala->renderToHtml());
        self::assertEquals('test2', $view->_tDada->renderToHtml());
    }

    public function testTemplateCloneAndDeleteWithoutArgs(): void
    {
        $view = new ViewWithSubTemplateCloneTrait();
        $view->template = new HtmlTemplate();
        $view->template->loadFromString('Hans{Lala}test1{/Lala}{Dada}test2{/Dada}');
        $view->templateCloneAndDelete();
        self::assertEquals('test1', $view->_tLala->renderToHtml());
        self::assertEquals('test2', $view->_tDada->renderToHtml());
    }

    public function testCloneAndDeleteWithNonExistantRegion(): void
    {
        $view = new ViewWithSubTemplateCloneTrait();
        $view->template = new HtmlTemplate();
        $view->template->loadFromString('Hans{Lala}test1{/Lala}{Dada}test2{/Dada}');
        $view->templateCloneAndDelete(['Lala', 'Dada', 'NonExistantRegion']);
        self::assertEquals('test1', $view->_tLala->renderToHtml());
        self::assertEquals('test2', $view->_tDada->renderToHtml());
    }
}
