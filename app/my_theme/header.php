<!DOCTYPE html>
<html <?php language_attributes(); ?> lang="ru">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title(); ?></title>
    <?php wp_head(); // Обязательно добавьте эту строку ?>
</head>
<body <?php body_class(); ?>>
