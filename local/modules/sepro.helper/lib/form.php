<?php
namespace Sepro;

class Form
{
    static $started = false;
    static $method = null;

    public static function start ($action, $method = 'post', array $attributes = [])
    {
        $attributes['action'] = $action;
        $attributes['method'] = $method;
        static::$started = true;
        static::$method = $method;

        return '<form'.static::attributes($attributes).'>';
    }

    public static function end ()
    {
        static::$started = false;
        static::$method = null;

        return '</form>';
    }

    public static function input ($type, $name, $value = null, $default = null, array $attributes = [])
    {
        $data = '<input';

        $attributes['type'] = $type;
        $attributes['name'] = $name;

        if (null !== $value)
        {
            $attributes['value'] = $value;
        }
        else
        {
            if (null !== $default)
            {
                $attributes['value'] = $default;
            }

            if (static::$started)
            {
                // IF METHOD HAS REQUEST VALUES
            }
        }

        $data .= static::attributes($attributes);

        return $data.'/>';
    }

    public static function select ($name, array $values, $value = null, $default = null, $multiple = false, array $attributes = [])
    {
        $data = '<select';

        $attributes['name'] = $name;

        if ($multiple)
        {
            $attributes['multiple'] = 'multiple';
        }

        $data .= static::attributes($attributes).'>';

        if (null === $value)
        {
            if (null !== $default)
            {
                $value = $default;
            }

            if (static::$started)
            {
                // IF METHOD HAS REQUEST VALUES
            }
        }

        foreach ($values as $k => $v)
        {
            $option = [];
            $n = null;

            $data .= '<option';

            if (is_array($v))
            {
                if (array_key_exists('value', $v))
                {
                    $option['value'] = $v['value'];

                    if (array_key_exists('name', $v))
                    {
                        $n = $v['name'];
                    }
                }
                else
                {
                    $shift = $v;
                    $option['value'] = array_shift($shift);

                    if (!empty($shift))
                    {
                        $n = array_shift($shift);
                    }

                    unset($shift);
                }
            }
            else
            {
                $n = $v;
                $option['value'] = $k;
            }

            if (is_array($value) && in_array($option['value'], $value))
            {
                $option['selected'] = 'selected';
            }
            else if ($option['value'] == $value)
            {
                $option['selected'] = 'selected';
            }

            $data .= static::attributes($option).'>';

            if (is_null($n))
            {
                $data .= $option['value'];
            }
            else
            {
                $data .= $n;
            }

            $data .= '</option>';
        }

        return  $data.'</select>';
    }

    private static function attributes (array $attributes = [])
    {
        if (null !== $attributes)
        {
            $data = array();

            foreach ($attributes as $name => $value)
            {
                $data[] = ' '.htmlspecialchars($name).'="'.htmlspecialchars($value).'"';
            }

            return implode(' ', $data);
        }

        return false;
    }
}