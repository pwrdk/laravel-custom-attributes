<?php

function attr($model, $key, $index = false)
{
    if ($index) {
        return $model->attr()->$key->value[$index] ?? null;
    }

    return $model->attr()->$key->value ?? null;
}
