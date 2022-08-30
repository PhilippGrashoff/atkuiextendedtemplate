<?php declare(strict_types=1);

namespace atkuiextendedtemplate\tests\testclasses;

use Atk4\Ui\View;
use atkuiextendedtemplate\SubTemplateCloneDeleteTrait;

class ViewWithSubTemplateCloneTrait extends View
{
    use SubTemplateCloneDeleteTrait;

    public $_tLala;
    public $_tDada;
}

;