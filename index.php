<?php
date_default_timezone_set('Asia/Vladivostok');
require_once('./helpers.php');

$categories =["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];
$advertisements = [
[
    'name'=> '2014 Rossignol District Snowboard',
    'category'=> $categories[0],
    'price'=> 10999,
    'imgUrl'=>'img/lot-1.jpg',
    'expirationDate'=> '2025-09-19'
],

[
    'name'=> 'DC Ply Mens 2016/2017 Snowboard',
    'category'=> $categories[0],
    'price'=> 159999,
    'imgUrl'=>'img/lot-2.jpg',
    'expirationDate'=> '2025-09-21'
],

[
    'name'=> 'Крепления Union Contact Pro 2015 года размер L/XL',
    'category'=> $categories[1],
    'price'=> 8000,
    'imgUrl'=>'img/lot-3.jpg',
    'expirationDate'=> '2025-09-22'
],

[
    'name'=> 'Ботинки для сноуборда DC Mutiny Charocal',
    'category'=> $categories[2],
    'price'=> 10999,
    'imgUrl'=>'img/lot-4.jpg',
    'expirationDate'=> '2025-09-23'
],

[
    'name'=> 'Куртка для сноуборда DC Mutiny Charocal',
    'category'=> $categories[3],
    'price'=> 7500,
    'imgUrl'=>'img/lot-5.jpg',
    'expirationDate'=> '2025-09-24'
],

[
    'name'=> 'Маска Oakley Canopy',
    'category'=> $categories[4],
    'price'=> 5400,
    'imgUrl'=>'img/lot-6.jpg',
    'expirationDate'=> '2025-09-25'
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
