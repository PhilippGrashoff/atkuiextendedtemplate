<?php declare(strict_types=1);

namespace atkuiextendedtemplate\tests;

use atk4\data\Model;
use Atk4\Ui\App;
use DateTime;

class TemplateTest extends TestCase
{

    private $app;

    protected $sqlitePersistenceModels = [
        Setting::class,
        SettingGroup::class
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->app = new App(['always_run' => false]);
    }

    public function testSetTagsFromModel()
    {
        $model = $this->getTestModel();
        $model->set('name', 'BlaDU');
        $model->set('value', 3);
        $model->set('text', 'LALALALA');

        $t = new Template();
        $t->app = $this->app;
        $t->loadTemplateFromString('Hallo {$name} Test {$value} Miau {$text}!');
        $t->setTagsFromModel($model, ['name', 'value', 'text'], '');
        self::assertEquals('Hallo BlaDU Test 3 Miau LALALALA!', $t->render());
    }

    public function testSetTagsFromModelWithNonExistingTagAndField()
    {
        $model = $this->getTestModel();
        $model->set('name', 'BlaDU');
        $model->set('value', 3);
        $model->set('text', 'LALALALA');

        $t = new Template();
        $t->app = $this->app;
        $t->loadTemplateFromString('Hallo {$name} Test {$value} Miau {$nottext}!');
        $t->setTagsFromModel($model, ['name', 'value', 'text', 'nilla'], '');
        self::assertEquals('Hallo BlaDU Test 3 Miau !', $t->render());
    }

    public function testSetTagsFromModelWithDates()
    {
        $model = $this->getTestModel();
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', '2019-05-05 10:30:00');
        $model->set('datetime', clone $dt);
        $model->set('date', clone $dt);
        $model->set('time', clone $dt);

        $t = new Template();
        $t->app = $this->app;
        $t->loadTemplateFromString('Hallo {$datetime} Test {$date} Miau {$time}!');
        $t->setTagsFromModel($model, ['datetime', 'date', 'time'], '');
        self::assertEquals('Hallo 05.05.2019 10:30 Test 05.05.2019 Miau 10:30!', $t->render());
    }

    public function testSetTagsFromModelWithLimitedFields()
    {
        $model = $this->getTestModel();
        $model->set('name', 'BlaDU');
        $model->set('value', 3);
        $model->set('text', 'LALALALA');

        $t = new Template();
        $t->app = $this->app;
        $t->loadTemplateFromString('Hallo {$name} Test {$value} Miau {$text}!');
        $t->setTagsFromModel($model, ['name', 'value'], '');
        self::assertEquals('Hallo BlaDU Test 3 Miau !', $t->render());
    }

    public function testSetTagsFromModelWithEmptyFieldArray()
    {
        $model = $this->getTestModel();
        $model->set('name', 'BlaDU');
        $model->set('value', 3);
        $model->set('text', 'LALALALA');

        $t = new Template();
        $t->app = $this->app;
        $t->loadTemplateFromString('Hallo {$name} Test {$value} Miau {$text}!');
        $t->setTagsFromModel($model, [], '');
        self::assertEquals('Hallo BlaDU Test 3 Miau LALALALA!', $t->render());
    }

    public function testSetTagsFromModelWithPrefix()
    {
        $model = $this->getTestModel();
        $model->set('name', 'BlaDU');
        $model->set('value', 3);
        $model->set('text', 'LALALALA');

        $t = new Template();
        $t->app = $this->app;
        $t->loadTemplateFromString('Hallo {$group_name} Test {$group_value} Miau {$group_text}!');
        $t->setTagsFromModel($model, [], 'group_');
        self::assertEquals('Hallo BlaDU Test 3 Miau LALALALA!', $t->render());
    }

    public function testSetTagsFromModelWithOnlyOneParameter()
    {
        $model = new JustABaseModel($this->getSqliteTestPersistence());
        $model->set('name', 'BlaDU');
        $model->set('firstname', 'GuGuGu');
        $model->set('lastname', 'LALALALA');

        $template = new Template();
        $template->app = $this->app;
        $template->loadTemplateFromString(
            'Hallo {$justabasemodel_name} Test {$justabasemodel_firstname} Miau {$justabasemodel_lastname}!'
        );
        $template->setTagsFromModel($model);
        self::assertEquals('Hallo BlaDU Test GuGuGu Miau LALALALA!', $template->render());
    }

    public function testSetTagsFromModelWithTwoModelsWithPrefix()
    {
        $model = $this->getTestModel();
        $model->set('name', 'BlaDU');
        $model->set('value', 3);
        $model->set('text', 'LALALALA');

        $model2 = $this->getTestModel();
        $model2->set('name', 'ABC');
        $model2->set('value', 9);
        $model2->set('text', 'DEF');

        $t = new Template();
        $t->app = $this->app;
        $t->loadTemplateFromString(
            'Hallo {$group_name} Test {$group_value} Miau {$group_text}, du {$tour_name} Hans {$tour_value} bist toll {$tour_text}!'
        );
        $t->setTagsFromModel($model, [], 'group_');
        $t->setTagsFromModel($model2, [], 'tour_');
        self::assertEquals('Hallo BlaDU Test 3 Miau LALALALA, du ABC Hans 9 bist toll DEF!', $t->render());
    }

    public function testWithLineBreaks()
    {
        $t = new Template();
        $t->app = $this->app;
        $t->loadTemplateFromString('Hallo {$with_line_break} Test');
        $t->setWithLineBreaks('with_line_break', 'Hans' . PHP_EOL . 'Neu');
        $ex = 'Hallo Hans<br />' . PHP_EOL . 'Neu Test';
        self::assertEquals($ex, $t->render());
    }

    protected function getTestModel(): Model
    {
        $class = new class extends Model {
            public $table = 'blalba';

            protected function init(): void
            {
                parent::init();
                $this->addFields(
                    [
                        ['name', 'type' => 'string'],
                        ['value', 'type' => 'integer'],
                        ['text', 'type' => 'text'],
                        ['datetime', 'type' => 'datetime'],
                        ['date', 'type' => 'date'],
                        ['time', 'type' => 'time'],
                    ]
                );
            }
        };

        return new $class($this->app->db);
    }

}
