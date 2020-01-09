<?php
    $start = microtime(true);
    /**
     * mysql> create database naiveBayes;
     * mysql> use naiveBayes;
     * mysql> create table trainingSet (S_NO integer primary key auto_increment, document text, category varchar(255));
     * mysql> create table wordFrequency (S_NO integer primary key auto_increment, word varchar(255), count integer, category varchar(255));
     */

    require_once('../mungkin_stemmed/NaiveBayesClassifier.php');

    $classifier = new NaiveBayesClassifier();
    $Positif = Category::$Positif;
    $Negatif = Category::$Negatif;
    $bukanBahasaIndonesia = Category::$bukanBahasaIndonesia;

    $category = $classifier -> classify('Mas kapan pean uplod maneng .');
    echo 'Mas kapan pean uplod maneng .  = '.$category."<br>";
?>