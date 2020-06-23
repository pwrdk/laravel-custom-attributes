<?php

function attr($model, $key, $column = false)
{
    if ($column) {
        return $model->attr($key)->get($column) ?? null;
    }

    return $model->attr($key)->get() ?? null;
}
