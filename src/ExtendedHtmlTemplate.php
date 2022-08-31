<?php declare(strict_types=1);

namespace atkuiextendedtemplate;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\HtmlTemplate;
use DateTimeInterFace;
use ReflectionClass;

class ExtendedHtmlTemplate extends HtmlTemplate
{

    protected string $dateTimeFormat = '';
    protected string $dateFormat = '';
    protected string $timeFormat = '';

    /**
     * Tries to set each passed tag with its value from passed model; if no tag list is passsed,
     * check for each model field if a tag is available
     */
    public function setTagsFromModel(Model $model, array $tags = [], string $prefix = null)
    {
        if (!$tags) {
            $tags = array_keys($model->getFields());
        }
        if ($prefix === null) {
            $prefix = strtolower((new ReflectionClass($model))->getShortName()) . '_';
        }

        foreach ($tags as $tag) {
            if (
                !$model->hasField($tag)
                || !$this->hasTag($prefix . $tag)
            ) {
                continue;
            }

            $this->setFieldValueToTag($model->getField($tag), $tag, $prefix);
        }
    }

    protected function setFieldValueToTag(Field $field, string $tag, string $prefix)
    {
        //try converting non-scalar values
        if (!is_scalar($field->get())) {
            if ($field->get() instanceof DateTimeInterFace) {
                $this->set(
                    $prefix . $tag,
                    $this->dateTimeFieldToString($field)
                );
            } else {
                $this->set($prefix . $tag, $field->toString());
            }
        } else {
            switch ($field->type) {
                case 'text':
                    $this->dangerouslySetHtml(
                        $prefix . $tag,
                        nl2br(
                            htmlspecialchars(
                                $this->getApp()->ui_persistence->typecastSaveField(
                                    $field,
                                    $field->get()
                                )
                            )
                        )
                    );
                    break;
                default:
                    $this->set(
                        $prefix . $tag,
                        $this->getApp()->ui_persistence->typecastSaveField(
                            $field,
                            $field->get()
                        )
                    );
                    break;
            }
        }
    }

    protected function dateTimeFieldToString(Field $field): string
    {
        //no DateTimeInterFace passed? Just return given value
        if ($field->get() instanceof \DateTimeInterface) {
            if ($field->type === 'datetime') {
                return $field->get()->format($this->getDesiredDateTimeFormat());
            }

            if ($field->type === 'date') {
                return $field->get()->format($this->getDesiredDateFormat());
            }
            if ($field->type === 'time') {
                return $field->get()->format($this->getDesiredTimeFormat());
            }
        }

        //field value can be null
        return (string)$field->get();
    }

    public function setDateTimeFormats(string $dateTimeFormat, string $dateFormat, string $timeFormat): void {
        //TODO check if formats are valid?
        $this->dateTimeFormat = $dateTimeFormat;
        $this->dateFormat = $dateFormat;
        $this->timeFormat = $timeFormat;
    }

    //TODO when properties in Persistence/UI are renamed to camel case, use this 3 methods as a wrapper,
    // put simple but duplicate logic in separate method.
    protected function getDesiredDateTimeFormat(): string
    {
        if (
            !$this->dateTimeFormat
            && isset($this->getApp()->ui_persistence->datetime_format)
        ) {
            $this->dateTimeFormat = $this->getApp()->ui_persistence->datetime_format;
        }
        return $this->dateTimeFormat;
    }

    protected function getDesiredDateFormat(): string
    {
        if (
            !$this->dateFormat
            && isset($this->getApp()->ui_persistence->date_format)
        ) {
            $this->dateFormat = $this->getApp()->ui_persistence->date_format;
        }
        return $this->dateFormat;
    }

    protected function getDesiredTimeFormat(): string
    {
        if (
            !$this->timeFormat
            && isset($this->getApp()->ui_persistence->time_format)
        ) {
            $this->timeFormat = $this->getApp()->ui_persistence->time_format;
        }
        return $this->timeFormat;
    }

    public function setWithLineBreaks(string $tag, string $value)
    {
        $this->dangerouslySetHtml($tag, nl2br(htmlspecialchars($value)));
    }
}
