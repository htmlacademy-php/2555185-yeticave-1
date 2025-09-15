<?php

require_once('./helpers.php');

$categories =["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];
$advertisements = [
[
    'name'=> '2014 Rossignol District Snowboard',
    'category'=> $categories[0],
    'price'=> 10999,
    'imgUrl'=>'img/lot-1.jpg'
],

[
    'name'=> 'DC Ply Mens 2016/2017 Snowboard',
    'category'=> $categories[0],
    'price'=> 159999,
    'imgUrl'=>'img/lot-2.jpg'
],

[
    'name'=> 'Крепления Union Contact Pro 2015 года размер L/XL',
    'category'=> $categories[1],
    'price'=> 8000,
    'imgUrl'=>'img/lot-3.jpg'
],

[
    'name'=> 'Ботинки для сноуборда DC Mutiny Charocal',
    'category'=> $categories[2],
    'price'=> 10999,
    'imgUrl'=>'img/lot-4.jpg'
],

[
    'name'=> 'Куртка для сноуборда DC Mutiny Charocal',
    'category'=> $categories[3],
    'price'=> 7500,
    'imgUrl'=>'img/lot-5.jpg'
],

[
    'name'=> 'Маска Oakley Canopy',
    'category'=> $categories[4],
    'price'=> 5400,
    'imgUrl'=>'img/lot-6.jpg'
],

];

$pageContent = include_template('main.php', [
    'categories' => $categories,
    'advertisements' => $advertisements,
]);

$pageLayout = include_template('layout.php', [
    'pageContent' => $pageContent,
    'title' => 'Главная',
    'userName' => 'Александра',
    'categories' => $categories,
]);

print $pageLayout;
