<?php

function field_to_label($name)
{
    $words = explode('_', $name);
    $label = '';
    for($i = 0; $i < count($words); $i++)
    {
        $word = $words[$i];
        $capitalized = strtoupper(substr($word, 0, 1)) . substr($word, 1, strlen($word));
        $label .= $capitalized;
        $label .= ($i != (count($words) - 1)) ?  ' ' : '';
    }
    return $label;
}

function form_trim($fields)
{
    foreach($fields as $field)
    {
        $_POST[$field] = trim($_POST[$field]);
    }
}

function form_required($field, &$errors)
{
    $fields = $field;
    is_array($fields) || $fields = array($fields);
    $success = TRUE;
    foreach($fields as $field)
    {
        if($_POST[$field] == '')
        {
            $errors[] = 'The "' . field_to_label($field) . '"" is required.';
            $success = FALSE;
        }
    }
    return $success;
}

function form_min_length($min, $field, &$errors)
{
    if(strlen($_POST[$field]) >= $min)
    {
        return TRUE;
    }
    $errors[] = 'The "' . field_to_label($field) . '" must be at least ' . $min . ' characters long.';
    return FALSE;
}

function form_max_length($max, $field, &$errors)
{
    if(strlen($_POST[$field]) < $max)
    {
        return TRUE;
    }
    $errors[] = 'The "' . field_to_label($field) . '" must be less than ' . $max . ' characters long.';
    return FALSE;
}

function form_valid_email(&$errors)
{
    if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
    {
        return TRUE;
    }
    $errors[] = 'The "Email" must be a valid email.';
    return FALSE;
}

function form_regex($regex, $error_message, $field, &$errors)
{
    if( ! preg_match_all($regex, $_POST[$field]))
    {
        return TRUE;
    }
    $errors[] = $error_message;
    return FALSE;
}

function form_matches($field1, $field2, &$errors)
{
    if($_POST[$field1] == $_POST[$field2])
    {
        return TRUE;
    }
    $errors[] = 'The "' . field_to_label($field1) . '" must match the "' . field_to_label($field2) . '".';
    return FALSE;
}