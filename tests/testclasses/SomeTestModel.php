<?php declare(strict_types=1);

namespace atkuiextendedtemplate\tests\testclasses;

use Atk4\Data\Model;

class SomeTestModel extends Model
{

    public $table = 'some_test_model';

    protected function init(): void
    {
        parent::init();

        $fieldTypesToAdd = [
            'datetime',
            'date',
            'time',
            'string',
            'text',
            'integer',
            'float'
        ];

        foreach ($fieldTypesToAdd as $fieldType) {
            $this->addField(
                'some_' . $fieldType,
                ['type' => $fieldType]
            );
        }
    }
}